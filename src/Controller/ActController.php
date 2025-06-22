<?php

namespace App\Controller;

use App\Entity\Act;
use App\Entity\Challenge;
use App\Entity\Location;
use App\Repository\ChallengeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class ActController extends AbstractController
{
    private $logger;

    public function __construct(?\Psr\Log\LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    #[Route('/act/new', name: 'app_act_new', methods: ['POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        Security $security,
        ChallengeRepository $challengeRepository
    ): JsonResponse
    {
        // Récupération de l'utilisateur connecté
        $user = $security->getUser();
        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour publier un acte'
            ], 401);
        }

        // Récupération des données JSON
        $data = json_decode($request->getContent(), true);

        // Log pour debug
        $this->logger?->info('Données reçues pour création d\'acte', [
            'data' => $data,
            'user' => $user->getUserIdentifier()
        ]);

        // Vérification des données requises
        if (!isset($data['title']) || !isset($data['description']) || !isset($data['category'])) {
            return $this->json([
                'success' => false,
                'message' => 'Veuillez remplir tous les champs obligatoires'
            ], 400);
        }

        // Création de l'acte
        $act = new Act();
        $act->setTitle($data['title']);
        $act->setDescription($data['description']);
        $act->setCategory($data['category']);
        $act->setUser($user);
        
        // Gestion de l'image
        if (isset($data['imgUrl']) && $data['imgUrl']) {
            $act->setImgUrl($data['imgUrl']);
        }
        
        // Gestion du challenge
        if (isset($data['challenge']) && $data['challenge']) {
            // Si c'est un IRI (/api/challenges/1), extrait l'ID
            if (is_string($data['challenge']) && strpos($data['challenge'], '/api/challenges/') === 0) {
                $challengeId = (int) substr($data['challenge'], strrpos($data['challenge'], '/') + 1);
                $challenge = $challengeRepository->find($challengeId);
                
                if ($challenge) {
                    $act->setChallenge($challenge);
                }
            }
        }
        
        // Gestion de la localisation
        if (isset($data['location']) && $data['location']) {
            // Si c'est un IRI (/api/locations/1), on récupère l'entité
            if (is_string($data['location']) && strpos($data['location'], '/api/locations/') === 0) {
                $locationId = (int) substr($data['location'], strrpos($data['location'], '/') + 1);
                $location = $entityManager->getRepository(Location::class)->find($locationId);
                
                if ($location) {
                    $act->setLocation($location);
                }
            }
        }
        
        // Enregistrement en base de données
        $entityManager->persist($act);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Acte publié avec succès !',
            'id' => $act->getId()
        ], 201);
    }
}

<?php

// Déclaration du namespace pour organiser le code dans le dossier Controller
namespace App\Controller;

// Import des entités utilisées
use App\Entity\Act;
use App\Entity\Challenge;
use App\Entity\Location;

// Import du repository Challenge pour rechercher des challenges en base
use App\Repository\ChallengeRepository;

// Import de l'EntityManager pour gérer la persistance en base
use Doctrine\ORM\EntityManagerInterface;

// Import du contrôleur de base Symfony
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Import des classes pour gérer la requête HTTP et la réponse JSON
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

// Import pour définir des routes via annotations PHP 8
use Symfony\Component\Routing\Annotation\Route;

// Import du service Security pour récupérer l'utilisateur connecté
use Symfony\Bundle\SecurityBundle\Security;

class ActController extends AbstractController
{
    // Propriété pour logger les informations (optionnelle)
    private $logger;

    // Constructeur avec injection optionnelle d'un logger PSR-3
    public function __construct(?\Psr\Log\LoggerInterface $logger = null)
    {
        $this->logger = $logger; // Assignation du logger à la propriété privée
    }

    // Définition de la route /act/new, accessible uniquement en méthode POST, nommée app_act_new
    #[Route('/act/new', name: 'app_act_new', methods: ['POST'])]
    public function new(
        Request $request,                     // Objet Request représentant la requête HTTP
        EntityManagerInterface $entityManager, // EntityManager pour gérer la base de données
        Security $security,                   // Service Security pour récupérer l'utilisateur connecté
        ChallengeRepository $challengeRepository // Repository pour récupérer des challenges
    ): JsonResponse                           // Retourne une réponse JSON
    {
        // Récupère l'utilisateur connecté via le service Security
        $user = $security->getUser();

        // Si aucun utilisateur connecté, renvoie une réponse JSON avec erreur 401 (non autorisé)
        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour publier un acte'
            ], 401);
        }

        // Récupère le contenu JSON de la requête et le décode en tableau associatif
        $data = json_decode($request->getContent(), true);

        // Si un logger est défini, on log l'information pour débogage
        $this->logger?->info('Données reçues pour création d\'acte', [
            'data' => $data,
            'user' => $user->getUserIdentifier() // Identifiant utilisateur (ex: email ou pseudo)
        ]);

        // Vérifie que les champs obligatoires sont bien présents dans les données
        if (!isset($data['title']) || !isset($data['description']) || !isset($data['category'])) {
            // Si un champ obligatoire manque, retourne une erreur 400 (mauvaise requête)
            return $this->json([
                'success' => false,
                'message' => 'Veuillez remplir tous les champs obligatoires'
            ], 400);
        }

        // Création d'une nouvelle instance de l'entité Act
        $act = new Act();

        // Remplit les propriétés de l'acte avec les données reçues
        $act->setTitle($data['title']);
        $act->setDescription($data['description']);
        $act->setCategory($data['category']);
        $act->setUser($user); // Associe l'acte à l'utilisateur connecté

        // Gestion facultative de l'image si l'URL est fournie dans les données
        if (isset($data['imgUrl']) && $data['imgUrl']) {
            $act->setImgUrl($data['imgUrl']);
        }

        // Gestion facultative du challenge lié à l'acte
        if (isset($data['challenge']) && $data['challenge']) {
            // Si challenge est une chaîne commençant par '/api/challenges/', on extrait l'ID
            if (is_string($data['challenge']) && strpos($data['challenge'], '/api/challenges/') === 0) {
                // Extraction de l'ID à partir de l'IRI (ex: /api/challenges/1 => 1)
                $challengeId = (int) substr($data['challenge'], strrpos($data['challenge'], '/') + 1);

                // Recherche du challenge en base via le repository
                $challenge = $challengeRepository->find($challengeId);

                // Si le challenge existe, on l'associe à l'acte
                if ($challenge) {
                    $act->setChallenge($challenge);
                }
            }
        }

        // Gestion facultative de la localisation liée à l'acte
        if (isset($data['location']) && $data['location']) {
            // Si location est une chaîne commençant par '/api/locations/', on extrait l'ID
            if (is_string($data['location']) && strpos($data['location'], '/api/locations/') === 0) {
                // Extraction de l'ID à partir de l'IRI (ex: /api/locations/1 => 1)
                $locationId = (int) substr($data['location'], strrpos($data['location'], '/') + 1);

                // Recherche de la location en base via EntityManager
                $location = $entityManager->getRepository(Location::class)->find($locationId);

                // Si la location existe, on l'associe à l'acte
                if ($location) {
                    $act->setLocation($location);
                }
            }
        }

        // Indique à Doctrine qu'il faut persister (enregistrer) cette nouvelle entité en base
        $entityManager->persist($act);

        // Exécute la requête SQL pour enregistrer définitivement en base
        $entityManager->flush();

        // Retourne une réponse JSON positive avec l'ID du nouvel acte et code 201 (créé)
        return $this->json([
            'success' => true,
            'message' => 'Acte publié avec succès !',
            'id' => $act->getId()
        ], 201);
    }
}

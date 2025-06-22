<?php

namespace App\DataTransformer;

use App\Entity\Act;
use App\Entity\Challenge;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class MultipartActDataTransformer implements ProcessorInterface
{
    public function __construct(
        private readonly ProcessorInterface $decorated,
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Act
    {
        // Si $data est déjà un Act, passe-le au processor décoré
        if ($data instanceof Act) {
            return $this->decorated->process($data, $operation, $uriVariables, $context);
        }
        
        // Récupère la requête du contexte
        $request = $context['request'] ?? null;
        if (!$request instanceof Request) {
            throw new \InvalidArgumentException('La requête est manquante dans le contexte');
        }
        
        // Vérifie le type de contenu
        if (!str_contains($request->headers->get('Content-Type', ''), 'multipart/form-data')) {
            throw new \InvalidArgumentException('Format de requête non supporté, attendu: multipart/form-data');
        }
        
        // Vérifie l'authentification
        $userObject = $this->security->getUser();
        if (!$userObject) {
            throw new AccessDeniedException('Vous devez être connecté pour publier un acte');
        }
        if (!$userObject instanceof User) {
            throw new \RuntimeException('L\'utilisateur connecté n\'est pas du bon type');
        }
        
        // Récupère les données du formulaire
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $category = $request->request->get('category');
        
        // Valide les champs obligatoires
        if (!$title || !$description || !$category) {
            throw new BadRequestHttpException('Les champs title, description et category sont obligatoires');
        }
        
        // Crée un nouvel acte
        $act = new Act();
        $act->setTitle($title);
        $act->setDescription($description);
        $act->setCategory($category);
        $act->setCreatedAt(new \DateTimeImmutable());
        $act->setUser($userObject);
        
        // Ajoute le défi si présent
        $challengeIri = $request->request->get('challenge');
        if ($challengeIri) {
            $matches = [];
            if (preg_match('|/api/challenges/(\d+)|', $challengeIri, $matches)) {
                $challengeId = $matches[1];
                $challenge = $this->entityManager->getRepository(Challenge::class)->find($challengeId);
                if ($challenge) {
                    $act->setChallenge($challenge);
                }
            }
        }
        
        // Ajoute l'image si présente
        $imageFile = $request->files->get('imageFile');
        if ($imageFile) {
            $act->setImageFile($imageFile);
        }
        
        // Logs pour le débogage
        $contentType = $request->headers->get('Content-Type', '');
        error_log("Content-Type reçu: " . $contentType);
        error_log("Méthode: " . $request->getMethod());
        error_log("Données POST: " . json_encode($request->request->all()));
        error_log("Fichiers: " . json_encode(array_keys($request->files->all())));
        
        // Persiste l'acte
        $this->entityManager->persist($act);
        $this->entityManager->flush();
        
        // Vérifie que l'acte a bien été persisté
        if (!$act->getId()) {
            throw new \RuntimeException('L\'acte n\'a pas été correctement persisté en base de données');
        }
        
        return $act;
    }
}
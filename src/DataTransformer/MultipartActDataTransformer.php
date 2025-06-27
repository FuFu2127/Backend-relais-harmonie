<?php

// Déclaration du namespace correspondant au dossier de la classe
namespace App\DataTransformer;

// Import des entités manipulées
use App\Entity\Act;
use App\Entity\Challenge;
use App\Entity\User;

// Import des classes Symfony pour manipuler la requête HTTP et exceptions
use Symfony\Component\HttpFoundation\Request;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

// Import de Doctrine pour gérer la persistance
use Doctrine\ORM\EntityManagerInterface;

// Import du service Security pour accéder à l'utilisateur connecté
use Symfony\Bundle\SecurityBundle\Security;

// Déclaration de la classe finale qui implémente ProcessorInterface d'API Platform
final class MultipartActDataTransformer implements ProcessorInterface
{
    // Constructeur avec injection de dépendances via PHP 8 constructor property promotion
    public function __construct(
        private readonly ProcessorInterface $decorated,       // Le processor décoré auquel déléguer si besoin
        private readonly EntityManagerInterface $entityManager, // L'EntityManager pour gérer la base de données
        private readonly Security $security                      // Service Security pour accéder à l'utilisateur connecté
    ) {
    }

    /**
     * Transforme les données entrantes en instance d'Act
     * 
     * @param mixed $data Données brutes reçues
     * @param Operation $operation Opération API Platform en cours
     * @param array $uriVariables Variables d'URI (ex: id)
     * @param array $context Contexte d'exécution contenant notamment la requête HTTP
     * 
     * @return Act L'entité Act créée et persistée
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Act
    {
        // Si $data est déjà une instance d'Act, on délègue au processor décoré
        if ($data instanceof Act) {
            return $this->decorated->process($data, $operation, $uriVariables, $context);
        }
        
        // Récupération de la requête HTTP depuis le contexte
        $request = $context['request'] ?? null;
        // Vérifie que la requête est bien présente et valide
        if (!$request instanceof Request) {
            throw new \InvalidArgumentException('La requête est manquante dans le contexte');
        }
        
        // Vérifie que le Content-Type est bien multipart/form-data (pour upload fichiers)
        if (!str_contains($request->headers->get('Content-Type', ''), 'multipart/form-data')) {
            throw new \InvalidArgumentException('Format de requête non supporté, attendu: multipart/form-data');
        }
        
        // Récupération de l'utilisateur connecté via le service Security
        $userObject = $this->security->getUser();
        // Vérifie que l'utilisateur est bien connecté
        if (!$userObject) {
            throw new AccessDeniedException('Vous devez être connecté pour publier un acte');
        }
        // Vérifie que l'utilisateur est bien une instance de User
        if (!$userObject instanceof User) {
            throw new \RuntimeException('L\'utilisateur connecté n\'est pas du bon type');
        }
        
        // Récupération des données du formulaire multipart depuis la requête
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $category = $request->request->get('category');
        
        // Validation : vérifie que les champs obligatoires ne sont pas vides
        if (!$title || !$description || !$category) {
            throw new BadRequestHttpException('Les champs title, description et category sont obligatoires');
        }
        
        // Création d'une nouvelle instance d'Act
        $act = new Act();
        // Hydratation de l'acte avec les données reçues
        $act->setTitle($title);
        $act->setDescription($description);
        $act->setCategory($category);
        $act->setCreatedAt(new \DateTimeImmutable()); // Date de création à l'instant
        $act->setUser($userObject);                    // Association avec l'utilisateur connecté
        
        // Gestion optionnelle du défi associé (challenge)
        $challengeIri = $request->request->get('challenge');
        if ($challengeIri) {
            $matches = [];
            // Extrait l'ID du challenge dans l'IRI attendu (/api/challenges/{id})
            if (preg_match('|/api/challenges/(\d+)|', $challengeIri, $matches)) {
                $challengeId = $matches[1];
                // Recherche l'entité Challenge en base via Doctrine
                $challenge = $this->entityManager->getRepository(Challenge::class)->find($challengeId);
                // Si trouvé, associe le challenge à l'acte
                if ($challenge) {
                    $act->setChallenge($challenge);
                }
            }
        }
        
        // Gestion optionnelle du fichier image envoyé avec la requête
        $imageFile = $request->files->get('imageFile');
        if ($imageFile) {
            $act->setImageFile($imageFile);
        }
        
        // Logs pour débogage : affichage des infos reçues (dans error_log PHP)
        $contentType = $request->headers->get('Content-Type', '');
        error_log("Content-Type reçu: " . $contentType);
        error_log("Méthode: " . $request->getMethod());
        error_log("Données POST: " . json_encode($request->request->all()));
        error_log("Fichiers: " . json_encode(array_keys($request->files->all())));
        
        // Persistance de l'entité Act en base
        $this->entityManager->persist($act);
        $this->entityManager->flush();
        
        // Vérification que l'acte a bien été enregistré (possède un ID)
        if (!$act->getId()) {
            throw new \RuntimeException('L\'acte n\'a pas été correctement persisté en base de données');
        }
        
        // Retourne l'entité Act créée et persistée
        return $act;
    }
}
<?php

// Déclare le namespace du fichier, qui correspond généralement à son emplacement dans src/
namespace App\State;

// Importe les classes nécessaires depuis les composants Symfony et ApiPlatform
use ApiPlatform\Metadata\Operation; // Représente une opération API (ex: POST, PUT)
use ApiPlatform\State\ProcessorInterface; // Interface obligatoire pour tout processeur personnalisé
use App\Entity\Comment; // L'entité que ce processor va gérer
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface; // Sert à récupérer l'utilisateur actuellement connecté
use DateTimeImmutable; // Classe pour gérer une date/heure immuable (non modifiable après création)


// Déclaration de la classe CommentProcessor qui implémente ProcessorInterface
class CommentProcessor implements ProcessorInterface
{
    // Déclaration de deux propriétés privées
    private $decorated; // Va contenir le processeur "d'origine" d'API Platform (qu'on va décorer)
    private $tokenStorage; // Va contenir le service qui nous permet d'accéder à l'utilisateur connecté

    // Constructeur de la classe : il reçoit deux services par injection
    public function __construct(
        ProcessorInterface $decorated, // Le processeur original d'API Platform
        TokenStorageInterface $tokenStorage // Le service pour accéder à l'utilisateur connecté
    ) {
        // On stocke les services dans les propriétés pour les utiliser plus tard
        $this->decorated = $decorated;
        $this->tokenStorage = $tokenStorage;
    }

    // Méthode principale obligatoire issue de ProcessorInterface
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        // Vérifie si l'objet $data est un commentaire ET qu'il n'a pas encore d'ID (donc c'est un nouveau commentaire)
        if ($data instanceof Comment && !$data->getId()) {
            // Récupère le token d'authentification actuel (qui contient l'utilisateur)
            $token = $this->tokenStorage->getToken();

            // Si on a bien un token, on essaie de récupérer l'utilisateur depuis ce token
            $user = $token ? $token->getUser() : null;

            // Si un utilisateur est bien connecté
            if ($user) {
                // On associe cet utilisateur au commentaire
                $data->setUser($user);
            }

            // Si aucune date de création n'a été définie pour le commentaire
            if (!$data->getCreatedAt()) {
                // On définit la date de création à maintenant
                $data->setCreatedAt(new DateTimeImmutable());
            }
        }

        // On termine le traitement en laissant le processeur d'origine faire son travail
        // (enregistrement en base de données, autres logiques internes, etc.)
        return $this->decorated->process($data, $operation, $uriVariables, $context);
    }
}
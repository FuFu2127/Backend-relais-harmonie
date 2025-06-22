<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Comment;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use DateTimeImmutable;

class CommentProcessor implements ProcessorInterface
{
    private $decorated;
    private $tokenStorage;

    public function __construct(
        ProcessorInterface $decorated, 
        TokenStorageInterface $tokenStorage
    ) {
        $this->decorated = $decorated;
        $this->tokenStorage = $tokenStorage;
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        // Si c'est une création de commentaire
        if ($data instanceof Comment && !$data->getId()) {
            // Récupérer l'utilisateur depuis le token
            $token = $this->tokenStorage->getToken();
            $user = $token ? $token->getUser() : null;
            
            // Associer l'utilisateur actuel au commentaire si l'utilisateur existe
            if ($user) {
                $data->setUser($user);
            }
            
            // Définir la date de création
            if (!$data->getCreatedAt()) {
                $data->setCreatedAt(new DateTimeImmutable());
            }
        }

        // Laisser le processeur décoré terminer le traitement
        return $this->decorated->process($data, $operation, $uriVariables, $context);
    }
}
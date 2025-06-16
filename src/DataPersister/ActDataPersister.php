<?php
namespace App\DataPersister;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Act;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ActDataPersister implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'security.helper')] private Security $security,
        private ProcessorInterface $persistProcessor // Ajoute ceci
    ) {}

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $method = $context['request_method'] ?? null;
        if ($data instanceof Act && $method === 'POST') {
            $user = $this->security->getUser();
            if (!$user) {
                throw new AccessDeniedException('Vous devez être connecté pour publier un acte.');
            }
            $data->setUser($user);
        }

        dump('User in persister:', $user);
        // Appelle le vrai persister pour sauvegarder l'entité
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
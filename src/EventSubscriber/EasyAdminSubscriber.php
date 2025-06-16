<?php
// src/EventSubscriber/EasyAdminSubscriber.php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['hashPasswordPersist'],
            BeforeEntityUpdatedEvent::class => ['hashPasswordUpdate'],
        ];
    }

    public function hashPasswordPersist(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof User) {
            // Ne rien faire si ce n'est pas un User
            return;
        }

        if ($entity->getPassword() !== null) {
            $this->hashPassword($entity, $entity->getPassword());
        }
    }

    public function hashPasswordUpdate(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof User) {
            // Ne rien faire si ce n'est pas un User
            return;
        }

        if ($entity->getPassword() !== null) {
            $this->hashPassword($entity, $entity->getPassword());
        }
    }

    private function hashPassword(User $user, string $plaintextPassword): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
    }
}

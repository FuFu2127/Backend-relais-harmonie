<?php
// src/EventSubscriber/EasyAdminSubscriber.php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private EntityManagerInterface $em
    ) {}


    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['hashPasswordPersist'],
            BeforeEntityUpdatedEvent::class => ['hashPasswordUpdate']
        ];
    }

    public function hashPasswordPersist(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof User) {
            if ($entity->getPassword() !== null) {
                $this->hashPassword($entity, $entity->getPassword());
            }
        } else {
            $dbUser = $this->em->createQueryBuilder()
                    ->select('u')
                    ->from(User::class, 'u')
                    ->where('u.id = :id')
                    ->setParameter('id', $entity->getId())
                    ->getQuery()
                    ->getScalarResult();
                if ($dbUser[0]['u_password'] !== null) {
                    $entity->setPassword(trim($dbUser[0]['u_password']));
                }
        }
    }

    public function hashPasswordUpdate(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof User) {
            if ($entity->getPassword() !== null) {
                $this->hashPassword($entity, $entity->getPassword());
            } else {
                $dbUser = $this->em->createQueryBuilder()
                    ->select('u')
                    ->from(User::class, 'u')
                    ->where('u.id = :id')
                    ->setParameter('id', $entity->getId())
                    ->getQuery()
                    ->getScalarResult();
                if ($dbUser[0]['u_password'] !== null) {
                    $entity->setPassword(trim($dbUser[0]['u_password']));
                }
            }
        }
    }

    private function hashPassword(&$user, $plaintextPassword)
    {
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
    }
}

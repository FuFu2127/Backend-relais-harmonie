<?php
// src/EventSubscriber/EasyAdminSubscriber.php

// Le fichier contient un subscriber (écouteur d'événements) pour EasyAdmin

namespace App\EventSubscriber; // Déclare l'emplacement (namespace) de la classe

// On importe les classes nécessaires :
use App\Entity\User; // L'entité User qu'on veut gérer
use Symfony\Component\EventDispatcher\EventSubscriberInterface; // Interface obligatoire pour un subscriber
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent; // Événement déclenché juste avant la mise à jour d'une entité
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent; // Événement déclenché juste avant l'ajout (persist) d'une entité
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Service pour hasher les mots de passe

// On crée la classe EasyAdminSubscriber qui écoute des événements
class EasyAdminSubscriber implements EventSubscriberInterface 
{
    // Constructeur de la classe : ici, on injecte le service pour hasher les mots de passe
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher // On stocke le service dans une propriété privée
    ) {}

    // Méthode obligatoire pour déclarer quels événements on écoute
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['hashPasswordPersist'], // Quand une entité est ajoutée : appel de hashPasswordPersist()
            BeforeEntityUpdatedEvent::class => ['hashPasswordUpdate'],   // Quand une entité est mise à jour : appel de hashPasswordUpdate()
        ];
    }

    // Méthode appelée avant la création (persist) d'une entité
    public function hashPasswordPersist(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance(); // Récupère l'entité concernée par l'événement

        if (!$entity instanceof User) {
            return; // Si ce n'est pas un User, on ne fait rien
        }

        if ($entity->getPassword() !== null) { // Si un mot de passe est défini
            $this->hashPassword($entity, $entity->getPassword()); // On hashe le mot de passe
        }
    }

    // Méthode appelée avant la mise à jour d'une entité
    public function hashPasswordUpdate(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance(); // Récupère l'entité concernée

        if (!$entity instanceof User) {
            return; // Si ce n'est pas un User, on ne fait rien
        }

        if ($entity->getPassword() !== null) { // Si un mot de passe est défini
            $this->hashPassword($entity, $entity->getPassword()); // On hashe le mot de passe
        }
    }

    // Méthode privée pour hasher un mot de passe
    private function hashPassword(User $user, string $plaintextPassword): void
    {
        // Utilise le service pour générer le mot de passe hashé à partir du mot de passe en clair
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );

        // Remplace le mot de passe de l'utilisateur par le mot de passe hashé
        $user->setPassword($hashedPassword);
    }
}

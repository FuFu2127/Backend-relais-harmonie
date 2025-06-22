<?php

namespace App\EventListener;
use App\Entity\Act;
// Import de la classe LifecycleEventArgs qui contient des informations sur l'événement du cycle de vie
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Classe ActListener
 * 
 * Cette classe écoute les événements liés à l'entité Act et gère la logique de progression des défis associés.
 * Elle est appelée automatiquement par Doctrine après qu'un acte a été créé en base de données.
 */
class ActListener
{
    /**
     * Méthode appelée automatiquement APRÈS qu'un nouvel acte a été persisté (créé) en base de données
     * 
     * @param Act $act - L'entité Act qui vient d'être persistée
     * @param LifecycleEventArgs $args - Objet contenant le contexte de l'événement
     * @return void - Cette méthode ne retourne aucune valeur
     */
    public function postPersist(Act $act, LifecycleEventArgs $args): void 
    {
        // Récupère le défi (challenge) associé à l'acte, s'il y en a un
        $challenge = $act->getChallenge();
        
        // Vérifie si un défi est associé à cet acte
        if ($challenge) {
            // Récupère la valeur actuelle de progression du défi
            $progress = $challenge->getProgress();
            
            // Incrémente la progression du défi de 1 (chaque acte compte pour une unité de progression)
            $challenge->setProgress($progress + 1);

            // Récupère l'EntityManager à partir des arguments de l'événement
            // L'EntityManager est responsable de la persistance des objets en base de données
            $em = $args->getObjectManager();
            
            // Indique à l'EntityManager que l'entité Challenge a été modifiée et doit être sauvegardée
            $em->persist($challenge);
            
            // Exécute la requête SQL pour mettre à jour la base de données avec les modifications
            $em->flush();
        }
    }
}

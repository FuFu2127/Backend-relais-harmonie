<?php

namespace App\EventListener;

use App\Entity\Act;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ActListener
{
    public function postPersist(Act $act, LifecycleEventArgs $args): void
    {
        $challenge = $act->getChallenge();
        if ($challenge) {
            $progress = $challenge->getProgress();
            $challenge->setProgress($progress + 1);

            $em = $args->getObjectManager();
            $em->persist($challenge);
            $em->flush();
        }
    }
}

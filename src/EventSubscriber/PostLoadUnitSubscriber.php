<?php

namespace App\EventSubscriber;

use App\Entity\AdministrativeUnit;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PostLoadUnitSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            Events::postLoad => 'postLoad',
        ];
    }

    public function postLoad(PostLoadEventArgs $event): void
    {
        $unit = $event->getObject();
        if (!$unit instanceof AdministrativeUnit) {
            return;
        }


    }
}

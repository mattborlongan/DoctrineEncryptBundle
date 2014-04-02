<?php

namespace TDM\DoctrineEncryptBundle\Subscribers\ORM;

use TDM\DoctrineEncryptBundle\Subscribers\AbstractORMDoctrineEncryptSubscriber;
use Doctrine\ORM\Events;

/**
 * Description of ORMEncrypt
 *
 * @author wpigott
 */
class ORMEncrypt extends AbstractORMDoctrineEncryptSubscriber {

    /**
     * Realization of EventSubscriber interface method.
     * @return Array Return all events which this subscriber is listening
     */
    public function getSubscribedEvents() {
        return array(
            Events::prePersist,
            Events::preUpdate,
        );
    }

}

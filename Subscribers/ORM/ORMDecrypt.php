<?php

namespace TDM\DoctrineEncryptBundle\Subscribers\ORM;

use TDM\DoctrineEncryptBundle\Subscribers\AbstractORMDoctrineEncryptSubscriber;
use Doctrine\ORM\Events;

/**
 * Description of ORMDecrypt
 *
 * @author wpigott
 */
class ORMDecrypt extends AbstractORMDoctrineEncryptSubscriber {

    /**
     * Realization of EventSubscriber interface method.
     * @return Array Return all events which this subscriber is listening
     */
    public function getSubscribedEvents() {
        return array(
            Events::postLoad,
        );
    }

}

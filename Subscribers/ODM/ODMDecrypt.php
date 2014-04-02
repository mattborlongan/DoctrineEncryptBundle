<?php

namespace TDM\DoctrineEncryptBundle\Subscribers\ODM;

use TDM\DoctrineEncryptBundle\Subscribers\AbstractODMDoctrineEncryptSubscriber;
use Doctrine\ODM\MongoDB\Events;

/**
 * Description of ODMDecrypt
 *
 * @author wpigott
 */
class ODMDecrypt extends AbstractODMDoctrineEncryptSubscriber {

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

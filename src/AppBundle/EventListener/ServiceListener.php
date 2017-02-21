<?php

namespace AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

use AppBundle\Entity\Training;

class ServiceListener
{
     private $vichservice;

     public function __construct($vichservice) {
         $this->vichservice = $vichservice;
     }

     public function postLoad(LifecycleEventArgs $args){
         $entity = $args->getEntity();
         if(method_exists($entity, 'setVichService')) {
             $entity->setVichService($this->vichservice);
         }
     }
}

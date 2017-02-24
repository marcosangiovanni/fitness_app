<?php
namespace AppBundle\EventListener;
use Doctrine\ORM\Event\LifecycleEventArgs;

use AppBundle\Entity\Log;

use CrEOF\Spatial\PHP\Types\Geometry\Point;

class LogListener
{
	//Before log save set fields
	public function prePersist(Log $log, LifecycleEventArgs $args){
        //Set position
        $log->setPosition(new Point($log->getLat(),$log->getLng()));
        //Set date obj
        $log->setDateObj(new \DateTime($log->getDate()));
		
		//get entity manager		
		$em = $args->getEntityManager();
		
		if(count($log->getSports()) > 0){
			$sports = $em->getRepository('AppBundle:Sport')->findBy(array('id' => $log->getSports()));
	
			$sports_obj = array();
			foreach ($sports as $sport) {
				$sports_obj[] = $sport->getTitle();
			}
			
			if(count($sports_obj) > 0){
				$log->setSportsObj(implode($sports_obj,','));
			}
		}
		
    }
	
}
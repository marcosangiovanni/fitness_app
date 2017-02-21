<?php
namespace AppBundle\EventListener;
use Doctrine\ORM\Event\LifecycleEventArgs;

use AppBundle\Entity\Training;

class TrainingListener
{
    //After training creation
	public function postPersist(Training $training, LifecycleEventArgs $args){
    	
		$em = $args->getEntityManager();
		$user = $training->getUser();

		//Get distinct of sport with count
		$query_builder = $em->getRepository('AppBundle:Training')->createQueryBuilder('t');
		
		$trainer_sport_label_number = $em->getRepository('AppBundle:Config')->findOneByCode('trainer_sport_label_number');
		
		$query = $query_builder	
						->select('s.id,count(t.id) as max_sport')
						->innerJoin('t.sport', 's')
						->andWhere('t.user_id = :user_id')
						->groupBy('s.id')
						->orderBy('max_sport','DESC')
						->setParameter('user_id', $user->getId())
						->setMaxResults($trainer_sport_label_number->getValue())
		;
		
		//Remove all trained sports from association
		$user->getSportsTrained()->clear();
		
		foreach ($query_builder->getQuery()->getResult() as $sport_count) {
			$sport_ids[] = $sport_count['id'];
		}
		
		$sports = $em->getRepository('AppBundle:Sport')->findById($sport_ids);
		
		foreach ($sports as $sport) {
			$user->addSportTrained($sport);
		}
		
		$em->persist($user);
		$em->flush();
		
    }

}
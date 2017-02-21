<?php
namespace AppBundle\EventListener;
use Doctrine\ORM\Event\LifecycleEventArgs;

use AppBundle\Entity\Subscribed;

class SubscribedListener
{
	//After subscribed update of user feedback
	public function postUpdate(Subscribed $subscribed, LifecycleEventArgs $args){
        //User that vote
        $user = $subscribed->getUser();
        //Voted training
        $training = $subscribed->getTraining();
		//Training Creator
		$creator = $training->getUser();

		//get entity manager		
		$em = $args->getEntityManager();
		//Get media average for user
		$query_builder = $em->getRepository('AppBundle:Training')->createQueryBuilder('t');
		
		$query_builder	->select('t.user_id,count(t.id) as vote_number,AVG(s.feedback) as vote_average')
						->innerJoin('t.subscribed', 's')
						->andWhere('t.user_id = :user_id')
						->andWhere('s.feedback IS NOT NULL')
						->groupBy('t.user_id')
						->setParameter('user_id', $creator->getId())
		;
		
		$ar_avg_vote = $query_builder->getQuery()->getResult();
		
		$creator->setFeedbackAvg($ar_avg_vote[0]['vote_average']);
		$creator->setFeedbackNum($ar_avg_vote[0]['vote_number']);
		
		$em->persist($creator);
		$em->flush();
		
    }
	
}
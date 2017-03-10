<?php
namespace AppBundle\EventListener;
use Doctrine\ORM\Event\LifecycleEventArgs;

use AppBundle\Entity\FacebookFriend;
use AppBundle\Entity\User\User;

class FacebookFriendListener
{
	//After facebook friend insert update friends relation
	public function postPersist(FacebookFriend $facebook_friend, LifecycleEventArgs $args){
		//get entity manager		
		$em = $args->getEntityManager();
		$user = $em->getRepository('AppBundle:User\User')->findOneBy(array('facebookUid' => $facebook_friend->getFacebookUid()));
		if($user){
			if (!$facebook_friend->getUser()->getMyFriends()->contains($user)) {
				$facebook_friend->getUser()->addMyFriend($user);
				$em->persist($facebook_friend->getUser());
				$em->flush();
			}
		}
    }
	
}
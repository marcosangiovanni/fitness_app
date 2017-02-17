<?php

namespace AppBundle\Controller;
use FOS\RestBundle\Controller\FOSRestController;

//Http
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//Manager
use AppBundle\Util\ErrorManager;
use AppBundle\Util\SerializerManager;

//Exception
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FacebookFriendsController extends FOSRestController
{
    public function getFacebook_friendsAction($user_id){
		try{
			//Get $fb_friends list
			$fb_friends = $this->getDoctrine()->getRepository('AppBundle:FacebookFriend')->findAll();
			$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($fb_friends));
			$jsonResponse->setStatusCode(200);
		}
		//500
		catch(\Exception $e){
			$jsonResponse = new Response(SerializerManager::getJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(500);
		}
		/* JSON RESPONSE */
		return $jsonResponse;
    } 

	public function putFacebook_friendsAction($user_id){
		
		try{
			//Get the user to update
			$user = $this->getDoctrine()->getRepository('AppBundle:User\User')->find($user_id);
			
			if(!$user){
				throw $this->createNotFoundException('No user found for id : '.$user_id);
			}else{
				//Remove all friends from association
				$user->getFriends()->clear();
				//get all friends from api call in request content
				$fb_friends_objects = SerializerManager::getObjectFromJsonData($this->getRequest()->getContent(), 'ArrayCollection<AppBundle\Entity\FacebookFriend>');
				
				foreach ($fb_friends_objects as $fb_friends) {
					$user->addFriend($fb_friends);
				}
				
				//Merging received data in entity
				$em = $this->getDoctrine()->getManager();
				$em->persist($user);
			    $em->flush();
		
				/* SERIALIZATION */
				$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($user));
			}
		
		}
		//User and password already in use
    	catch(NotFoundHttpException $e){
    		$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(404);
    	}
		//500
		catch(\Exception $e){
			$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(500);
		}
		
		/* JSON RESPONSE */
		return $jsonResponse;
		
	}
		
}
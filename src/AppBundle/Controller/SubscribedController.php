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
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

//Entities
use AppBundle\Entity\Subscribed;

class SubscribedController extends FOSRestController
{
    public function getSubscribedAction($user_id){
		try{
			//Get the $user to update
			$user = $this->getDoctrine()->getRepository('AppBundle:User\User')->find($user_id);
			if(!$user){
				throw $this->createNotFoundException('No user found for id : '.$user_id);
			}else{
				//Get subscribed list
				$subscription = $this->getDoctrine()->getRepository('AppBundle:Subscribed')->findBy(array('user_id' => $user_id));
				$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($subscription));
				$jsonResponse->setStatusCode(200);
			}
		}
		//404
    	catch(NotFoundHttpException $e){
    		$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(404);
    	}
		//500
		catch(\Exception $e){
			$jsonResponse = new Response(SerializerManager::getJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(500);
		}
		/* JSON RESPONSE */
		return $jsonResponse;
    } 


	//This metod represent user subscription to a specific training (training is passed in body via his ID)
	public function putTrainingSubscribedAction($user_id,$training_id){
		
		try{
			//Get the user to update
			$user = $this->getDoctrine()->getRepository('AppBundle:User\User')->find($user_id);
			
			if(!$user){
				throw $this->createNotFoundException('No user found for id : '.$user_id);
			}else{

				$training = $this->getDoctrine()->getRepository('AppBundle:Training')->find($training_id);
				
				if(!$training){
					throw $this->createNotFoundException('No training found for id : '.$training_id);
				}else{
					//Create new subscription
					$subscription = new Subscribed();
					$subscription->setTraining($training);
					$subscription->setUser($user);
					
					//Add new subscription
					$user->addSubscribed($subscription);
	
					//Save user
					$em = $this->getDoctrine()->getManager();
					$em->persist($user);
				    $em->flush();
			
					/* SERIALIZATION */
					$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($user));
				}
			}
		
		}
		//User and password already in use
    	catch(NotFoundHttpException $e){
    		$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(404);
    	}
		//User and password already in use
    	catch(UniqueConstraintViolationException $e){
    		$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(409);
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
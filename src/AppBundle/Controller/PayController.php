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
//use AppBundle\Entity\Subscribed;

class PayController extends FOSRestController
{

	//This metod represent user subscription to a specific training (training is passed in body via his ID)
	public function postTrainingPayAction($user_id,$training_id){
		
		try{
			//Get the user
			$user = $this->getDoctrine()->getRepository('AppBundle:User\User')->find($user_id);
			if(!$user){
				throw $this->createNotFoundException('No user found for id : '.$user_id);
			}else{

				//Get the training
				$training = $this->getDoctrine()->getRepository('AppBundle:Training')->find($training_id);
				if(!$training){
					throw $this->createNotFoundException('No training found for id : '.$training_id);
				}else{

					//Set api key 
					$this->container->get('wmc_stripe.stripe')->auth();

					//TODO : maybe better create an object and use it to unserialize					
					// Token is created from app and passed in request body
					$json_data = json_decode($this->getRequest()->getContent(),true);
					
					//If i dont have a token 
					//Customer create 
					if(!$user->getStripeToken()){

						if(!isset($json_data['token'])){
							throw new \Exception('Missing token',409);
						}
						$token = $json_data['token'];
						
						// Create a Customer:
						$stripe_customer = \Stripe\Customer::create(array(
						  "email" 		=> $user->getEmail(),
						  "source" 		=> $token,
						  "description" => $user->getFullname()
						));

						// Charge Payment
						$charge = \Stripe\Charge::create(array(
						  "amount" 		=> $training->getPrice()*100,
						  "currency" 	=> "eur",
						  "customer" 	=> $stripe_customer->id
						));

						//Get entity manager
						$em = $this->getDoctrine()->getManager();
						
						$user->setStripeToken($stripe_customer->id);
						
						$em->persist($user);
						$em->flush();
						
					}else{
						//Se ho il token aggiorno i dati del customer
						if(isset($json_data['token'])){
							//Get stripe user and save new data with token
							$cu = \Stripe\Customer::retrieve($user->getStripeToken());
							$cu->source = $json_data['token'];
							$cu->save();
						}

						$charge = \Stripe\Charge::create(array(
						  "amount" 		=> $training->getPrice()*100,
						  "currency" 	=> "eur",
						  "customer" 	=> $user->getStripeToken()
						));
						
					}
					//If payment is ok forward to subsscription add to training
					$response = $this->forward('AppBundle:Subscribed:putTrainingSubscribed', array(
				        'user_id'  		=> $user_id,
				        'training_id' 	=> $training_id
				    ));
					return $response;
					
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
			$jsonResponse->setStatusCode($e->getCode() ? $e->getCode() : 500);
		}
		
		/* JSON RESPONSE */
		return $jsonResponse;
		
	}	

}
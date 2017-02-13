<?php

namespace AppBundle\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Sport;
use AppBundle\Form\SportType;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Util\ErrorManager;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SportsController extends FOSRestController
{

	// [GET] /sports/{id}
	public function getSportAction($id){
		try{

			//Find sport entity by ID
			$sport = $this->getDoctrine()->getRepository('AppBundle:Sport')->find($id);

			if(!$sport){
				throw $this->createNotFoundException('No sport found for id : '.$id);
			}else{
				/* SERIALIZATION */
				$context = SerializationContext::create()->setGroups(array('detail'))->enableMaxDepthChecks();
				$serializer = SerializerBuilder::create()->build();
				$jsonContent = $serializer->serialize(array('data' => $sport), 'json', $context);
				
				/* JSON RESPONSE */
				$jsonResponse = new Response($jsonContent);
				return $jsonResponse->setStatusCode(200);
			}	

		}
		catch(NotFoundHttpException $e){
			/* SERIALIZATION */
			$jsonContent = SerializerBuilder::create()->build()->serialize(ErrorManager::createErrorArrayFromException($e), 'json');
				
			/* JSON RESPONSE */
			$jsonResponse = new Response($jsonContent);
			return $jsonResponse->setStatusCode(404);
		}
		catch(\Exception $e){
			/* SERIALIZATION */
			$jsonContent = SerializerBuilder::create()->build()->serialize(ErrorManager::createErrorArrayFromException($e), 'json');
				
			/* JSON RESPONSE */
			$jsonResponse = new Response($jsonContent);
			return $jsonResponse->setStatusCode(500);
		}
		
		
		
    } 
    
	// [GET] /sports
    public function getSportsAction(){
    	try{
    		
			//Get sport list
			$sports = $this->getDoctrine()->getRepository('AppBundle:Sport')->findAll();
			
			if(count($sports) === 0){
				throw $this->createNotFoundException('No sport found');
			}else{

				/* SERIALIZATION */
				$context = SerializationContext::create()->setGroups(array('detail'))->enableMaxDepthChecks();
				$serializer = SerializerBuilder::create()->build();
				$jsonContent = $serializer->serialize(array('data' => $sports), 'json', $context);
				
				/* JSON RESPONSE */
				$jsonResponse = new Response($jsonContent);
				return $jsonResponse->setStatusCode(200);
			
			}

		}
		catch(\NotFoundHttpException $e){
			/* SERIALIZATION */
			$jsonContent = SerializerBuilder::create()->build()->serialize(ErrorManager::createErrorArrayFromException($e), 'json');
				
			/* JSON RESPONSE */
			$jsonResponse = new Response($jsonContent);
			return $jsonResponse->setStatusCode(404);
		}
		catch(\Exception $e){
			/* SERIALIZATION */
			$jsonContent = SerializerBuilder::create()->build()->serialize(ErrorManager::createErrorArrayFromException($e), 'json');
				
			/* JSON RESPONSE */
			$jsonResponse = new Response($jsonContent);
			return $jsonResponse->setStatusCode(404);
		}
		
    }

}
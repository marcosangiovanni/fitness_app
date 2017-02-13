<?php

namespace AppBundle\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Sport;
use AppBundle\Form\SportType;

use Symfony\Component\HttpFoundation\Response;

use AppBundle\Util\ErrorManager;
use AppBundle\Util\SerializerManager;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SportsController extends FOSRestController
{

	// [GET] /sports/{id}
	public function getSportAction($id){
		try{
			//Find sport entity by ID
			$sport = $this->getDoctrine()->getRepository('AppBundle:Sport')->find($id);
			//If sport not found 404 exception
			if(!$sport){
				throw $this->createNotFoundException('No sport found for id : '.$id);
			}else{
				$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($sport));
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
			$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(500);
		}
		/* JSON RESPONSE */
		return $jsonResponse;
    } 
    
	// [GET] /sports
    public function getSportsAction(){
    	try{
			//Get sport list
			$sports = $this->getDoctrine()->getRepository('AppBundle:Sport')->findAll();
			//If none sport found 404 exception
			if(count($sports) === 0){
				throw $this->createNotFoundException('No sport found');
			}else{
				$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($sports));
				$jsonResponse->setStatusCode(200);
			}
		}
		//404
		catch(NotFoundHttpException $e){
			$jsonResponse = new Response(SerializerManager::getJsonData(ErrorManager::createErrorArrayFromException($e)));
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

}
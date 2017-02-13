<?php

//Controller
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
			$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($sports));
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

}
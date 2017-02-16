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

class ConfigsController extends FOSRestController
{

	// [GET] /config
    public function getConfigsAction(){
    	try{
    		//Get current user
    		$user = $this->container->get('security.token_storage')->getToken()->getUser();
			//Get config list
			$configs = $this->getDoctrine()->getRepository('AppBundle:Config')->findAll();
			//Return data
			$ar_return_config = array();

			//Passing the user ID for further calls
			$ar_return_config['user_id'] = $user->getId();
			
			//Loop the DB config variables
			foreach ($configs as $config) {
				$ar_return_config[$config->getCode()] = $config->getValueForExport($user);
			}
			
			$jsonResponse = new Response(SerializerManager::getJsonData($ar_return_config));
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
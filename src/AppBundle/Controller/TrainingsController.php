<?php

namespace AppBundle\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use AppBundle\Entity\Sport;
use CrEOF\Spatial\PHP\Types\Geometry\Point;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class TrainingsController extends FOSRestController
{

	// [GET] /trainings/{id}
	public function getTrainingAction($id)
    {
		//Find training data
		$training = $this->getDoctrine()->getRepository('AppBundle:Training')->find($id);

		if(!$training){
			throw $this->createNotFoundException('No training found for id '.$id);
		}else{
			
			$context = SerializationContext::create()
							->setGroups(array('detail'))
							->enableMaxDepthChecks();
			
			$serializer = SerializerBuilder::create()->build();
			
			$jsonContent = $serializer->serialize($training, 'json', $context);
			
			$jsonResponse = new Response($jsonContent);
    		return $jsonResponse->setStatusCode(200);

		}

    } 
    
	// [GET] /trainings
	// Set search parameters
    public function getTrainingsAction(){

		//Find USER By Token
    	$logged_user = $this->get('security.context')->getToken()->getUser();
		
		//Find request parameters
		$request = $this->getRequest();

		/* POSITION */		
		//The user starting position to search for trainings
		$x = $request->get('x');
		$y = $request->get('y');
		$point = new Point($x,$y);
		//The max distance in meters of training
		$max_distance = $request->get('distance');

		/* SPORT TYPE */
		//The sports I intend to search within
		$sports = $request->get('sports');

		/* TRAINING DATETIME */
		//The date of training
		//0 = today
		//1 = tomorrow
		//etc...
		$date = $request->get('date');

		/* QUERY CONSTRUCTOR */
		//Instantiate the repositiory		
		$repository = $this->getDoctrine()->getRepository('AppBundle:Training');
		
		/* ADDING PARAMETER */
		$repository->findByNotClosedTrainings()
					->findBySports($sports)
					->findByDate($date)
					->findByPublic($logged_user)
					->findByPositionAndDistance($point,$max_distance)
		;
		
		$trainings = $repository->getQueryBuilder()->getQuery()->getResult();
		
		/* SERIALIZATION */
		$context = SerializationContext::create()->setGroups(array('detail'))->enableMaxDepthChecks();
		$serializer = SerializerBuilder::create()->build();
		$jsonContent = $serializer->serialize($trainings, 'json', $context);
		
		/* JSON RESPONSE */
		$jsonResponse = new Response($jsonContent);
		return $jsonResponse->setStatusCode(200);
	
    }

	// "post_trainings"           
	// [POST] /trainings
    public function postTrainingsAction()
    {} 

	// "put_trainings"             
	// [PUT] /trainings/{id}
    public function putTrainingAction($id)
    {}

	// "delete_trainings"          
	// [DELETE] /trainings/{id}
    public function deleteTrainingAction($id)
    {}

}
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

use AppBundle\Util\ObjectMerger;

class SportsController extends FOSRestController
{

	// [GET] /sports/{id}
	public function getSportAction($id)
    {
    	//Find USER By Token
    	$logged_user = $this->get('security.context')->getToken()->getUser();
		
		//Check is granted
		$is_granted = $this->get('security.context')->isGranted('ROLE_USER');
		
		//Find sport data
		$sport = $this->getDoctrine()->getRepository('AppBundle:Sport')->find($id);
		if(!$sport){
			throw $this->createNotFoundException('No sport found for id '.$id);
		}else{
			$view = $this->view($sport, 200);
        	return $this->handleView($view);
		}
    } 
    
	// [GET] /sports
	// Set search parameters
    public function getSportsAction(){
        $sports = $this->getDoctrine()->getRepository('AppBundle:Sport')->findAll();
		if(!$sports){
			throw $this->createNotFoundException('No collection found');
		}else{
			$view = $this->view($sports, 200);
        	return $this->handleView($view);
		}
    }

	// "post_sports"           
	// [POST] /sports
    public function postSportsAction()
    {} 

	// "put_sports"             
	// [PUT] /sports/{id}
    public function putSportAction($id){
    	
		//Get the sport to update
		$sport = $this->getDoctrine()->getRepository('AppBundle:Sport')->find($id);
		
		//Request Object
		$request = $this->getRequest();
		
		//Serialization data (serializer and context)
		$context = SerializationContext::create()->setGroups(array('detail'))->enableMaxDepthChecks();
		$deserialization_context = DeserializationContext::create()->setGroups(array('detail'))->enableMaxDepthChecks();
		//Serializer and builder
		$builder = SerializerBuilder::create();
		$serializer = $builder->build();
		
		//Deserialized object with field conversion (see JMS Groups and SerializedName)
		$obj = $serializer->deserialize($request->getContent(), 'AppBundle\Entity\Sport', 'json', $deserialization_context);
		
		//Merging received data in entity
		$em = $this->getDoctrine()->getManager();
		$sport = ObjectMerger::mergeEntities($em, $sport, $obj);	

		/* PERSISTENCE */
		$em->persist($sport);
	    $em->flush();
		
		/* SERIALIZATION */
		$jsonContent = $serializer->serialize($sport, 'json', $context);
		
		/* JSON RESPONSE */
		$jsonResponse = new Response($jsonContent);
		return $jsonResponse->setStatusCode(200);
		
    }

	// "delete_sports"          
	// [DELETE] /sports/{id}
    public function deleteSportAction($id)
    {}

}
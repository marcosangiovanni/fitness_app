<?php

namespace AppBundle\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Sport;

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
    public function putSportAction($slug)
    {}

	// "delete_sports"          
	// [DELETE] /sports/{id}
    public function deleteSportAction($slug)
    {}

}
<?php

namespace AppBundle\Controller;
use FOS\RestBundle\Controller\FOSRestController;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use \DateTime;
use \Exception;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class UsersController extends FOSRestController
{

	// [GET] /users/{id}
	public function getUserAction($id){
		//Find user data
		$user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

		if(!$user){
			throw $this->createNotFoundException('No product found for id '.$id);
		}else{
			
			$context = SerializationContext::create()
							->setGroups(array('detail'))
							->enableMaxDepthChecks();
			
			$serializer = SerializerBuilder::create()->build();
			
			$jsonContent = $serializer->serialize($user, 'json', $context);
			
			$jsonResponse = new Response($jsonContent);
    		return $jsonResponse->setStatusCode(200);

		}
    } 
    
	// [GET] /users
	// Set search parameters
    public function getUsersAction(){
    	
		//Find request parameters
		$request = $this->getRequest();
		
		//If we want only training creator
		$is_trainer = $request->get('is_trainer');
		
		//If we want only user with active training created
		$is_active_trainer = $request->get('is_active_trainer');
		
		/* QUERY CONSTRUCTOR */
		//Instantiate the repositiory
		$repository = $this->getDoctrine()->getRepository('AppBundle:User');
		
		/* ADDING PARAMETER */
		if($is_trainer){
			$repository->findByCreatedTrainings();
		}elseif($is_active_trainer){
			$repository->findByActiveTrainings();
		}
		
		$users = $repository->getQueryBuilder()->getQuery()->getResult();

		/* SERIALIZATION */
		$context = SerializationContext::create()->setGroups(array('detail'))->enableMaxDepthChecks();
		$serializer = SerializerBuilder::create()->build();
		$jsonContent = $serializer->serialize($users, 'json', $context);

		/* JSON RESPONSE */
		$jsonResponse = new Response($jsonContent);
		return $jsonResponse->setStatusCode(200);
			
    }

	// "post_users"           
	// [POST] /users
	// New user insert
    public function postUsersAction(){
    	try{
    		//Get user manager
	    	$userManager = $this->container->get('fos_user.user_manager');

			//Creation of new user object
    		$user = $userManager->createUser();
			
			//Find request parameters
			$request = $this->getRequest();
			
			//User parameters for registration
			$data = $request->get('data');
			
			//A new user is alway created enabled
			$user->setEnabled(true);
			
			//Data passed in body
			$user->setName($data['name']);
			$user->setSurname($data['surname']);
			$user->setEmail($data['email']);
			$user->setPhone($data['phone']);
			$user->setDob(new DateTime($data['dob']));
			$user->setUsername($data['user']);
			$user->setPlainPassword($data['password']);
			
			//Save new user
			$userManager->updateUser($user);
			
			//Return user
			$view = $this->view($user, 200);
        	return $this->handleView($view);

    	}
    	//User and password already in use
    	catch(UniqueConstraintViolationException $e){
			throw new \Exception('user or email already in use');
    	}
		//This catch any other exception
		catch(Exception $e){
			throw new Exception('Something went wrong!');
    	}

    } 

	// "put_user"             
	// [PUT] /users/{id}
	// User update
    public function putUserAction($id){

    	//Find USER By Token
    	$logged_user = $this->get('security.context')->getToken()->getUser();
		
		if($logged_user->getId() != $id){
			throw new Exception('You cant modify this user');
		}
		
		
		
		var_dump($logged_user->getId());
		exit;
    	
    }

	// "delete_user"          
	// [DELETE] /users/{id}
    public function deleteUserAction($id)
    {}

}
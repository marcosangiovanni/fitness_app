<?php

namespace AppBundle\Controller;

use AppBundle\User\User;
use FOS\RestBundle\Controller\FOSRestController;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use \DateTime;
use \Exception;
use CrEOF\Spatial\PHP\Types\Geometry\Point;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerBuilder;

use AppBundle\Util\ObjectMerger;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class UsersController extends FOSRestController
{

	// [GET] /users/{id}
	public function getUserAction($id){
		//Find user data
		$user = $this->getDoctrine()->getRepository('AppBundle:User\User')->find($id);

		if(!$user){
			throw $this->createNotFoundException('No product found for id '.$id);
		}else{
			
			/* SERIALIZATION */
			$context = SerializationContext::create()->setGroups(array('detail'))->enableMaxDepthChecks();
			$serializer = SerializerBuilder::create()->build();
			$jsonContent = $serializer->serialize($user, 'json', $context);
			
			/* JSON RESPONSE */
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

		/* POSITION */		
		//The user starting position to search for trainings
		$x = $request->get('x');
		$y = $request->get('y');
		
		/* QUERY CONSTRUCTOR */
		//Instantiate the repositiory
		$repository = $this->getDoctrine()->getRepository('AppBundle:User\User');
		
		/* ADDING PARAMETER */
		if($is_trainer){
			$repository->findByCreatedTrainings();
		}elseif($is_active_trainer){
			$repository->findByActiveTrainings();
		}
		
		//If a starting point it's present the order is set by training position
		if($x && $y){
			$point = new Point($x,$y);
			$repository->orderByPosition($point);
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

		//Get the user to update
		$user = $this->getDoctrine()->getRepository('AppBundle:User\User')->find($id);
		
		//Request Object
		$request = $this->getRequest();
		
		//Serialization data (serializer and context)
		$context = SerializationContext::create()->setGroups(array('detail'))->enableMaxDepthChecks();
		$deserialization_context = DeserializationContext::create()->setGroups(array('detail'))->enableMaxDepthChecks();
		//Serializer and builder
		$builder = SerializerBuilder::create();
		$serializer = $builder->build();
		
		//Deserialized object with field conversion (see JMS Groups and SerializedName)
		$obj = $serializer->deserialize($request->getContent(), 'AppBundle\Entity\User\User', 'json', $deserialization_context);
		
		//Merging received data in entity
		$em = $this->getDoctrine()->getManager();
		$user = ObjectMerger::mergeEntities($em, $user, $obj);

		/* PERSISTENCE */
		$em->persist($user);
	    $em->flush();
		
		/* SERIALIZATION */
		$jsonContent = $serializer->serialize($user, 'json', $context);
		
		/* JSON RESPONSE */
		$jsonResponse = new Response($jsonContent);
		return $jsonResponse->setStatusCode(200);		
		
    }

	// "delete_user"          
	// [DELETE] /users/{id}
    public function deleteUserAction($id)
    {}

}
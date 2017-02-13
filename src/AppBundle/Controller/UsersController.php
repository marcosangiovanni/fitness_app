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

use JMS\Serializer\Construction\DoctrineObjectConstructor;

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
			$jsonContent = $serializer->serialize(array('data' => $user), 'json', $context);
			
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
		$lat = $request->get('lat');
		$lng = $request->get('lng');
		
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
		if($lat && $lng){
			$point = new Point($lat,$lng);
			$repository->orderByPosition($point);
		}
		
		$users = $repository->getQueryBuilder()->getQuery()->getResult();

		/* SERIALIZATION */
		$context = SerializationContext::create()->setGroups(array('detail'))->enableMaxDepthChecks();
		$serializer = SerializerBuilder::create()->build();
		$jsonContent = $serializer->serialize(array('data' => $users), 'json', $context);

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
			$user->setFirstname($data['firstname']);
			$user->setLastname($data['lastname']);
			$user->setEmail($data['email']);
			$user->setPhone($data['phone']);
			$user->setDob(new DateTime($data['date_of_birth']));
			$user->setUsername($data['username']);
			$user->setPlainPassword($data['password']);
			
			//Save new user
			$userManager->updateUser($user);
			
			/* SERIALIZATION */
			$context = SerializationContext::create()->setGroups(array('detail'))->enableMaxDepthChecks();
			$serializer = SerializerBuilder::create()->build();
			$jsonContent = $serializer->serialize(array('data' => $user), 'json', $context);
	
			/* JSON RESPONSE */
			$jsonResponse = new Response(array('data' => $jsonContent));
			return $jsonResponse->setStatusCode(200);

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
		//Setting object constructor to use DoctrineObjectConstructor for deserialize
		$builder->setObjectConstructor(
															new DoctrineObjectConstructor(
																			$this->container->get('doctrine'),
																			$this->container->get('jms_serializer.object_constructor')
															)
		);
		$serializer = $builder->build();

		//Deserialized object with field conversion (see JMS Groups and SerializedName)
		$obj = $serializer->deserialize($request->getContent(), 'AppBundle\Entity\User\User', 'json', $deserialization_context);

		//Merging received data in entity
		$em = $this->getDoctrine()->getManager();
		$em->persist($obj);
	    $em->flush();


		/* SERIALIZATION */
		$jsonContent = $serializer->serialize(array('data' => $user), 'json', $context);
		
		/* JSON RESPONSE */
		$jsonResponse = new Response($jsonContent);
		return $jsonResponse->setStatusCode(200);		
		
    }

	// "delete_user"          
	// [DELETE] /users/{id}
    public function deleteUserAction($id)
    {}

}
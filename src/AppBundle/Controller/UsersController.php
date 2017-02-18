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
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use \Exception;

//Date
use \DateTime;

//Spatial
use CrEOF\Spatial\PHP\Types\Geometry\Point;

class UsersController extends FOSRestController
{
	
	// [GET] /users/{id}
	public function getUserAction($id){
		try{
			//Find user entity by ID
			$user = $this->getDoctrine()->getRepository('AppBundle:User\User')->find($id);
			//If user not found 404 exception
			if(!$user){
				throw $this->createNotFoundException('No user found for id : '.$id);
			}else{
				$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($user));
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
	
	// [GET] /users
	// Set search parameters
    public function getUsersAction(){
		try{
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
			
			$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($users));
			$jsonResponse->setStatusCode(200);
		
		}
		//500
		catch(\Exception $e){
			$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(500);
		}

		return $jsonResponse;
			
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
			
			$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($user));
			$jsonResponse->setStatusCode(200);
    	}
    	//User and password already in use
    	catch(UniqueConstraintViolationException $e){
    		$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(409);
    	}
		//500
		catch(\Exception $e){
			$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(500);
		}
		
		return $jsonResponse;
    } 

	// "put_user"             
	// [PUT] /users/{id}
	// User update
    public function putUserAction($id){
		
		try{
			//Get the user to update
			$user = $this->getDoctrine()->getRepository('AppBundle:User\User')->find($id);
			
			if(!$user){
				throw $this->createNotFoundException('No user found for id : '.$id);
			}else{
				//Get user entity and deserialize in user object
				$user_entity = SerializerManager::getDoctrineObjectFromJsonDataWithContext($this->getRequest()->getContent(), 'AppBundle\Entity\User\User', $this->container);
				
				//Merging received data in entity
				$em = $this->getDoctrine()->getManager();
				$em->persist($user_entity);
			    $em->flush();
		
				/* SERIALIZATION */
				$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($user));
			}
		
		}
		//User and password already in use
    	catch(NotFoundHttpException $e){
    		$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(409);
    	}
		//500
		catch(\Exception $e){
			$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(500);
		}
		
		/* JSON RESPONSE */
		return $jsonResponse;
		
    }

	// "delete_user"          
	// [DELETE] /users/{id}
    public function deleteUserAction($id)
    {}
	
	
	 /*******************
	 * SPORT MANAGEMENT *
	 *******************/
	
	// "get_user_sports"    
	// [GET] /users/{slug}/sports
	public function getUserSportsAction($user_id){
		try{
			//Get the user to update
			$user = $this->getDoctrine()->getRepository('AppBundle:User\User')->find($user_id);
			
			if(!$user){
				throw $this->createNotFoundException('No user found for id : '.$user_id);
			}else{
				//Get user entity and deserialize in user object
				$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($user->getSports()));
				$jsonResponse->setStatusCode(200);
			}
		
		}
		//User and password already in use
    	catch(NotFoundHttpException $e){
    		$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(409);
    	}
		//500
		catch(\Exception $e){
			$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(500);
		}
		
		/* JSON RESPONSE */
		return $jsonResponse;
    }
	

	public function putUserSportsAction($user_id){
		
		try{
			//Get the user to update
			$user = $this->getDoctrine()->getRepository('AppBundle:User\User')->find($user_id);
			
			if(!$user){
				throw $this->createNotFoundException('No user found for id : '.$user_id);
			}else{
				//Remove all friends from association
				$user->getSports()->clear();
				//get all relation object from api call in request content
				$relation_objects = SerializerManager::getObjectFromJsonDataWithContext($this->getRequest()->getContent(), 'ArrayCollection<AppBundle\Entity\Sport>');

				//TODO : UP -> correct serializer to update without quering collection Sport 
				$ar = array();
				foreach ($relation_objects as $object) {
					$ar[] = $object->getId();
				}
				$sports = $this->getDoctrine()->getRepository('AppBundle:Sport')->findById($ar);

				//Adding sport to user				
				foreach ($sports as $sport) {
					$user->addSport($sport);
				}
				
				//Merging received data in entity
				$em = $this->getDoctrine()->getManager();
				$em->persist($user);
			    $em->flush();
		
				/* SERIALIZATION */
				$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($user));
			}
		
		}
		//User and password already in use
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
	
	

}
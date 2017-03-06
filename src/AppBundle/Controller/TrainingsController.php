<?php

//Controller
namespace AppBundle\Controller;
use FOS\RestBundle\Controller\FOSRestController;

//Spatial
use CrEOF\Spatial\PHP\Types\Geometry\Point;

//Http
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

//Manager
use AppBundle\Util\ErrorManager;
use AppBundle\Util\SerializerManager;

//Exception
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Entities
use AppBundle\Entity\Training;

class TrainingsController extends FOSRestController
{
	
	// [GET] /trainings/{id}
	public function getTrainingAction($id){
		try{
			//Find training entity by ID
			$training = $this->getDoctrine()->getRepository('AppBundle:Training')->find($id);
			//If training not found 404 exception
			if(!$training){
				throw $this->createNotFoundException('No training found for id : '.$id);
			}else{
				$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($training));
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
    
	// [GET] /trainings
	// Set search parameters
    public function getTrainingsAction(){
    	
		try{
			//Find USER By Token
	    	$logged_user = $this->get('security.context')->getToken()->getUser();

			//Log query
			$this->getDoctrine()->getRepository('AppBundle:Log')->createNewLog($logged_user, $this->getRequest());
			
			//Find request parameters
			$request = $this->getRequest();

			/* POSITION */		
			//The user starting position to search for trainings
			$lat = $request->get('lat');
			$lng = $request->get('lng');

			//The max distance in meters of training
			$max_distance = $request->get('distance');

			//Set user position
			if($lat && $lng && $max_distance){
				$logged_user->setPosition(new Point($lng,$lat));
				$logged_user->setDistance($max_distance);
				$em = $this->getDoctrine()->getManager();
				$em->persist($logged_user);
			    $em->flush();
			}

			/* SPORT TYPE */
			//The sports I intend to search within
			$sports = $request->get('sports');
	
			/* TRAINING DATETIME */
			//The date of training
			//0 = today
			//1 = tomorrow
			//etc...
			$date = $request->get('date');
			$date_operator = $request->get('date_op','eq');
			
			/* TRAINING MAXPRICE */
			$max_price = $request->get('max_price');
			
			/* LIMIT/OFFSET */		
			//The user starting position to search for trainings
			$limit = $request->get('limit');
			$offset = $request->get('offset');

			/* QUERY CONSTRUCTOR */
			//Instantiate the repositiory		
			$repository = $this->getDoctrine()->getRepository('AppBundle:Training');
			
			/* ADDING PARAMETER */
			$repository->findByNotClosedTrainings()
						->findBySports($sports)
						->findByDate($date,$date_operator)
						->findByEnabled(true)
						->findByMaxPrice($max_price)
						->findByPublic($logged_user)
						->findByPositionAndDistance($lng,$lat,$max_distance)
						->orderByPosition($lng,$lat)
						->setLimitOffset($limit,$offset)
			;
			
			$trainings = $repository->getQueryBuilder()->getQuery()->getResult();
			
			$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($trainings));
			$jsonResponse->setStatusCode(200);
		
		}
		//500
		catch(\Exception $e){
			$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(500);
		}
		
		return $jsonResponse;
	
    }

	// [GET] /trainings
	// Set search parameters
    public function getTrainingsdateAction(){

		try{
			//Find USER By Token
	    	$logged_user = $this->get('security.context')->getToken()->getUser();

			//Find request parameters
			$request = $this->getRequest();

			/* POSITION */		
			//The user starting position to search for trainings
			$lat = $request->get('lat');
			$lng = $request->get('lng');

			//The max distance in meters of training
			$max_distance = $request->get('distance');
	
			/* SPORT TYPE */
			//The sports I intend to search within
			$sports = $request->get('sports');
	
			/* TRAINING MAXPRICE */
			$max_price = $request->get('max_price');
			
			/* QUERY CONSTRUCTOR */
			//Instantiate the repositiory		
			$repository = $this->getDoctrine()->getRepository('AppBundle:Training');
			
			/* ADDING PARAMETER */
			$repository	->findByNotClosedTrainings()
						->findBySports($sports)
						->findByEnabled(true)
						->findByMaxPrice($max_price)
						->findByPublic($logged_user)
						->findByPositionAndDistance($lng,$lat,$max_distance)
			;
			
			$trainings = $repository->getQueryBuilder()
										->select('DISTINCT(date_format(t.start,\'%Y-%m-%d\')) as training_date')
										->getQuery()
										->getResult();
			
			
			$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($trainings));
			$jsonResponse->setStatusCode(200);
		
		}
		//500
		catch(\Exception $e){
			$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(500);
		}
		
		return $jsonResponse;
	
    }

	// "post_trainings"           
	// [POST] /trainings
    public function postTrainingsAction(){
    	try{

			//get all relation object from api call in request content
			$obj_training = SerializerManager::getObjectFromJsonDataWithContext($this->getRequest()->getContent(), 'AppBundle\Entity\Training');
			
			//New Trainings are enabled by default
			$obj_training->setEnabled(true);
			
			//Merging objects			
			$em = $this->getDoctrine()->getManager();
					$em->merge($obj_training);
				    $em->flush();
			
			/* SERIALIZATION */
			$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($obj_training));
		
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

	// "delete_trainings"          
	// [DELETE] /trainings/{id}
    public function deleteTrainingAction($id)
    {
    	try{
    		
			$training = $this->getDoctrine()->getRepository('AppBundle:Training')->find($id);
			
			//If training not found 404 exception
			if(!$training){
				throw $this->createNotFoundException('No training found for id : '.$id);
			}else{
				$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($training));
				$jsonResponse->setStatusCode(200);
			}	

			//Disable training (soft delete)
			$training->setEnabled(false);
			
			//Merging objects			
			$em = $this->getDoctrine()->getManager();
					$em->persist($training);
				    $em->flush();
			
			/* SERIALIZATION */
			$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($training));
		
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

	// "put_trainings"             
	// [PUT] /trainings/{id}
    public function putTrainingsAction($id){
    	try{
    		
			//Get the training to update
			$training = $this->getDoctrine()->getRepository('AppBundle:Training')->find($id);
			
			if(!$training){
				throw $this->createNotFoundException('No training found for id : '.$id);
			}else{
				//Get user entity and deserialize in user object
				$training_entity = SerializerManager::getDoctrineObjectFromJsonDataWithContext($this->getRequest()->getContent(), 'AppBundle\Entity\Training', $this->container);
				
				//Merging received data in entity
				$em = $this->getDoctrine()->getManager();
				$em->persist($training_entity);
			    $em->flush();
		
				/* SERIALIZATION */
				$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($training_entity));
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
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



			use sngrl\PhpFirebaseCloudMessaging\Client;
use sngrl\PhpFirebaseCloudMessaging\Message;
use sngrl\PhpFirebaseCloudMessaging\Recipient\Device;
use sngrl\PhpFirebaseCloudMessaging\Notification;


class PushController extends FOSRestController
{

	public function postPushAction(){
		try{
			
			$fcmClient = $this->container->get('redjan_ym_fcm.client');
			
			$notification = $fcmClient->createDeviceNotification(
		         'Title of Notification', 
		         'Body of Notification', 
		         'Firebase Token of the device who will recive the notification'
		    );
			
			$array = array('data1' => 'uegdsfuygjds', 'data2' => 'dsahjdgsajdgsadgsa');
			
			$notification->setData($array); 
			//and $notification->getData();
			// Excepts 2 priorities, high(default) and low
    		$notification->setPriority('high'); 
    		$notification->setIcon('name of icon located in the native mobile app');
			
			$notification->setTitle('Titolone');
		    $notification->setBody('Dolor sic amet Nescio, quam certcare faciat.');
		    $notification->setDeviceToken('gmWecHUSM8MXYWuR94ve4Fl0hA13');
			
			$response = $fcmClient->sendNotification($notification);
			
			$jsonResponse = new Response(SerializerManager::getJsonDataWithContext($response->getBody()->getContents()));
			$jsonResponse->setStatusCode(200);
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


}
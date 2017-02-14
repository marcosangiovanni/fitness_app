<?php
// TokenController.php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\OAuthServerBundle\Controller\TokenController as BaseTokenController;

use OAuth2\OAuth2ServerException;

//Manager
use AppBundle\Util\ErrorManager;
use AppBundle\Util\SerializerManager;

class TokenController extends BaseTokenController
{
   /**
    * @param Request $request
    * @param String $network
    * 
    * @return type
    */
	public function getSocialTokenAction(Request $request, $network){
		$server = $this->get('app_oauth_server.server');
        try {
            return $server->grantAccessToken($request, $network);
        }
        catch (OAuth2ServerException $e) {
        	$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(500);
        }
        catch (\Exception $e) {
        	$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(500);
        }
		return $jsonResponse;
    }
  
  	/**
     * @param Request $request
     *
     * @return Response
     */
    public function tokenAction(Request $request){
        try {
        	$obj_token = $this->server->grantAccessToken($request);
        	echo '{"data" : '.$obj_token->getContent().'}';
			return;
        }
        catch (OAuth2ServerException $e) {
        	$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(500);
        }
        catch (\Exception $e) {
        	$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($e)));
			$jsonResponse->setStatusCode(500);
        }
		return $jsonResponse;
    }

}
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
     *
     * @return Response
     */
    public function tokenAction(Request $request){
        try {
            return $this->server->grantAccessToken($request);
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
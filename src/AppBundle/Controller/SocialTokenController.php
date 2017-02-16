<?php
// TokenController.php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use OAuth2\OAuth2ServerException;

//Manager
use AppBundle\Util\ErrorManager;
use AppBundle\Util\SerializerManager;

class SocialTokenController extends Controller
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
}
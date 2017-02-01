<?php
// TokenController.php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use OAuth2\OAuth2ServerException;

class TokenController extends Controller
{
  /**
   * @param Request $request
   * @param String $network
   * 
   * @return type
   */
  public function getSocialTokenAction(Request $request, $network)
  {
    $server = $this->get('app_oauth_server.server');
    
    try {
        return $server->grantAccessToken($request, $network);
    } catch (OAuth2ServerException $e) {
        return $e->getHttpResponse();
    }
  }
}
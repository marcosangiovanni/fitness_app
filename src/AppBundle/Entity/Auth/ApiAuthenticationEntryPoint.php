<?php

namespace AppBundle\Entity\Auth;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

//Manager
use AppBundle\Util\ErrorManager;
use AppBundle\Util\SerializerManager;

class ApiAuthenticationEntryPoint implements AuthenticationEntryPointInterface {

    private $realmName;

    public function __construct($realmName) {
        $this->realmName = $realmName;
    }

    public function start(Request $request, AuthenticationException $authException = null) {
		$jsonResponse = new Response(SerializerManager::getErrorJsonData(ErrorManager::createErrorArrayFromException($authException)));
		$jsonResponse->setStatusCode(401);
		return $jsonResponse;
    }
	
}
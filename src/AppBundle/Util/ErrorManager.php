<?php

namespace AppBundle\Util;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ErrorManager{
	
	public function createErrorArrayFromException(\Exception $e){
		$code = null;
		if($e instanceof AuthenticationException){
			$code = 1000;
		}
		return array('code' => $code ? $code : $e->getCode(),'message' => $e->getMessage());
	}
	
}

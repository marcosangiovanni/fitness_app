<?php

namespace AppBundle\Util;

class ErrorManager{
	
	public function createErrorArrayFromException(\Exception $e){
		return array('error' => array('status_code' => $e->getStatusCode(),'message' => $e->getMessage()));
	}
	
}

<?php

namespace AppBundle\Util;

class ErrorManager{
	
	public function createErrorArrayFromException(\Exception $e){
		return array('code' => $e->getCode(),'message' => $e->getMessage());
	}
	
}

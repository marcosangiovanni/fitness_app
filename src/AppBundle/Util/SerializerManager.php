<?php

namespace AppBundle\Util;

class SerializerManager{
	
	public function createErrorArrayFromException(\Exception $e){
		return array('error' => array('status_code' => $e->getStatusCode(),'message' => $e->getMessage()));
	}
	
}

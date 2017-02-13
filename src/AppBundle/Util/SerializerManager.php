<?php

namespace AppBundle\Util;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerBuilder;

class SerializerManager{
	
	public function getJsonDataWithContext($entity_object,array $groups = array('detail'),ObjectConstructorInterface $objectConstructor = null){
		$context = SerializationContext::create()->setGroups($groups)->enableMaxDepthChecks();
		$builder = SerializerBuilder::create();
		if($objectConstructor){
			$builder->setObjectConstructor($objectConstructor);
		}
		$serializer = $builder->build();
		return $serializer->serialize(array('data' => $entity_object), 'json', $context);
	}
	
	public function getJsonData($entity_object){
		return SerializerBuilder::create()->build()->serialize(array('data' => $entity_object), 'json');
	}
	
	public function getErrorJsonData($error_array){
		return SerializerBuilder::create()->build()->serialize(array('error' => $error_array), 'json');
	}
	
}

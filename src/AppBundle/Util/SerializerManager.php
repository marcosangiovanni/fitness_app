<?php

namespace AppBundle\Util;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerBuilder;

//Object constructor
use JMS\Serializer\Construction\DoctrineObjectConstructor;

class SerializerManager{
	
	public function getJsonDataWithContext($entity_object,array $groups = array('detail')){
		$context = SerializationContext::create()->setGroups($groups)->enableMaxDepthChecks();
		return SerializerBuilder::create()->build()->serialize(array('data' => $entity_object), 'json', $context);
	}
	
	public function getObjectFromJsonDataWithContext($json, $entity_class, array $groups = array('detail'), $objectConstructor = null){
		$deserialization_context = DeserializationContext::create()->setGroups($groups)->enableMaxDepthChecks();
		$builder = SerializerBuilder::create();
		if($objectConstructor){
			$builder->setObjectConstructor($objectConstructor);
		}
		return $builder->build()->deserialize($json, $entity_class, 'json', $deserialization_context);
	}
	
	public function getObjectFromJsonData($json, $entity_class){
		return SerializerBuilder::create()->build()->deserialize($json, $entity_class, 'json');
	}
	
	public function getDoctrineObjectFromJsonDataWithContext($json, $entity_class,$container, array $groups = array('detail')){
		$doctrine_constructor = new DoctrineObjectConstructor($container->get('doctrine'),$container->get('jms_serializer.object_constructor'));
		return self::getObjectFromJsonDataWithContext($json, $entity_class, $groups, $doctrine_constructor);
	}
	
	public function getJsonData($entity_object){
		return SerializerBuilder::create()->build()->serialize(array('data' => $entity_object), 'json');
	}
	
	public function getErrorJsonData($error_array){
		return SerializerBuilder::create()->build()->serialize(array('error' => $error_array), 'json');
	}
	
}

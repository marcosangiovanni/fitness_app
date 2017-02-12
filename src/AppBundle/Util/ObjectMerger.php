<?php

namespace AppBundle\Util;
use Doctrine\Common\Collections\ArrayCollection;

class ObjectMerger{
	
	public function mergeEntities($em, $mainEntity, $subEntity){

		$className = get_class($mainEntity);
		$metadata = $em->getClassMetadata($className);
		
		//Field management
		foreach($metadata->fieldMappings as $k => $v){
			if($subEntity->__get($k) !== null){
				$mainEntity->__set($k,$subEntity->__get($k));
			}
		}
		
		
		//Relationship management
		foreach($metadata->associationMappings as $field_name => $field_description){

			$content = $subEntity->__get($field_name);
			if($content !== null){
				
				//Many to many or One to Many
				if($content instanceof ArrayCollection){

					$ids = array_column($content->toArray(), 'id');
					$collection = $em->getRepository($field_description['targetEntity'])->findById($ids);
					
					//Remove all relation
					//
					
					var_dump($field_name);
					
					if($field_name === 'subscribed'){
						$collection = $em->getRepository($field_description['targetEntity'])->findBy(array('user_id' => $mainEntity->getId()));
						
						foreach ($collection as $key => $item) {
								
							$mainEntity->removeSubscribed($item);
						}
						
						
					}else{
						$mainEntity->__set($field_name,new ArrayCollection());
					}
					
					
					
					//Add all relation received
					/*
					foreach ($collection as $key => $elem) {
						if($field_name === 'subscribed'){
							$mainEntity->addSubscribed($elem);
						}else{
							$mainEntity->add($field_name,$elem);	
						}
						
					}
					*/
				}
			}
		}

		return $mainEntity;
		
	}
	
}

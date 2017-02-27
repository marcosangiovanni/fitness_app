<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Log;
use Doctrine\Common\Util\Inflector as Inflector;

class LogRepository extends EntityRepository
{
	
	private $query_builder = null;
	
    public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class){
    	parent::__construct($em, $class);
    	$this->query_builder = $this->createQueryBuilder('l');
    }
	
    public function getQueryBuilder(){
    	return $this->query_builder;
    }
	
	//Create new log from request get parameters
    public function createNewLog(\AppBundle\Entity\User\User $logged_user, $request){
			
		$params = $request->query->all();

		$log = new Log();
		$log->setUser($logged_user);
		$log->setFullquery(json_encode($params));
		
		foreach ($params as $key => $value) {
			$method = "set".ucfirst(Inflector::camelize($key));
			if(method_exists($log,$method)){
				$log->$method($value);
			}
		}

		$em = $this->getEntityManager();
        $em->persist($log);
        $em->flush();
		
    }

}

?>
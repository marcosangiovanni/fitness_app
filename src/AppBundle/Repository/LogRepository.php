<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Log;

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
			$method = "set".preg_replace('/(^|_)([a-z])/e', 'strtoupper("\\2")', "_".$key);
			if(method_exists($log,$method)){
				$log->$method($value);
			}
		}
		
		//\Doctrine\Common\Util\Debug::dump($log);
		
		$em = $this->getEntityManager();
        $em->persist($log);
        $em->flush();
		
    }

}

?>
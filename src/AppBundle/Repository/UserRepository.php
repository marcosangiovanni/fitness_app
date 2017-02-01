<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use \DateTime;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use AppBundle\Entity\User;

class UserRepository extends EntityRepository
{
	
	private $query_builder = null;
	
    public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class){
    	parent::__construct($em, $class);
    	$this->query_builder = $this->createQueryBuilder('u');
    }
	
    public function getQueryBuilder(){
    	return $this->query_builder;
    }
	
	//Only user that created a training
    public function findByCreatedTrainings(){
        $this->query_builder->innerJoin('u.trainings', 't');
		return $this;
    }

	//Only user that created a training
    public function findByActiveTrainings(){
    	$now = new DateTime();
        $this->query_builder->innerJoin('u.trainings', 't', 'WITH', 't.cutoff > :current_datetime')->setParameter('current_datetime', $now->format('Y-m-d H:i:s'));;
		return $this;
    }

}

?>
<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use \DateTime;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use AppBundle\Entity\User\User;

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

	//Only user that created a training and that training is active
    public function findByActiveTrainings(){
    	$now = new DateTime();
        $this->query_builder->innerJoin('u.trainings', 't', 'WITH', 't.start > :current_datetime')->setParameter('current_datetime', $now->format('Y-m-d H:i:s'));;
		return $this;
    }
	
    public function findByFeedback($feedback){
    	$this->query_builder->andWhere('u.feedback_avg BETWEEN :start_feedback AND :end_feedback')
    								->setParameter('start_feedback', $feedback - 1)
    								->setParameter('end_feedback', $feedback);
		return $this;
    }
	
	//Only sports associated with user
    public function findByLinkedSports($sport_ids){
    	if($sport_ids){
	        $this->query_builder->innerJoin('u.sports_trained', 'st')->andWhere('st.id IN (:sports)')->setParameter('sports', $sport_ids);
    	}
		return $this;
    }
	
	//Set order by 
    public function orderByPosition(Point $point){
    	//If training is not already joined... JOIN
		if(!in_array('t',$this->query_builder->getAllAliases())){
			$this->query_builder->innerJoin('u.trainings', 't');
		}
        $this->query_builder->orderBy("st_distance_sphere(t.position,point(:x_position,:y_position))")
        			->setParameter('x_position', $point->getX())
					->setParameter('y_position', $point->getY())
		;
		return $this;
    }

}

?>
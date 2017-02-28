<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use \DateTime;
use \DateInterval;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use AppBundle\Entity\User\User;

class TrainingRepository extends EntityRepository
{
	
	private $query_builder = null;
	
    public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class){
    	parent::__construct($em, $class);
    	$this->query_builder = $this->createQueryBuilder('t');
    }
	
    public function getQueryBuilder(){
    	return $this->query_builder;
    }
	
	//Set pagination
    public function setLimitOffset($limit,$offset){
    	if($limit >= 0 && $offset >= 0){
	    	$this->query_builder->setFirstResult($offset)->setMaxResults($limit);
    	}
    }
	
	//Only training with price = $price -> set 0 for free training
    public function findByMaxPrice($max_price){
    	if($max_price !== NULL && $max_price >= 0){
	        $this->query_builder->andWhere('t.price <= :max_price')->setParameter('max_price', $max_price);
    	}
		return $this;
    }

	//Only training with start date > now
    public function findByNotClosedTrainings(){
    	$now = new DateTime();
        $this->query_builder->andWhere('t.start > :current_datetime')->setParameter('current_datetime', $now->format('Y-m-d H:i:s'));
		return $this;
    }
	
	//Add join with subscribed users
    public function findWithSubscribedUsers(){
        $this->query_builder
					->leftJoin('t.subscribed', 's')
					->leftJoin('s.user', 'u')
		;
		return $this;
    }

	//Only training that are starting
	//A training is starting if starts before than NOW + $time_missing
	//$time_missing is expressed in minutes
    public function findByStartingTrainings($time_missing){
    	$now = new DateTime();
		$endTime = clone $now;
		$endTime->add(new \DateInterval('PT'.$time_missing.'M'));
        $this->query_builder->andWhere('t.start < :ref_datetime')->setParameter('ref_datetime', $endTime->format('Y-m-d H:i:s'));
		return $this;
    }

	//Only training with enabled = true
    public function findByEnabled($is_enabled){
        $this->query_builder->andWhere('t.enabled = :is_enabled')->setParameter('is_enabled', $is_enabled);
		return $this;
    }

	//Only training not notified
    public function findByNotNotified(){
        $this->query_builder->andWhere('t.is_notified IS NULL');
		return $this;
    }

	//Only sports associated with user
    public function findBySports($sport_ids){
    	if($sport_ids){
	        $this->query_builder->andWhere('t.sport_id IN (:sports)')->setParameter('sports', $sport_ids);
    	}
		return $this;
    }

	//Only if date is selected
    public function findByDate($date,$date_operator){
    	if($date){
    		$date_start = new DateTime($date);
    		$date_end = new DateTime($date);
    		$date_end->add(new DateInterval('P1D'));
	        $this->query_builder->andWhere('t.start > :date_start')->setParameter('date_start', $date_start);
			//If date operator === gt I take all training that starts from that day
			if($date_operator === 'eq'){
		        $this->query_builder->andWhere('t.start < :date_end')->setParameter('date_end', $date_end);
			}
    	}
		return $this;
    }

	//Only public training or only friends of creator
    public function findByPublic(User $user){
		$this->query_builder	->leftJoin('t.user', 'u')
								->leftJoin('u.myFriends', 'f')
								->andWhere(
									$this->query_builder->expr()->orX(
										$this->query_builder->expr()->eq('f.id',':logged_user_id'),
										$this->query_builder->expr()->eq('t.is_public',':is_public')
								)
		)
		->setParameter('logged_user_id', $user->getId())
		->setParameter('is_public', true)
		;
		return $this;
    }

    public function findByPositionAndDistance($lat, $lng, $max_distance){
    	if($lat && $lng && $max_distance){
	        $this->query_builder->andWhere("st_distance_sphere(t.position,point(:x_position,:y_position)) < :max_distance")
	        			->setParameter('x_position', $lat)
						->setParameter('y_position', $lng)
						->setParameter('max_distance', $max_distance)
			;
    	}
		return $this;
    }

    public function orderByPosition($lat, $lng){
    	if($lat && $lng){
	        $this->query_builder->orderBy("st_distance_sphere(t.position,point(:x_position,:y_position))")
	        			->setParameter('x_position', $lat)
						->setParameter('y_position', $lng)
			;
		}
		return $this;
    }

}

?>
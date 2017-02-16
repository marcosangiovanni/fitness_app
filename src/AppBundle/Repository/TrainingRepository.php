<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use \DateTime;
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
	
	//Only training with cutoff date > now
    public function findByNotClosedTrainings(){
    	$now = new DateTime();
        $this->query_builder->andWhere('t.cutoff > :current_datetime')->setParameter('current_datetime', $now->format('Y-m-d H:i:s'));
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
    public function findByDate($date){
    	if($date){
	        $this->query_builder->andWhere('DATE_DIFF(t.start,CURRENT_DATE()) IN (:date)')->setParameter('date', $date);
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
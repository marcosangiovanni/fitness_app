<?php

namespace AppBundle\Repository;
use Cron\CronBundle\Entity\CronJobRepository as BaseCronJobRepository;
use Doctrine\ORM\EntityRepository;

class CronJobRepository extends BaseCronJobRepository
{
	
	private $query_builder = null;
	
    public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class){
    	parent::__construct($em, $class);
    	$this->query_builder = $this->createQueryBuilder('cj');
    }
	
	public function find($id, $lockMode = null, $lockVersion = null){
    	$this->query_builder->andWhere('cj.id = :id')->setParameter('id', $id['id']);
		$results = $this->query_builder->getQuery()->getResult();
		foreach ($results as $cron_job) {
			return $cron_job;
		}
    }

}

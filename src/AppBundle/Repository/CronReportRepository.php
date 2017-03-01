<?php

namespace AppBundle\Repository;
use Cron\CronBundle\Entity\CronReportRepository as BaseCronReportRepository;

class CronReportRepository extends BaseCronReportRepository
{
	
	private $query_builder = null;
	
    public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class){
    	parent::__construct($em, $class);
    	$this->query_builder = $this->createQueryBuilder('cr');
    }
	
	public function find($id, $lockMode = null, $lockVersion = null){
    	$this->query_builder->andWhere('cr.id = :id')->setParameter('id', $id['id']);
		$results = $this->query_builder->getQuery()->getResult();
		foreach ($results as $cron_report) {
			return $cron_report;
		}
    }

}

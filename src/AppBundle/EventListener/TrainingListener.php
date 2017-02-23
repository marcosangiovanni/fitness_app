<?php
namespace AppBundle\EventListener;
use Doctrine\ORM\Event\LifecycleEventArgs;

use AppBundle\Entity\Training;

class TrainingListener
{

	private $youtubeservice;

	//Read argument passed in construct in file  
	public function __construct($youtubeservice) {
		$this->youtubeservice = $youtubeservice;
	}

    //After training creation i save trainer main sport association
	public function postPersist(Training $training, LifecycleEventArgs $args){
		$this->saveTrainerMainSports($training,$args);
    }

    //Before training creation i check youtube video infos
	public function prePersist(Training $training, LifecycleEventArgs $args){
		$this->checkYoutubeVideoInfos($training,$args);
    }

    //Before training update i check youtube video infos
	public function preUpdate(Training $training, LifecycleEventArgs $args){
		$this->checkYoutubeVideoInfos($training,$args);
    }

    //After training creation i save trainer main sport association
	protected function checkYoutubeVideoInfos(Training $training, LifecycleEventArgs $args){
		try{
			$em = $args->getEntityManager();
	
			//Find ID of youtube video		
			preg_match("#([\/|\?|&]vi?[\/|=]|youtu\.be\/|embed\/)(\w+)#", $training->getVideo(), $matches);
			$video_id = end($matches);
			
			$listResponse = $this->youtubeservice->videos->listVideos('status', array('id' => $video_id));
			
			if(count($listResponse->getItems()) === 0){
				throw new \RuntimeException(sprintf('Could not find video with id %s', $video_id),404);
			}
			
			if(!$infos || $infos['privacyStatus'] === 'private'){
				throw new \Exception('The video is private and cannot be used',409);
			}
		}
		//Google comunication problem
		catch(\Google_Service_Exception $e){
			$errorObj = json_decode($e->getMessage());
			throw new \Google_Service_Exception('Youtube call : '.$errorObj->error->message, $errorObj->error->code);
		}
		//General error
		catch(\Exception $e){
			throw new \Exception($e->getMessage(),$e->getCode());
		}
	}
	
    //After training creation i save trainer main sport association
	protected function saveTrainerMainSports(Training $training, LifecycleEventArgs $args){
		$em = $args->getEntityManager();
		$user = $training->getUser();

		//Get distinct of sport with count
		$query_builder = $em->getRepository('AppBundle:Training')->createQueryBuilder('t');
		
		$trainer_sport_label_number = $em->getRepository('AppBundle:Config')->findOneByCode('trainer_sport_label_number');
		
		$query = $query_builder	
						->select('s.id,count(t.id) as max_sport')
						->innerJoin('t.sport', 's')
						->andWhere('t.user_id = :user_id')
						->groupBy('s.id')
						->orderBy('max_sport','DESC')
						->setParameter('user_id', $user->getId())
						->setMaxResults($trainer_sport_label_number->getValue())
		;
		
		//Remove all trained sports from association
		$user->getSportsTrained()->clear();
		
		//Get all sports entities
		foreach ($query_builder->getQuery()->getResult() as $sport_count) {
			$sport_ids[] = $sport_count['id'];
		}
		$sports = $em->getRepository('AppBundle:Sport')->findById($sport_ids);
		
		//Add all sport to trainer association
		foreach ($sports as $sport) {
			$user->addSportTrained($sport);
		}
		
		$em->persist($user);
		$em->flush();
		
	}

}
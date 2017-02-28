<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyTrainingEndedCommand extends ContainerAwareCommand
{
    protected function configure(){
    	$this	->setName('notify:training-ended')
        		->setDescription('This task notify all users whome training is ended to ask for feedback')
		        ->setHelp('This task notify all users whome training is ended to ask for feedback')
    	;
    }

    protected function execute(InputInterface $input, OutputInterface $output){
    	
		try{
			$output->write('Get Entity Manager');
	    	$em = $this->getContainer()->get('doctrine')->getEntityManager();
			
			$minutes = $em->getRepository('AppBundle:Config')->findOneByCode('notify_training_minutes_after');
	    	$output->writeln('Get config minutes after value : '.$minutes->getValue());
			
			$output->write('Get all training that are ended');
			$repository = $em->getRepository('AppBundle:Training')
													->findByNotNotifiedEndSubscribedUsers()
													->findByEndedTrainings($minutes->getValue())
													->findByEnabled(true)
			;
			
			$output->write('Execute query');
			$trainings = $repository->getQueryBuilder()->getQuery()->getResult();
			
			$output->write('get Push client');
			$fcmClient = $this->getContainer()->get('redjan_ym_fcm.client');
	
			$trainings_num = 0;
			foreach ($trainings as $training) {
				$trainings_num++;
				$output->writeln('Get subscribed of training : '.$training->getId());
				$users_num = 0;
				foreach ($training->getSubscribed() as $subscribed) {

					if($subscribed->getIsNotifiedEnd() !== true){
						try{
							$notification = $fcmClient->createDeviceNotification(
						         'Sta iniziando l\'allenamento', 
						         'Sta per iniziare l\'allenamento a cui ti sei iscritto', 
						         $subscribed->getUser()->getPushToken()
						    );
							$response = $fcmClient->sendNotification($notification);
							
							$output->writeln($response->getBody()->getContents());
							
							$ar_response = json_encode($response->getBody()->getContents());
	
							if($ar_response['failure']){
								throw new \Exception('Notification error for user : '.$subscribed->getUserId(), 1);
							}
	
							$output->writeln('Notification sent for user : '.$subscribed->getUserId());
							$subscribed->setIsNotifiedStart(true);
							$em->persist($subscribed);
							$users_num++;
							
						}catch(\Exception $e){
							$output->writeln('Notification error for user : '.$subscribed->getUserId());
						}
					}

				}
				$output->writeln('Training : '.$training->getId().' - notified '.$users_num.' users.');
				
			}
	
			$output->writeln('Notified '.$trainings_num.' trainings');
			$em->flush();
			$output->write('End of job');
		
		}catch(\Exception $e){
			
			$output->write($e->getMessage());
			
		}

    }
	
}

?>
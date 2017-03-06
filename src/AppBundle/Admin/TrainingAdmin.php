<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use AppBundle\Util\Utility as Utility;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use AppBundle\Util\YoutubeManager;

class TrainingAdmin extends Admin
{

	public function preUpdate($object){
        foreach ($object->getSubscribed() as $subscribed) {
            $subscribed->setTraining($object);
        }
    }
	
	public function validateVideo($data, ExecutionContextInterface $context){
		if($data !== NULL && !YoutubeManager::getYoutubeVideoId($data)){
		    $errorMessage = 'Incorrect youtube video.';
			$context->buildViolation($errorMessage)->addViolation();
	        $this->getConfigurationPool()->getContainer()->get('session')->getFlashBag()->add('sonata_flash_error', $errorMessage);
		}
	}
		
    protected function configureFormFields(FormMapper $formMapper){

		$options = array('required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM));
		
	    if (($subject = $this->getSubject()) && $subject->getPicture()) {
			$container = $this->getConfigurationPool()->getContainer();
			$helper = $container->get('vich_uploader.templating.helper.uploader_helper');
			$path = $helper->asset($subject, 'imageFile');
	        $options['help'] = '<img width="500px" src="' . $path . '" />';
	    }					
		
        $formMapper	
					->tab('Location')
						->with('Position')	
	 						->add('address', 'text', array('attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
	 						->add('latlng','oh_google_maps',array('label' => 'Training position','map_width' => 500),array())
						->end()
					->end()
        			->tab('Info')
        				->with('Main info')
		        			->add('title', 'text', array('attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
		        			->add('description', 'textarea', array('required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
							->add('user', null, array('property' => 'fullname', 'label'=>'Trainer','attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
							->add('sport', null, array('property' => 'title', 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
		 					->add('price', 'number', array('attr' => array('style' => Utility::FIELD_STYLE_SMALL)))
		 					->add('is_public')
		 					->add('enabled')
						->end()
					->end()
        			->tab('Difficulty')
        				->with('Level')
							->add('traininglevel', null, array('property' => 'title', 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
						->end()
        				->with('Intensive for')
							->add('is_cardio', null, array('label' => 'Intensive for lungs/heart', 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
						->end()
					->end()
					->tab('Timings')
						->with('Training date')
							->add('start','sonata_type_datetime_picker', array('attr' => array('style' => Utility::FIELD_STYLE_SMALL),'format' => Utility::DATE_FORMAT_DATETIME))
							->add('end','sonata_type_datetime_picker', array('attr' => array('style' => Utility::FIELD_STYLE_SMALL),'format' => Utility::DATE_FORMAT_DATETIME))
 						->end()
					->end()
					->tab('Media')
						->with('Media')
							->add('imageFile', 'file', array_merge($options,array('label' => 'Image file', 'required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM))))
							->add('video', 'url', array('required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM), 'constraints' => array(new Assert\Callback(array(array($this, 'validateVideo'))))))
						->end()
					->end()
					->tab('Subscribed')
						->with('Subscribed')
							->add('subscribed', 'sonata_type_collection', array(
				                'by_reference' => false,
				                'type_options'       => array( 'delete' => true ),
				                'by_reference' => true,
				                'label' => 'Subscribed',
				                'type_options' => array('delete' => true),
				                'cascade_validation' => true,
				                'btn_add' => 'Add new User',
				            ), array(
				                'edit' => 'inline',
				                'inline' => 'table',
				            ))
						->end()
					->end()
		;
				
    }
	
    protected function configureDatagridFilters(DatagridMapper $datagridMapper){
        $datagridMapper	->add('title')
						->add('user',null)
						->add('enabled', null, 	array('label' => 'Enabled'),'sonata_type_translatable_choice', array(
											                													'translation_domain' => "SonataAdminBundle",
																								                'choices' => array(
																								                    1 => 'label_type_yes',
																								                    2 => 'label_type_no'
																								                ))
			            )
						->add('sport', null, array('label' => 'Sports'), null, array('expanded' => false, 'multiple' => true))
						->add('start', 'doctrine_orm_date_range', array('field_type'=>'sonata_type_date_range_picker'), null, array('format' => Utility::DATE_FORMAT_DATE))
						->add('end', 'doctrine_orm_date_range', array('field_type'=>'sonata_type_date_range_picker'), null, array('format' => Utility::DATE_FORMAT_DATE))
	 					->add('price')
		;
    }

    protected function configureListFields(ListMapper $listMapper){
        $listMapper	->addIdentifier('id')
					->addIdentifier('title')
					->add('enabled', null, array('editable' => true))
					->addIdentifier('user',null,array('associated_property' => 'fullname'))
					->addIdentifier('sport',null,array('associated_property' => 'title'))
					->addIdentifier('start')
					->addIdentifier('end')
					->addIdentifier('price')
					->addIdentifier('_action', 'actions', array(
			            'actions' => array(
			                'edit' => array(),
			                'delete' => array(),
			            )
		        	))
		;
    }
}

?>
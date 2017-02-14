<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use AppBundle\Util\Utility as Utility;

class TrainingAdmin extends Admin
{

	public function preUpdate($object){
        foreach ($object->getSubscribed() as $subscribed) {
            $subscribed->setTraining($object);
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

        $formMapper	->with('General')
	        			->add('title', 'text', array('attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
	 					->add('price', 'number', array('attr' => array('style' => Utility::FIELD_STYLE_SMALL)))
	 					->add('is_public')
						->add('user', null, array('label'=>'User creator','attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
						->add('sport', null, array('attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
						->end()
					->with('Training date')
						->add('start','sonata_type_datetime_picker', array('attr' => array('style' => Utility::FIELD_STYLE_SMALL),'format' => Utility::DATE_FORMAT_DATETIME))
						->add('end','sonata_type_datetime_picker', array('attr' => array('style' => Utility::FIELD_STYLE_SMALL),'format' => Utility::DATE_FORMAT_DATETIME))
	  					->add('cutoff','sonata_type_datetime_picker', array('attr' => array('style' => Utility::FIELD_STYLE_SMALL),'format' => Utility::DATE_FORMAT_DATETIME))
 						->end()
					->with('Position')	
 						->add('latlng','oh_google_maps',array('label' => 'Training position','map_width' => 500),array())
						->end()
					->with('Media')
						->add('imageFile', 'file', array_merge($options,array('label' => 'Image file', 'required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM))))
						->add('video', 'url', array('attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
						->end()
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
		;
    }
	
    protected function configureDatagridFilters(DatagridMapper $datagridMapper){
        $datagridMapper	->add('title')
						->add('user')
						->add('sport')
						->add('start', 'doctrine_orm_date_range', array('field_type'=>'sonata_type_date_range_picker'), null, array('format' => Utility::DATE_FORMAT_DATE))
						->add('end', 'doctrine_orm_date_range', array('field_type'=>'sonata_type_date_range_picker'), null, array('format' => Utility::DATE_FORMAT_DATE))
	  					->add('cutoff', 'doctrine_orm_date_range', array('field_type'=>'sonata_type_date_range_picker'), null, array('format' => Utility::DATE_FORMAT_DATE))
	 					->add('price')
		;
    }

    protected function configureListFields(ListMapper $listMapper){
        $listMapper	->addIdentifier('id')
					->addIdentifier('title')
					->add('user')
					->addIdentifier('sport')
					->addIdentifier('start')
					->addIdentifier('end')
					->addIdentifier('cutoff')
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
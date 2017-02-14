<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use AppBundle\Util\Utility as Utility;

use Sonata\AdminBundle\Route\RouteCollection;

class SubscribedAdmin extends Admin
{

    protected function configureFormFields(FormMapper $formMapper){
        $formMapper	->add('user', 'entity', array(
		                'class' => 'AppBundle\Entity\User\User',
		                'property' => 'email'
		            ))
					->add('training', 'entity', array(
		                'class' => 'AppBundle\Entity\Training',
		                'property' => 'title'
		            ))
					->add('feedback','number', array('attr' => array('style' => Utility::FIELD_STYLE_SMALL)))
					->add('created','sonata_type_datetime_picker', array('attr' => array('style' => Utility::FIELD_STYLE_SMALL),'format' => Utility::DATE_FORMAT_DATETIME))
					->add('updated','sonata_type_datetime_picker', array('attr' => array('style' => Utility::FIELD_STYLE_SMALL),'format' => Utility::DATE_FORMAT_DATETIME))
		;
    }
	
    protected function configureDatagridFilters(DatagridMapper $datagridMapper){
        $datagridMapper	
				        ->add('user', null, array(), null, array(
				            'class' => 'AppBundle\Entity\User\User',
				            'property'=>'email',
				        ))
						->add('training', null, array(), null, array(
				            'class' => 'AppBundle\Entity\Training',
				            'property'=>'title',
				        ))
						->add('training.start', 'doctrine_orm_datetime_range', array('field_type'=>'sonata_type_datetime_range_picker'), null, array('format' => Utility::DATE_FORMAT_DATETIME))
						->add('training.end', 'doctrine_orm_datetime_range', array('field_type'=>'sonata_type_datetime_range_picker'), null, array('format' => Utility::DATE_FORMAT_DATETIME))
		;
    }

    protected function configureListFields(ListMapper $listMapper){
    	
        $listMapper	->addIdentifier('id')
        			->add('user', 'entity', array(
		                'class' => 'AppBundle\Entity\User\User',
		                'associated_property' => 'email'
		            ))
					->add('training', 'entity', array(
		                'class' => 'AppBundle\Entity\Training',
		                'associated_property' => 'title'
		            ))
					->add('training.start')
					->add('training.end')
					->add('created', 'datetime', array('label' => 'Subscription created'))
					->add('updated', 'datetime', array('label' => 'Subscription updated'))
					->add('_action', 'actions', array(
			            'actions' => array(
			                'edit' => array(),
			                'delete' => array(),
			            )
		        	))
		;	
    }
}

?>
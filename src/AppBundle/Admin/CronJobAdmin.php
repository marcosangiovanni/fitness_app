<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use AppBundle\Util\Utility as Utility;

use Sonata\AdminBundle\Route\RouteCollection;

class CronJobAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper){
        $formMapper	->add('name', 'text', array('label' => 'Name', 'required' => true, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
					->add('command', 'text', array('label' => 'Command', 'required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
					->add('schedule', 'text', array('label' => 'Schedule', 'required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
					->add('description', 'textarea', array('label' => 'Description', 'required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
					->add('enabled')
		;
    }
	
    protected function configureDatagridFilters(DatagridMapper $datagridMapper){
        $datagridMapper	->add('id')
						->add('name')
		;
    }

    protected function configureListFields(ListMapper $listMapper){
        $listMapper	->add('id')
					->add('name')
					->add('command')
					->add('schedule')
					->add('description')
					->add('enabled', null, 	array('label' => 'Enabled'),'sonata_type_translatable_choice', array(
										                													'translation_domain' => "SonataAdminBundle",
																							                'choices' => array(
																							                    1 => 'label_type_yes',
																							                    2 => 'label_type_no'
																							                ))
		            )
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
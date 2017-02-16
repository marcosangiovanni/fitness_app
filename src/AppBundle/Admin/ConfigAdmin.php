<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use AppBundle\Util\Utility as Utility;

use Sonata\AdminBundle\Route\RouteCollection;

class ConfigAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper){
        $formMapper	->add('code', 'text', array('label' => 'Code', 'required' => true, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
					->add('value', 'text', array('label' => 'Value', 'required' => true, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
					->add('description', 'textarea', array('label' => 'Description', 'required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
		;
    }
	
    protected function configureDatagridFilters(DatagridMapper $datagridMapper){
        $datagridMapper	->add('code')
						->add('value')
		;
    }

    protected function configureListFields(ListMapper $listMapper){
        $listMapper	->add('id')
					->add('code')
					->add('value')
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
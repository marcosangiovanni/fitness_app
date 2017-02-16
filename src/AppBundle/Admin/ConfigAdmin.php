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
        $formMapper	->add('code', 'text', array('label' => 'Config variable code', 'required' => true, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
					->add('value', 'text', array('label' => 'Config variable value', 'required' => true, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
		;
    }
	
    protected function configureDatagridFilters(DatagridMapper $datagridMapper){
        $datagridMapper	->add('code')
						->add('value')
		;
    }

    protected function configureListFields(ListMapper $listMapper){
        $listMapper	->add('id')
					->add('value')
					->add('code')
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
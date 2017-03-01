<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use AppBundle\Util\Utility as Utility;

use Sonata\AdminBundle\Route\RouteCollection;

class CronReportAdmin extends Admin
{

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'runAt',
    );

    protected function configureFormFields(FormMapper $formMapper){
        $formMapper	->add('job', 'entity', array(
		                'class' 	=> 'AppBundle\Entity\CronJob',
		                'property' 	=> 'name',
		                'attr' => array('style' => Utility::FIELD_STYLE_SMALL)
		            ))
        			->add('run_at','sonata_type_datetime_picker', array('label' => 'Run At', 'attr' => array('style' => Utility::FIELD_STYLE_SMALL),'format' => Utility::DATE_FORMAT_DATETIME))
					->add('run_time', 'text', array('label' => 'Run Time', 'required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
					->add('exit_code', 'text', array('label' => 'Exit code', 'required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
					->add('output', 'textarea', array('label' => 'Output', 'required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
		;
    }
	
    protected function configureDatagridFilters(DatagridMapper $datagridMapper){
        $datagridMapper	->add('id')
						->add('exitCode')
						->add('runAt', 'doctrine_orm_datetime_range', array('field_type'=>'sonata_type_datetime_range_picker'), null, array('format' => Utility::DATE_FORMAT_DATETIME))
						;
    }

    protected function configureListFields(ListMapper $listMapper){
        $listMapper	->add('id')
					->add('job',null,array('associated_property' => 'name'))
					->add('run_at','datetime')
					->add('run_time')
					->add('exit_code')
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
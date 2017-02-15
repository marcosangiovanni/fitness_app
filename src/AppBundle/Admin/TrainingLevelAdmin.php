<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use AppBundle\Util\Utility as Utility;

class TrainingLevelAdmin extends Admin
{

	protected function configureListFields(ListMapper $listMapper){
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('title')
            ->add('created')
            ->add('updated')
        ;
    }
	
	protected function configureDatagridFilters(DatagridMapper $filterMapper){
        $filterMapper
            ->add('title')
			->add('created', 'doctrine_orm_date_range', array('field_type'=>'sonata_type_date_range_picker'), null, array('format' => Utility::DATE_FORMAT_DATE))
			->add('updated', 'doctrine_orm_date_range', array('field_type'=>'sonata_type_date_range_picker'), null, array('format' => Utility::DATE_FORMAT_DATE))
        ;
    }
	
	protected function configureFormFields(FormMapper $formMapper){
        $formMapper
	            ->with('Info')
	                ->add('title','text', array('attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
	                ->add('description','textarea', array('attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
					->add('created','sonata_type_datetime_picker', array('attr' => array('style' => Utility::FIELD_STYLE_SMALL),'format' => Utility::DATE_FORMAT_DATETIME))
					->add('updated','sonata_type_datetime_picker', array('attr' => array('style' => Utility::FIELD_STYLE_SMALL),'format' => Utility::DATE_FORMAT_DATETIME))
	            ->end();
    }


}

?>
<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use AppBundle\Util\Utility as Utility;

class LogAdmin extends Admin
{

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'created',
    );

    protected function configureFormFields(FormMapper $formMapper){

		$options = array('required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM));
		
        $formMapper	->add('user', null, array('property' => 'fullname', 'label'=>'User','attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
					->add('sports_obj', 'text', array('required' => false, 'label' => 'Sport'))
					->add('max_price', 'text', array('required' => false, 'label' => 'Max price'))

					->add('distance', null, array('required' => false, 'label' => 'Distance'))
					->add('latlng','oh_google_maps',array('label' => 'Log search center','map_width' => 500),array())
					->add('fullquery', null, array('required' => false, 'label' => 'Fullquery'))
		;
				
    }
	
    protected function configureDatagridFilters(DatagridMapper $datagridMapper){
        $datagridMapper	->add('user',null, array('property' => 'fullname'))
						->add('date_obj', 'doctrine_orm_date_range', array('field_type'=>'sonata_type_date_range_picker'), null, array('format' => Utility::DATE_FORMAT_DATE))
						->add('date_op')
						->add('max_price')
						->add('distance')
						->add('sports_obj',null,array('label' => 'Sport'))
		;
    }

    protected function configureListFields(ListMapper $listMapper){
        $listMapper	->addIdentifier('id')
					->addIdentifier('user',null,array('associated_property' => 'fullname'))
					->add('sports_obj')
					->add('date_obj')
					->add('date_op')
					->add('max_price')
					->add('distance')
					->add('created')
/*
					->addIdentifier('title')
					->add('enabled', null, array('editable' => true))
					->addIdentifier('sport',null,array('associated_property' => 'title'))
					->addIdentifier('start')
					->addIdentifier('end')
					->addIdentifier('price')
 * 
 */
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
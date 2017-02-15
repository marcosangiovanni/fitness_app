<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class FacebookFriendAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper	->add('user', 'entity', array(
            				'class' 	=> 	'AppBundle\Entity\User\User',
            				'property' 	=> 	'email',
            				'attr' 		=> 	array('style' => 'width:200px')
        				)
					)
					->add('name',null,array('label' => 'Friend\'s Name'))
					->add('facebook_uid',null,array('label' => 'Friend\'s Facebook Uid'))
		;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper	->add('user_id')
						->add('name')
						->add('facebook_uid')
		;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper	->addIdentifier('id')
					->add('user', 'entity', array(
							'label' => 'User',
            				'route' => array(
                    			'name' => 'edit'
                			)
        				)
					)
					->add('name')
					->add('facebook_uid')
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
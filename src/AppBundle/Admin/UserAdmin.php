<?php

// src/AppBundle/Admin/CategoryAdmin.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class UserAdmin extends Admin
{

	//Methods to manage password update
	public function updateUser(\AppBundle\Entity\User $u) {
	    $um = $this->getConfigurationPool()->getContainer()->get('fos_user.user_manager');
	    $um->updateUser($u, false);
	}

	public function prePersist($object){
        parent::prePersist($object);
		$this->updateUser($object);

    }
	
    public function preUpdate($object){
        parent::preUpdate($object);
		$this->updateUser($object);

    }
	
	//Form fields
    protected function configureFormFields(FormMapper $formMapper){
        $formMapper	->add('name')
					->add('surname')
					->add('username')
					->add('email')
					->add('enabled')
					->add('plainPassword', 'repeated', array(
		                'type' => 'password',
		                'options' => array('translation_domain' => 'FOSUserBundle'),
		                'first_options' => array('label' => 'form.password'),
		                'second_options' => array('label' => 'form.password_confirmation'),
		                'invalid_message' => 'fos_user.password.mismatch',
		                'required' => false,
        ));
    }

	//Form filters
    protected function configureDatagridFilters(DatagridMapper $datagridMapper){
        $datagridMapper	->add('name')
						->add('surname')
						->add('username')
						->add('email')
						->add('enabled', null, 	array('label' => 'Enabled'),'sonata_type_translatable_choice', array(
											                													'translation_domain' => "SonataAdminBundle",
																								                'choices' => array(
																								                    1 => 'label_type_yes',
																								                    2 => 'label_type_no'
																								                ))
			            )
		;
    }

	//Grid fields
    protected function configureListFields(ListMapper $listMapper){
        $listMapper	->addIdentifier('id')
					->addIdentifier('name')
					->addIdentifier('surname')
					->addIdentifier('username')
					->addIdentifier('email')
					->add('enabled')
					->add('_action', 'actions', array(
			            'actions' => array(
			                'edit' => array(),
			                'delete' => array(),
			            )
		        	))
					/*
					->add('friends', 'entity', array(
							'multiple' 	=> 	true,
            				'class' 	=> 	'AppBundle\Entity\FacebookFriend',
            				'property' 	=> 	'name',
            				'attr' 		=> 	array('style' => 'width:200px')
        				)
					)
					*/
		;
    }
}

?>
<?php

// src/AppBundle/Admin/CategoryAdmin.php
namespace AppBundle\Admin;

#use Sonata\AdminBundle\Admin\Admin;
use Sonata\UserBundle\Admin\Model\UserAdmin as BaseUserAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use AppBundle\Util\Utility as Utility;

class UserAdmin extends BaseUserAdmin
{

	protected function configureListFields(ListMapper $listMapper){
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('username')
            ->add('email')
            ->add('groups')
            ->add('enabled', null, array('editable' => true))
            ->add('createdAt')
        ;
    }
	
	protected function configureDatagridFilters(DatagridMapper $filterMapper){
        $filterMapper
            ->add('id')
            ->add('username')
            ->add('enabled', null, 	array('label' => 'Enabled'),'sonata_type_translatable_choice', array(
											                													'translation_domain' => "SonataAdminBundle",
																								                'choices' => array(
																								                    1 => 'label_type_yes',
																								                    2 => 'label_type_no'
																								                ))
			            )
            ->add('email')
            ->add('groups')
        ;
    }
	
	protected function configureFormFields(FormMapper $formMapper){
		$options = array('required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM));
		
	    if (($subject = $this->getSubject()) && $subject->getPicture()) {
			$container = $this->getConfigurationPool()->getContainer();
			$helper = $container->get('vich_uploader.templating.helper.uploader_helper');
			$path = $helper->asset($subject, 'imageFile');
	        $options['help'] = '<img width="500px" src="' . $path . '" />';
	    }

        $formMapper
            ->tab('Account')
	            ->with('Credential')
	                ->add('username')
	                ->add('email')
	                ->add('plainPassword', 'text', array(
	                    'required' => (!$this->getSubject() || is_null($this->getSubject()->getId()))
	                ))
					->add('token', null, array('required' => false))
					->add('enabled', null, array('required' => false))
	            ->end();
				
				//If is super admin can change user groups
				if ($this->getSubject() && $this->getSubject()->hasRole('ROLE_SUPER_ADMIN')) {
            		$formMapper->with('Groups')
		                ->add('groups', 'sonata_type_model', array(
		                    'required' => false,
		                    'expanded' => true,
		                    'multiple' => true
		                ))
	            	->end();
        		}

			$formMapper->end();
	            
            $formMapper
            	->tab('Profile')
		            ->with('Profile')
		                ->add('firstname', null, array('required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
		                ->add('lastname', null, array('required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
		                ->add('gender', 'sonata_user_gender', array(
		                    'required' => true,
		                    'translation_domain' => $this->getTranslationDomain(),
		                    'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)
		                ))
						->add('dateOfBirth','sonata_type_date_picker', array('attr' => array('style' => Utility::FIELD_STYLE_SMALL),'format' => Utility::DATE_FORMAT_DATE))
		                ->add('phone', null, array('required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
		            ->end()
	            ->end()
				->tab('Media')
		            ->with('Media')
						->add('imageFile', 'file', array_merge($options,array('label' => 'Image file', 'required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM))))
		                ->add('video', 'url', array('label' => 'Video', 'required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
		            ->end()
	            ->end()
				->tab('Social')
		            ->with('Facebook')
        	        	->add('facebookUid', null, array('required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
            		    ->add('facebookName', null, array('required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
					->end()
            	->end()
        ;

        $formMapper
        	->tab('Relations')
            ->with('Sport')
				->add('sports', 'sonata_type_model', array('property' => 'title', 'label' => 'User sports', 'multiple' => true, 'by_reference' => false, 'btn_add' => false)) 
				->end()
            ->with('Training')
				->add('trainings', 'sonata_type_model', array('property' => 'title', 'label' => 'Created trainings', 'multiple' => true, 'by_reference' => false, 'btn_add' => false),array( 'readonly' => true))
				->add('subscribed', 'sonata_type_collection', array(
	                'by_reference' 			=> false,
	                'label'					=> 'Subscribed trainings',
	                'type_options' 			=> array('delete' => true),
	                'cascade_validation' 	=> true,
	                'btn_add' 				=> 'Add new User',
	            ), array(
	                'edit' 		=> 'inline',
	                'inline' 	=> 'table',
	                'readonly' 	=> true
	            ))
				->end()
				
				
				
		;

    }


}

?>
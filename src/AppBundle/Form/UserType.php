<?php

namespace AppBundle\Form;
use AppBundle\Entity\User\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Form\UserType;

class UserType extends AbstractType
{
	
		/* FORM SOLUTION TO IMPLEMENT IN CONTROLLER 
		
		//Serializa object and json_decode to transform obj in an array request_like
		$request_like_array = json_decode($serializer->serialize($obj, 'json'),true);
		
		//Form creation for update
		$form = $this->createForm(SportType::class, $sport);
		$form->bind($request_like_array);
		
		//Check FORM performing validation data
		if($form->isValid()){
			$em = $this->getDoctrine()->getManager();
			$em->persist($sport);
		    $em->flush();
		}
		
		*/
	
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder	->add('id')
        			->add('picture')
        			->add('video')
        			->add('city')
					//->add('sports', CollectionType::class, 	array('entry_type' => SportType::class,	'by_reference' => false));
					->add('sports', CollectionType::class, 	array('by_reference' => false));
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'allow_extra_fields' => true,
            'csrf_protection'   => false,
        ));
    }

    public function getBlockPrefix(){
        return 'appbundle_user_user';
    }

}

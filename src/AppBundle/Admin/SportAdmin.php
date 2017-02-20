<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use AppBundle\Util\Utility as Utility;

use Sonata\AdminBundle\Route\RouteCollection;

class SportAdmin extends Admin
{
	public $last_position = 0;

    private $positionService;

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'position',
    );

    public function setPositionService(\Pix\SortableBehaviorBundle\Services\PositionHandler $positionHandler){
        $this->positionService = $positionHandler;
    }

    protected function configureRoutes(RouteCollection $collection){
        $collection->add('move', $this->getRouterIdParameter().'/move/{position}');
    }

    protected function configureFormFields(FormMapper $formMapper){
		//Setting 
		$options_img = array('required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM));
	    if (($subject = $this->getSubject()) && $subject->getPicture()) {
			$container = $this->getConfigurationPool()->getContainer();
			$helper = $container->get('vich_uploader.templating.helper.uploader_helper');
			$path = $helper->asset($subject, 'imageFile');
	        $options_img['help'] = '<img width="100px" src="' . $path . '" />';
	    }					
		$options_plc = array('required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM));
	    if (($subject = $this->getSubject()) && $subject->getPlaceholder()) {
			$container = $this->getConfigurationPool()->getContainer();
			$helper = $container->get('vich_uploader.templating.helper.uploader_helper');
			$path = $helper->asset($subject, 'placeholderFile');
	        $options_plc['help'] = '<img width="400px" src="' . $path . '" />';
	    }					

        $formMapper	
        	->with('Title')
        		->add('title', 'text', array('attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
			->end()
			->with('Picture')
				->add('picture', null, $options_img)
				->add('imageFile', 'file', array('label' => 'Image file', 'required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
			->end()
			->with('Training placeholder')
				->add('placeholder', null, $options_plc)
				->add('placeholderFile', 'file', array('label' => 'Placeholder file', 'required' => false, 'attr' => array('style' => Utility::FIELD_STYLE_MEDIUM)))
			->end()
		;
    }
	
    protected function configureDatagridFilters(DatagridMapper $datagridMapper){
        $datagridMapper->add('title');
    }

    protected function configureListFields(ListMapper $listMapper){
    	
        $listMapper	->add('id')
					->add('title')
					->add('picture', 'string', array('template' => 'admin/image_format_list_vich.html.twig'))
					->add('_action', 'actions', array(
			            'actions' => array(
			            	'move' => array('template' => 'PixSortableBehaviorBundle:Default:_sort.html.twig'),
			                'edit' => array(),
			                'delete' => array(),
			            )
		        	))
		;
    }
}

?>
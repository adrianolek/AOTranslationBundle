<?php
namespace AO\TranslationBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class MessageAdmin extends Admin
{
    private $container;
    
    public function setContainer($container)
    {
        $this->container = $container;
    }
    
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('identification')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('domain')
            ->add('identification')            
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $locales = array_keys($this->container->getParameter('ao_translation.locales'));
        $listMapper
            ->addIdentifier('id')
            ->add('domain')
            ->add('identification')
        ;
        
        foreach($locales as $locale)
        {
            $listMapper->add($locale, null, array(
                'code' => 'getLocaleTranslation',
                'parameters' => array($locale)));
        }
    }
}
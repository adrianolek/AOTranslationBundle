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
        $locales = array_keys($this->container->getParameter('ao_translation.locales'));
        
        $formMapper
            ->add('identification', 'text', array('read_only' => true))
            ->add('translations', 'sonata_type_collection', array(
                'type_options' => array('delete' => false),
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
            ))
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
        
        $listMapper->add('_action', 'actions', array(
            'actions' => array(
                'edit' => array(),
                'delete' => array(),
            )
        ));
    }

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $query->leftJoin('o.translations', 't');
        $query->addSelect('t');
        return $query;
    }
}
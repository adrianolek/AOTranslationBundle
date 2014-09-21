<?php
namespace AO\TranslationBundle\Admin;

use AO\TranslationBundle\Form\Admin\TranslationsType;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


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
        $locales = $this->container->getParameter('ao_translation.locales');      
        
        $formMapper
            ->add('identification', 'text', array('read_only' => true))
            ->add('translations', new TranslationsType($this->getSubject(), $locales), array(
                'required' => false, 'mapped' => false));

        $formMapper->getFormBuilder()->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event){
            $form = $event->getForm();
            $message = $form->getData();
            $data = $event->getData();
            foreach($data['translations'] as $locale => $translation) {
                $message->setTranslation($locale, $translation);
            }
        });
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
        $locales = $this->container->getParameter('ao_translation.locales');
        $listMapper
            ->addIdentifier('id')
            ->add('domain')
            ->add('identification')
        ;

        foreach ($locales as $locale => $label) {
            $listMapper->add($locale, null, array(
                'label' => $label,
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

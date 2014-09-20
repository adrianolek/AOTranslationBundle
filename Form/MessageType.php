<?php


namespace AO\TranslationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MessageType extends AbstractType
{
    public function __construct($locales)
    {
        $this->locales = $locales;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add textarea for each translation locale
        foreach ($this->locales as $locale) {
            $builder->add($locale, 'textarea', array('required' => false));
        }
    }

    public function getName()
    {
        return 'message';
    }
}

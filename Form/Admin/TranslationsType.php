<?php


namespace AO\TranslationBundle\Form\Admin;

use AO\TranslationBundle\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TranslationsType extends AbstractType
{
    private $locales;
    private $message;
    
    public function __construct(Message $message, $locales)
    {
        $this->locales = $locales;
        $this->message = $message;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = array();
        foreach($this->locales as $locale => $label) {
            $builder->add($locale, 'textarea', array('label' => $label, 'required' => false));
            $data[$locale] = (string) $this->message->getLocaleTranslation($locale);
        }
        
        $builder->setData($data);        
    }

    public function getName()
    {
        return 'translations';
    }
}

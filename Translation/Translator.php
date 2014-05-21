<?php
namespace AO\TranslationBundle\Translation;
use Symfony\Bundle\FrameworkBundle\Translation\Translator as BaseTranslator;
use Symfony\Component\Translation\MessageSelector;

/**
 * @author Adrian Olek <adrianolek@gmail.com>
 */
class Translator extends BaseTranslator
{

    private $doctrineCatalogues = array();
    private $selector, $bundle, $controller, $action;

    /**
     * Store bundle, controller & action name
     */
    private function setAction()
    {
        if (isset($this->bundle)) {
            return;
        }

        $this->bundle = $this->controller = $this->action = 'none';

        if ($this->container->isScopeActive('request')) {
          $request = $this->container->get('request');

          if ($controller = $request->get('_controller')) {
            preg_match('/(.+)\\\\(.+Bundle)\\\\Controller\\\\(.+)Controller::(.+)Action/', $controller, $matches);
            if ($matches) {
              $this->bundle = $matches[1].$matches[2];
              $this->controller = $matches[3];
              $this->action = $matches[4];
            }
          }
        }
    }

    public function setCommand($bundle, $controller, $action)
    {
        $this->bundle = $bundle;
        $this->controller = $controller;
        $this->action = $action;
    }

    /**
     * Load doctrine catalogue for selected locale.
     * @param unknown $locale
     */
    protected function loadDoctrineCatalogue($locale)
    {
        if (isset($this->doctrineCatalogues[$locale])) {
          return;
        }

        $this->setAction();

        $this->doctrineCatalogues[$locale] = $this->container->get('ao_translation.loader.doctrine')
            ->loadAction($locale, $this->bundle, $this->controller, $this->action);
    }

    /**
     * @see \Symfony\Component\Translation\Translator::trans()
     */
    public function trans($id, array $parameters = array(), $domain = 'messages', $locale = null)
    {
        $message = $this->getMessage($id, $domain, $parameters, $locale);
        if ($message->getContent()) {
            // return entity translation
            return strtr($message->getContent(), $parameters);
        }

        // else return translation handled by sf
        return parent::trans($id, $parameters, $domain, $locale);
    }

    /**
     * @see \Symfony\Component\Translation\Translator::transChoice()
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = 'messages', $locale = null)
    {
        if (!isset($this->selector)) {
            $this->selector = new MessageSelector();
        }

        $message = $this->getMessage($id, $domain, $parameters, $locale);
        if ($message->getContent()) {
          // return entity translation
          return strtr($this->selector->choose($message->getContent(), (int) $number, $locale), $parameters);
        }

        // else return translation handled by sf
        return parent::transChoice($id, $number, $parameters, $domain, $locale);
    }

    /**
     * Get message from doctrine catalogue
     * @param  string                                    $id
     * @param  string                                    $domain
     * @param  array                                     $parameters
     * @param  string                                    $locale
     * @return \AO\TranslationBundle\Translation\Message
     */
    protected function getMessage($id, $domain, $parameters, $locale)
    {
        if (!isset($locale)) {
          $locale = $this->getLocale();
        }

        // load catalogue from db if not loaded
        if (!isset($this->doctrineCatalogues[$locale])) {
          $this->loadDoctrineCatalogue($locale);
        }

        if (!$this->doctrineCatalogues[$locale]->defines($id, $domain)) {
          // add message to catalogue
          $this->doctrineCatalogues[$locale]->addMessage($this->container->get('ao_translation.loader.doctrine')->loadMessage($id, $domain, $locale));
        }

        $message = $this->doctrineCatalogues[$locale]->getMessage((string) $id, $domain);
        $message->updateParameters(array_keys($parameters));
        $message->setCache($this->bundle, $this->controller, $this->action);

        return $message;
    }

    /**
     * Get messages used in all loaded locales.
     * @return array
     */
    public function getMessages()
    {
        $messages = array();
        foreach ($this->doctrineCatalogues as $locale => $catalogue) {
          foreach ($catalogue->getMessageObjects() as $domain => $msgs) {
            if (!isset($messages[$domain])) {
              $messages[$domain] = array();
            }
            $messages[$domain] = array_merge($messages[$domain], $msgs);
          }
        }

        return $messages;
    }
}

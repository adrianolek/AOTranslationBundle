<?php

namespace AO\TranslationBundle\Translation;
/**
 * @author Adrian Olek <adrianolek@gmail.com>
 */
class Message
{
    private $identification, $content, $domain, $parameters = array(), $status = 'new',
        $bundle, $controller, $action,
        $entity, $updateParameters = false;

    public function __construct($identification, $content, $domain)
    {
        $this->identification = $identification;
        $this->content = $content;
        $this->domain = $domain;
    }

    public function getIdentification()
    {
        return $this->identification;
    }

    public function setIdentification($value)
    {
        $this->identification = $value;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($value)
    {
        $this->content = $value;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setDomain($value)
    {
        $this->domain = $value;
    }
    
    public function getParameters() 
    {
      return $this->parameters;
    }
    
    public function setParameters($value) 
    {
      $this->parameters = $value;
    }
    
    public function getStatus() 
    {
      return $this->status;
    }
    
    public function setStatus($value) 
    {
      $this->status = $value;
    }
        
    public function getBundle() 
    {
      return $this->bundle;
    }
    
    public function setBundle($value) 
    {
      $this->bundle = $value;
    }
    
    public function getController() 
    {
      return $this->controller;
    }
    
    public function setController($value) 
    {
      $this->controller = $value;
    }
    
    public function getAction() 
    {
      return $this->action;
    }
    
    public function setAction($value) 
    {
      $this->action = $value;
    }
    
    /**
     * Returns true when message hasn't been stored in db yet.
     * @return boolean
     */
    public function isNew()
    {
      return $this->getStatus() == 'new';
    }
    
    /**
     * Returns true when message has cache entry.
     * @return boolean
     */
    public function isCached()
    {
      return $this->getStatus() == 'cached';
    }
    
    /**
     * Set cache parameters.
     * @param string $bundle
     * @param string $controller
     * @param string $action
     */
    public function setCache($bundle, $controller, $action)
    {
      list($this->bundle, $this->controller, $this->action) = func_get_args();
    }
    
    /**
     * Get cache key - used in translation saving listener.
     * @return string
     */
    public function getCacheKey()
    {
      return $this->getBundle().':'.$this->getController().':'.$this->getAction();
    }
    
    /**
     * Return message doctrine entity
     * @return \AO\TranslationBundle\Entity\Message
     */
    public function getEntity() 
    {
      return $this->entity;
    }
    
    /**
     * Set message doctrine entity
     * @param \AO\TranslationBundle\Entity\Message $value
     */
    public function setEntity($value) 
    {
      $this->entity = $value;
    }
    
    /**
     * Set current parameters of message and check if there is any change.
     * @param array $value
     */
    public function updateParameters($value)
    {
        if ($this->getParameters() != $value) {
          $this->updateParameters = true;
        }
        
        $this->setParameters($value);
    }
    
    /**
     * Tells if the message parameters have changed comparing to those stored in db. 
     * @return boolean
     */
    public function getUpdateParameters()
    {
        return $this->updateParameters;
    }
}

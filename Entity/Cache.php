<?php
namespace AO\TranslationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Timestampable;

/**
 * @author Adrian Olek <adrianolek@gmail.com>
 * 
 * @ORM\Table(name="ao_translation_cache",
 *   uniqueConstraints={@ORM\UniqueConstraint(name="action_uniq",columns={"bundle", "controller", "action"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="AO\TranslationBundle\Entity\CacheRepository")
 */
class Cache
{
    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $bundle;
    
    /**
     * @ORM\Column(type="string")
     */
    private $controller;
    
    /**
     * @ORM\Column(type="string")
     */
    private $action;
    
    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
        
    public function __construct()
    {
      $this->messages = new ArrayCollection();
    }
    
        
    public function getId() 
    {
      return $this->id;
    }
    
    public function setId($value) 
    {
      $this->id = $value;
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
}
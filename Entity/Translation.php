<?php
namespace AO\TranslationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Timestampable;

/**
 * @author Adrian Olek <adrianolek@gmail.com>
 * 
 * @ORM\Table(name="ao_translations",
 *   uniqueConstraints={@ORM\UniqueConstraint(name="message_locale_uniq",columns={"message_id","locale"})})
 * @ORM\Entity
 */
class Translation
{
    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="translations", cascade={"all"})
     */
    private $message;
    
    /**
     * @ORM\Column(type="string")
     */
    private $locale;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;
    
    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;
    
    public function getLocale() 
    {
      return $this->locale;
    }
    
    public function setLocale($value) 
    {
      $this->locale = $value;
    }
           
    public function getContent() 
    {
      return $this->content;
    }
    
    public function setContent($value) 
    {
      $this->content = $value;
    }
            
    public function getMessage() 
    {
      return $this->message;
    }
    
    public function setMessage($value) 
    {
      $this->message = $value;
    }
}
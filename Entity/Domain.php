<?php
namespace AO\TranslationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Timestampable;

/**
 * @author Adrian Olek <adrianolek@gmail.com>
 * 
 * @ORM\Table(name="ao_translation_domains",
 *   uniqueConstraints={@ORM\UniqueConstraint(name="name_uniq",columns={"name"})})
 * @ORM\Entity
 */
class Domain
{
    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;
    
    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="domain")
     */
    private $messages;
    
    public function __construct()
    {
      $this->messages = new ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->getName();
    }
    
    public function getId() 
    {
      return $this->id;
    }

    public function getName() 
    {
      return $this->name;
    }
    
    public function setName($value) 
    {
      $this->name = $value;
    }
}
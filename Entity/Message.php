<?php
namespace AO\TranslationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Timestampable;

/**
 * @author Adrian Olek <adrianolek@gmail.com>
 *
 * @ORM\Table(name="ao_translation_messages",
 *   uniqueConstraints={@ORM\UniqueConstraint(name="domain_message_uniq",columns={"domain_id", "identification"})})
 * @ORM\Entity
 */
class Message
{
    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Domain", inversedBy="messages")
     */
    private $domain;

    /**
     * @ORM\Column(type="string",options={"collation"="utf8_bin"})
     */
    private $identification;

    /**
     * @ORM\Column(type="array")
     */
    private $parameters;

    /**
     * @ORM\Column(type="array")
     */
    private $occurences;

    /**
     * @ORM\Column(name="uses_count", type="integer")
     */
    private $usesCount;

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

    /**
     * @ORM\OneToMany(targetEntity="Translation", mappedBy="message", cascade={"all"})
     */
    private $translations;

    /**
     * @ORM\ManyToMany(targetEntity="Cache")
     * @ORM\JoinTable(name="ao_translation_cache_messages")
     */
    private $caches;

    public function __construct()
    {
      $this->caches = new ArrayCollection();
      $this->translations = new ArrayCollection();
      $this->parameters = array();
      $this->occurences = array();
      $this->usesCount = 0;
    }

    public function getId()
    {
      return $this->id;
    }

    public function setId($value)
    {
      $this->id = $value;
    }

    public function getDomain()
    {
      return $this->domain;
    }

    public function setDomain($value)
    {
      $this->domain = $value;
    }

    public function getIdentification()
    {
      return $this->identification;
    }

    public function setIdentification($value)
    {
      $this->identification = $value;
    }

    public function getParameters()
    {
      return $this->parameters;
    }

    public function setParameters($value)
    {
      $this->parameters = $value;
    }

    public function getCaches()
    {
      return $this->caches;
    }

    public function setCaches($value)
    {
      $this->caches = $value;
    }

    public function getTranslations()
    {
      return $this->translations;
    }

    public function setTranslations($value)
    {
      $this->translations = $value;
    }

    /**
     * Set translation content for specified locale.
     *
     * @param string $locale
     * @param string $content
     */
    public function setTranslation($locale, $content)
    {
        $translations = $this->getTranslations();
        foreach ($translations as $t) {
            if ($t->getLocale() == $locale) {
                $t->setContent($content);

                return;
            }
        }
        $t = new Translation();
        $t->setLocale($locale);
        $t->setContent($content);
        $t->setMessage($this);
        $translations->add($t);
        $this->translations = $translations;
    }

    /**
     * Get translation for specified locale.
     *
     * @param  string                                       $locale
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getLocaleTranslation($locale)
    {
        foreach ($this->getTranslations() as $translation) {
            if ($translation->getLocale() == $locale) {
                return $translation;
            }
        }
    }
}

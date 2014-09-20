<?php
namespace AO\TranslationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Orm\EntityManager;

/**
 * @author Adrian Olek <adrianolek@gmail.com>
 */
class TranslationsType extends AbstractType
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var array
     */
    private $messages;

    /**
     * @var array
     */
    private $locales;

    public function __construct(EntityManager $entityManager, $messages, $locales)
    {
        $this->em = $entityManager;
        $this->messages = $messages;
        $this->locales = $locales;
    }

    public function getLocales()
    {
        return $this->locales;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // hidden input to store ids of edited messages
        $builder->add('ids', 'hidden');

        $data = array();

        $qb = $this->em->createQueryBuilder();
        $qb->select('m.id, m.parameters, m.identification, t.locale, t.content')
            ->from('AOTranslationBundle:Message', 'm')
            ->leftJoin('m.translations', 't')
            ->join('m.domain', 'd')
            ->where('m.identification IN (:ids) AND d.name=:domain')
            ->orderBy('m.identification');
        $q = $qb->getQuery();

        foreach ($this->messages as $domain => $messages) {
            $q->setParameter('ids', $messages);
            $q->setParameter('domain', $domain);

            // store additional infos for displaying in translations table
            $infos = array();
            foreach ($q->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY) as $row) {
                $infos[$row['id']] = array('id' => $row['id'],
                        'identification' => $row['identification'],
                        'parameters' => $row['parameters']);

                // embed message form row
                $builder->add((string) $row['id'], new MessageType($this->locales));
                // set default value
                $data[$row['id']][$row['locale']] = $row['content'];
            }
            $this->messages[$domain] = $infos;
        }
        $builder->setData($data);
    }

    public function getName()
    {
        return 'translations';
    }

    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Save form data
     * @param array $data
     */
    public function save($data)
    {
        // get the ids of translations that changed
        $ids = explode(',', $data['ids']);

        if ($ids) {
            // load translations entities
            $qb = $this->em->createQueryBuilder();
            $qb->select('m')
                ->from('AOTranslationBundle:Message', 'm')
                ->leftJoin('m.translations', 't')
                ->where('m.id IN (:ids)');
            $q = $qb->getQuery();

            $q->setParameter('ids', $ids);

            foreach ($q->getResult() as $message) {
                foreach ($data[$message->getId()] as $locale => $content) {
                    if ($content) {
                        // set translation content
                        $message->setTranslation($locale, $content);
                    } elseif ($translation = $message->getLocaleTranslation($locale)) {
                        // or remove when empty
                        $this->em->remove($translation);
                    }
                }
            }

            $this->em->flush();
        }
    }
}
<?php
namespace AO\TranslationBundle\Translation\Loader;
use Symfony\Component\Translation\Loader\LoaderInterface;
use AO\TranslationBundle\Translation\MessageCatalogue;
use AO\TranslationBundle\Translation\Message;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Loads messages stored as doctrine entities.
 * @author Adrian Olek <adrianolek@gmail.com>
 */
class DoctrineLoader implements LoaderInterface
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(ContainerInterface $container)
    {
        $this->em = $container->get('ao_translation.entity_manager');
    }

    public function load($resource, $locale, $domain = 'messages')
    {

    }

    /**
     * Load messages cached for specified action.
     * @param  string                                             $locale
     * @param  string                                             $bundle
     * @param  string                                             $controller
     * @param  string                                             $action
     * @return \AO\TranslationBundle\Translation\MessageCatalogue
     */
    public function loadAction($locale, $bundle, $controller, $action)
    {
        $catalogue = new MessageCatalogue($locale);
        $qb = $this->em->createQueryBuilder();

        $qb->select('m, d, t')->from('AOTranslationBundle:Message', 'm')
                ->leftJoin('m.translations', 't', 'WITH', 't.locale = :locale')
                ->innerJoin('m.caches', 'c')
                ->innerJoin('m.domain', 'd')
                ->where('c.bundle=:bundle AND c.controller=:controller AND c.action=:action')
                ->setParameters(
                        array('locale' => $locale,
                              'bundle' => $bundle,
                              'controller' => $controller,
                              'action' => $action));

        $results = $qb->getQuery()->getResult();

        foreach ($results as $m) {
            $message = new Message($m->getIdentification(), null, $m->getDomain()->getName());
            if ($translation = $m->getLocaleTranslation($locale)) {
              $message->setContent($translation->getContent());
            }
            $message->setParameters($m->getParameters());
            $message->setStatus('cached');
            $message->setEntity($m);

            $catalogue->addMessage($message);
        }

        return $catalogue;
    }

    /**
     * Load message for specified domain & id.
     * @param  string                                    $id
     * @param  string                                    $domain
     * @param  string                                    $locale
     * @return \AO\TranslationBundle\Translation\Message
     */
    public function loadMessage($id, $domain, $locale)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('m, t')->from('\AO\TranslationBundle\Entity\Message', 'm')->join('m.domain', 'd')
                ->leftJoin('m.translations', 't', 'WITH', 't.locale = :locale')->where('m.identification = :id AND d.name=:domain')
                ->setParameters(array('id' => $id, 'domain' => $domain, 'locale' => $locale));

        $m = $qb->getQuery()->getOneOrNullResult();

        $message = new Message($id, null, $domain);

        if ($m) {
            $message->setParameters($m->getParameters());
            $message->setStatus('not_cached');
            $message->setEntity($m);

            if ($translation = $m->getLocaleTranslation($locale)) {
                $message->setContent($translation->getContent());
            }
        }

        return $message;
    }
}

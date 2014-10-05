<?php
namespace AO\TranslationBundle\EventListener;
use AO\TranslationBundle\Entity\Cache;
use AO\TranslationBundle\Entity\Message;
use AO\TranslationBundle\Entity\Domain;
use AO\TranslationBundle\Translation\Translator;
use Symfony\Component\EventDispatcher\Event;
use AO\TranslationBundle\Translation;
use Doctrine\ORM\EntityManager;

/**
 * @author Adrian Olek <adrianolek@gmail.com>
 *
 * Stores translation messages info.
 */
class TranslationListener
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(Translator $translator, EntityManager $entityManager)
    {
        $this->translator = $translator;
        $this->em = $entityManager;
    }

    public function onCommand(Event $event)
    {
        $command = $event->getCommand();
        $class = get_class($command);
        preg_match('/^(.+Bundle)\\\\.+\\\\(.+)$/', $class, $matches);
        if ($matches) {
            $bundle = str_replace('\\', '', $matches[1]);
            $controller = $matches[2];
        } else {
            $bundle = '';
            $controller = '';
        }

        $this->translator->setCommand($bundle, $controller, $command->getName());
    }

    /**
     * Save domain, message & cache info on kernel.terminate
     * @param Event $event
     */
    public function onTerminate(Event $event)
    {
        $t_messages = $this->translator->getMessages();

        // prepare domains ids array for new messages
        $domains = array();
        // prepare cache ids array for not cached messages
        $caches = array();

        foreach ($t_messages as $domain => $messages) {
            foreach ($messages as $message) {
                if ($message->isNew()) {
                    // we need domain ids only for new messages
                    $domains[$domain] = null;
                }

                if (!$message->isCached()) {
                    // we need cache ids only for not cached messages
                    $caches[$message->getCacheKey()] = array(
                        'bundle' => $message->getBundle(),
                        'controller' => $message->getController(),
                        'action' => $message->getAction()
                    );
                }
            }
        }

        // load domain ids
        if ($domains) {
            // load existing domain ids
            $qb = $this->em->createQueryBuilder();
            $qb
                ->select('d.id, d.name')->from('\AO\TranslationBundle\Entity\Domain', 'd')
                ->where('d.name IN (:names)')
                ->setParameter('names', array_keys($domains))
            ;

            $q = $qb->getQuery();
            $iterable = $q->iterate();

            while ($row = $iterable->next()) {
                $row = array_shift($row);
                $domains[$row['name']] = $row['id'];
            }

            // create missing domains and get its ids
            foreach ($domains as $domain => &$id) {
                if (!$id) {
                    $d = new Domain();
                    $d->setName($domain);
                    $this->em->persist($d);
                    $this->em->flush();
                    $id = $d->getId();
                }
            }
        }

        // load cache ids
        foreach ($caches as &$cache) {
            // load existing cache ids
            $qb = $this->em->createQueryBuilder();
            $qb
                ->select('c')
                ->from('\AO\TranslationBundle\Entity\Cache', 'c')
                ->where('c.bundle = :bundle AND c.controller = :controller AND c.action = :action')
                ->setParameters($cache)
            ;

            $c = $qb->getQuery()->getOneOrNullResult();

            if ($c) {
                $cache = $c->getId();
            } else {
                // create missing cache and get its id
                $c = new Cache();
                $c->setBundle($cache['bundle']);
                $c->setController($cache['controller']);
                $c->setAction($cache['action']);
                $this->em->persist($c);
                $this->em->flush();
                $cache = $c->getId();
            }
        }

        // save messages
        foreach ($t_messages as $domain => $messages) {
            foreach ($messages as $message) {
                // create new message
                if ($message->isNew()) {
                    $m = new Message();
                    $m->setIdentification($message->getIdentification());
                    $m->setDomain($this->em->getReference('\AO\TranslationBundle\Entity\Domain', $domains[$message->getDomain()]));
                    $m->setParameters($message->getParameters());
                    $this->em->persist($m);
                    $this->em->flush();
                    $message->setEntity($m);
                }

                // add cache
                if (!$message->isCached()) {
                    $m = $message->getEntity();
                    $c = $caches[$message->getCacheKey()];
                    $m->getCaches()->add($this->em->getReference('\AO\TranslationBundle\Entity\Cache', $c));
                    $this->em->persist($m);
                }

                // update parameters if needed
                if ($message->getUpdateParameters()) {
                    $m = $message->getEntity();
                    $m->setParameters($message->getParameters());
                    $this->em->persist($m);
                }
            }
        }

        $this->em->flush();
    }
}

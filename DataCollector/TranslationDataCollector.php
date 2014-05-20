<?php

namespace AO\TranslationBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * TranslationDataCollector.
 *
 * @author Adrian Olek <adrianolek@gmail.com>
 */
class TranslationDataCollector extends DataCollector
{
    private $container;

    /**
     * @param ContainerInterface $container A ContainerInterface instance
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        if ($controller = $request->get('_controller')) {
            preg_match('/(.+)\\\\(.+Bundle)\\\\Controller\\\\(.+)Controller::(.+)Action/', $controller, $matches);
            if($matches)
            {
                $this->data['cache_key'] = array(
                    'bundle' => $matches[1].$matches[2],
                    'controller' => $matches[3],
                    'action' => $matches[4]);
            }
        }
        
        $this->data['messages'] = array();
        $this->data['unatranslated_count'] = 0;
        
        foreach($this->container->get('translator')->getMessages() as $domain => $messages)
        {
            $this->data['messages'][$domain] = array_keys($messages);
            foreach($messages as $m)
            {
                if(!$m->getContent())
                {
                    $this->data['unatranslated_count']++;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'translation';
    }

    /**
     * Returns message names grouped by domain
     * @return array The message names array
     */
    public function getMessages()
    {
        return $this->data['messages'];
    }

    /**
     * Returns total number of messages in all domains
     * @return number
     */
    public function getMessagesCount()
    {
        $count = 0;
        foreach($this->data['messages'] as $domain => $messages)
        {
            $count += count($messages);
        }
        return $count;
    }

    /**
     * Returns number of messages without translation in current locale
     * @return number
     */
    public function getUntranslatedCount()
    {
        return $this->data['unatranslated_count'];
    }
    
    public function getCacheKey()
    {
        return $this->data['cache_key'];
    }
}

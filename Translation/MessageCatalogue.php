<?php

namespace AO\TranslationBundle\Translation;
use Symfony\Component\Translation as BaseTranslation;

/**
 * @author Adrian Olek <adrianolek@gmail.com>
 *
 */
class MessageCatalogue extends BaseTranslation\MessageCatalogue
{
    private $msg_objs = array();

    /**
     * Add message object to catalogue.
     * @param Message $message
     */
    public function addMessage(Message $message)
    {
        $this->msg_objs[$message->getDomain()][$message->getIdentification()] = $message;

        return $this->set($message->getIdentification(), (string) $message->getContent(), $message->getDomain());
    }

    /**
     * Get message object from catalogue.
     * @param string $identification
     * @param string $domain
     */
    public function getMessage($identification, $domain)
    {
        if (isset($this->msg_objs[$domain][$identification])) {
            return $this->msg_objs[$domain][$identification];
        }
    }

    /**
     * Get all message objects from catalogue.
     * @return array:
     */
    public function getMessageObjects()
    {
        return $this->msg_objs;
    }

}

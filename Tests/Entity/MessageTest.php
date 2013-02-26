<?php
namespace AO\TranslationBundle\Tests\Entity;

use AO\TranslationBundle\Entity\Translation;

use AO\TranslationBundle\Entity\Message;


class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function testMessage()
    {
        $message = new Message();
        $message->setTranslation('foo', 'bar');
        
        $this->assertEquals('bar', $message->getLocaleTranslation('foo')->getContent());
    }
}
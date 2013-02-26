<?php 

namespace AO\TranslationBundle\Tests\Translation;

use AO\TranslationBundle\Translation\Message;

use AO\TranslationBundle\Translation\MessageCatalogue;

class MessageCatalogueTest extends \PHPUnit_Framework_TestCase
{
    public function testMessage()
    {
        $catalogue = new MessageCatalogue('foo');
        $message = new Message('foo', 'bar', 'baz');
        $catalogue->addMessage($message);
        
        $this->assertTrue($catalogue->defines('foo', 'baz'));
        $this->assertEquals($message, $catalogue->getMessage('foo', 'baz'));
    }
}
<?php

namespace AO\TranslationBundle\Tests\Translation;

use AO\TranslationBundle\Translation\Message;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function testMessage()
    {
        $message = new Message('foo', 'bar', 'baz');
        $message->setParameters(array('foo' => 'bar'));

        $message->setCache('foo', 'bar', 'baz');

        $this->assertTrue($message->isNew());
        $this->assertFalse($message->isCached());
        $this->assertEquals('foo:bar:baz', $message->getCacheKey());
        $this->assertFalse($message->getUpdateParameters());
        $message->updateParameters(array('foo' => 'bar'));
        $this->assertFalse($message->getUpdateParameters());
        $message->updateParameters(array('bar' => 'baz'));
        $this->assertTrue($message->getUpdateParameters());
    }
}

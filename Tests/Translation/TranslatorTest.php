<?php

namespace AO\TranslationBundle\Tests\Translation;

use AO\TranslationBundle\Translation\Translator;

class TranslatorTest extends \PHPUnit_Framework_TestCase
{
    public function testMessage()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $selector = $this->getMock('Symfony\Component\Translation\MessageSelector');
        $translator = new Translator($container, $selector);
    }
}

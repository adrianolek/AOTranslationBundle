<?php
namespace AO\TranslationBundle\Tests\DataCollector;
use AO\TranslationBundle\DataCollector\TranslationDataCollector;

class TranslationDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function testAdd()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $calc = new TranslationDataCollector($container);

        $this->assertEquals(1, 1);
    }
}

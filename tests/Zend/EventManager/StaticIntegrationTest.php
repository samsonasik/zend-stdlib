<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_EventManager
 * @subpackage UnitTests
 */

namespace ZendTest\EventManager;

use Zend\EventManager\EventManager,
    Zend\EventManager\StaticEventManager,
    PHPUnit_Framework_TestCase as TestCase;

/**
 * @category   Zend
 * @package    Zend_EventManager
 * @subpackage UnitTests
 * @group      Zend_EventManager
 */
class StaticIntegrationTest extends TestCase
{
    public function setUp()
    {
        StaticEventManager::resetInstance();
    }

    public function testCanConnectStaticallyToClassWithEvents()
    {
        $counter = (object) array('count' => 0);
        $events  = StaticEventManager::getInstance();
        $events->attach(
            'ZendTest\EventManager\TestAsset\ClassWithEvents', 
            'foo', 
            function ($e) use ($counter) {
                $counter->count++;
            }
        );
        $class = new TestAsset\ClassWithEvents();
        $class->getEventManager()->setSharedManager($events);
        $class->foo();
        $this->assertEquals(1, $counter->count);
    }

    public function testLocalHandlersAreExecutedPriorToStaticHandlersWhenSetWithSamePriority()
    {
        $test = (object) array('results' => array());
        $events = StaticEventManager::getInstance();
        $events->attach(
            'ZendTest\EventManager\TestAsset\ClassWithEvents', 
            'foo', 
            function ($e) use ($test) {
                $test->results[] = 'static';
            }
        );
        $class = new TestAsset\ClassWithEvents();
        $class->getEventManager()->attach('foo', function ($e) use ($test) {
            $test->results[] = 'local';
        });
        $class->getEventManager()->setSharedManager($events);
        $class->foo();
        $this->assertEquals(array('local', 'static'), $test->results);
    }

    public function testLocalHandlersAreExecutedInPriorityOrderRegardlessOfStaticOrLocalRegistration()
    {
        $test = (object) array('results' => array());
        $events = StaticEventManager::getInstance();
        $events->attach(
            'ZendTest\EventManager\TestAsset\ClassWithEvents', 
            'foo', 
            function ($e) use ($test) {
                $test->results[] = 'static';
            },
            10000 // high priority
        );
        $class = new TestAsset\ClassWithEvents();
        $class->getEventManager()->attach('foo', function ($e) use ($test) {
            $test->results[] = 'local';
        }, 1); // low priority
        $class->getEventManager()->attach('foo', function ($e) use ($test) {
            $test->results[] = 'local2';
        }, 1000); // medium priority
        $class->getEventManager()->attach('foo', function ($e) use ($test) {
            $test->results[] = 'local3';
        }, 15000); // highest priority
        $class->getEventManager()->setSharedManager($events);
        $class->foo();
        $this->assertEquals(array('local3', 'static', 'local2', 'local'), $test->results);
    }

    public function testCallingUnsetSharedManagerDisablesStaticManager()
    {
        $counter = (object) array('count' => 0);
        StaticEventManager::getInstance()->attach(
            'ZendTest\EventManager\TestAsset\ClassWithEvents', 
            'foo', 
            function ($e) use ($counter) {
                $counter->count++;
            }
        );
        $class = new TestAsset\ClassWithEvents();
        $class->getEventManager()->unsetSharedManager();
        $class->foo();
        $this->assertEquals(0, $counter->count);
    }

    public function testCanPassAlternateStaticConnectionsHolder()
    {
        $counter = (object) array('count' => 0);
        StaticEventManager::getInstance()->attach(
            'ZendTest\EventManager\TestAsset\ClassWithEvents', 
            'foo', 
            function ($e) use ($counter) {
                $counter->count++;
            }
        );
        $mockStaticEvents = new TestAsset\StaticEventsMock();
        $class = new TestAsset\ClassWithEvents();
        $class->getEventManager()->setSharedManager($mockStaticEvents);
        $this->assertSame($mockStaticEvents, $class->getEventManager()->getSharedManager());
        $class->foo();
        $this->assertEquals(0, $counter->count);
    }

    public function testTriggerMergesPrioritiesOfStaticAndInstanceListeners()
    {
        $test = (object) array('results' => array());
        $events = StaticEventManager::getInstance();
        $events->attach(
            'ZendTest\EventManager\TestAsset\ClassWithEvents', 
            'foo', 
            function ($e) use ($test) {
                $test->results[] = 'static';
            },
            100
        );
        $class = new TestAsset\ClassWithEvents();
        $class->getEventManager()->attach('foo', function ($e) use ($test) {
            $test->results[] = 'local';
        }, -100);
        $class->getEventManager()->setSharedManager($events);
        $class->foo();
        $this->assertEquals(array('static', 'local'), $test->results);
    }
}

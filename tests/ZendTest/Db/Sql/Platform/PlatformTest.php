<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Sql\Platform;

use ReflectionMethod;
use ZendTest\Db\TestAsset;
use Zend\Db\Sql\Platform\Platform;
use Zend\Db\Adapter\Adapter;

/**
 * Tests for {@see \ZendTest\Db\Sql\Platform\AbstractPlatform}
 *
 * @covers \ZendTest\Db\Sql\Platform\AbstractPlatform
 */
class PlatformTest extends \PHPUnit_Framework_TestCase
{

    public function testResolveDefaultPlatform()
    {
        $adapter = $this->resolveAdapter('sql92');
        $platform = new Platform($adapter);

        $reflectionMethod = new ReflectionMethod($platform, 'resolvePlatform');

        $reflectionMethod->setAccessible(true);

        $this->assertEquals($adapter->getPlatform(), $reflectionMethod->invoke($platform, null));
    }

    public function testResolvePlatformName()
    {
        $platform = new Platform($this->resolveAdapter('sql92'));

        $reflectionMethod = new ReflectionMethod($platform, 'resolvePlatformName');

        $reflectionMethod->setAccessible(true);

        $this->assertEquals('mysql', $reflectionMethod->invoke($platform, new TestAsset\TrustingMysqlPlatform()));
        $this->assertEquals('sqlserver', $reflectionMethod->invoke($platform, new TestAsset\TrustingSqlServerPlatform()));
        $this->assertEquals('oracle', $reflectionMethod->invoke($platform, new TestAsset\TrustingOraclePlatform()));
        $this->assertEquals('sql92', $reflectionMethod->invoke($platform, new TestAsset\TrustingSql92Platform()));
    }

    /**
     * @group 6890
     */
    public function testAbstractPlatformCrashesGracefullyOnMissingDefaultPlatform()
    {
        $adapter = $this->resolveAdapter('sql92');
        $reflectionProperty = new \ReflectionProperty($adapter, 'platform');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($adapter, null);

        $platform = new Platform($adapter);
        $reflectionMethod = new ReflectionMethod($platform, 'resolvePlatform');

        $reflectionMethod->setAccessible(true);

        $this->setExpectedException('Zend\Db\Sql\Exception\RuntimeException', '$this->defaultPlatform was not set');

        $reflectionMethod->invoke($platform, null);
    }

    /**
     * @group 6890
     */
    public function testAbstractPlatformCrashesGracefullyOnMissingDefaultPlatformWithGetDecorators()
    {

        $adapter = $this->resolveAdapter('sql92');
        $reflectionProperty = new \ReflectionProperty($adapter, 'platform');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($adapter, null);

        /* @var $platform \Zend\Db\Sql\Platform\AbstractPlatform */
        $platform = new Platform($adapter);
        $reflectionMethod = new ReflectionMethod($platform, 'resolvePlatform');

        $reflectionMethod->setAccessible(true);

        $this->setExpectedException('Zend\Db\Sql\Exception\RuntimeException', '$this->defaultPlatform was not set');

        $platform->getDecorators();
    }

    protected function resolveAdapter($platform)
    {
        switch ($platform) {
            case 'sql92'     : $platform  = new TestAsset\TrustingSql92Platform();     break;
            case 'MySql'     : $platform  = new TestAsset\TrustingMysqlPlatform();     break;
            case 'Oracle'    : $platform  = new TestAsset\TrustingOraclePlatform();    break;
            case 'SqlServer' : $platform  = new TestAsset\TrustingSqlServerPlatform(); break;
            default : $platform = null;
        }

        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnCallback(function () {return new Adapter\StatementContainer;}));

        return new Adapter($mockDriver, $platform);
    }
}

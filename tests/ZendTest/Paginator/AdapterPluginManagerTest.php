<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Paginator;

use Zend\Stdlib\CallbackHandler;
use Zend\Db\Adapter\Platform\Sql92;
use Zend\Paginator\AdapterPluginManager;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;

/**
 * @group      Zend_Paginator
 */
class AdapterPluginManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $adapaterPluginManager;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $mockSelect;

    protected $mockAdapter;

    protected function setUp()
    {
        $this->adapaterPluginManager = new AdapterPluginManager();
        $this->mockSelect = $this->getMock('Zend\Db\Sql\Select');

        $mockStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $mockResult = $this->getMock('Zend\Db\Adapter\Driver\ResultInterface');

        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($mockStatement));
        $mockStatement->expects($this->any())->method('execute')->will($this->returnValue($mockResult));
        $mockPlatform = $this->getMock('Zend\Db\Adapter\Platform\PlatformInterface');
        $mockPlatform->expects($this->any())->method('getName')->will($this->returnValue('platform'));

        $this->mockAdapter = $this->getMockForAbstractClass(
            'Zend\Db\Adapter\Adapter',
            array($mockDriver, $mockPlatform)
        );
    }

    public function testCanRetrieveAdapterPlugin()
    {
        $plugin = $this->adapaterPluginManager->get('array', array(1, 2, 3));
        $this->assertInstanceOf('Zend\Paginator\Adapter\ArrayAdapter', $plugin);
        $plugin = $this->adapaterPluginManager->get('iterator', new \ArrayIterator(range(1, 101)));
        $this->assertInstanceOf('Zend\Paginator\Adapter\Iterator', $plugin);
        $plugin = $this->adapaterPluginManager->get('dbselect', array($this->mockSelect, $this->mockAdapter));
        $this->assertInstanceOf('Zend\Paginator\Adapter\DbSelect', $plugin);
        $plugin = $this->adapaterPluginManager->get('null', 101);
        $this->assertInstanceOf('Zend\Paginator\Adapter\Null', $plugin);

        //test dbtablegateway
        $mockStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())
                   ->method('createStatement')
                   ->will($this->returnValue($mockStatement));
        $mockDriver->expects($this->any())
            ->method('formatParameterName')
            ->will($this->returnArgument(0));
        $mockAdapter = $this->getMockForAbstractClass(
            'Zend\Db\Adapter\Adapter',
            array($mockDriver, new Sql92())
        );
        $mockTableGateway = $this->getMockForAbstractClass(
            'Zend\Db\TableGateway\TableGateway',
            array('foobar', $mockAdapter)
        );
        $where  = "foo = bar";
        $order  = "foo";
        $group  = "foo";
        $having = "count(foo)>0";
        $plugin = $this->adapaterPluginManager->get('dbtablegateway', array($mockTableGateway, $where, $order, $group, $having));
        $this->assertInstanceOf('Zend\Paginator\Adapter\DbTableGateway', $plugin);

        //test callback
        $itemsCallback = new CallbackHandler(function () {
            return array();
        });
        $countCallback = new CallbackHandler(function () {
            return 0;
        });
        $plugin = $this->adapaterPluginManager->get('callback', array($itemsCallback, $countCallback));
        $this->assertInstanceOf('Zend\Paginator\Adapter\Callback', $plugin);
    }

    public function testCanRetrievePluginManagerWithServiceManager()
    {
        $sm = $this->serviceManager = new ServiceManager(
            new ServiceManagerConfig(array(
                'factories' => array(
                    'PaginatorPluginManager'  => 'Zend\Mvc\Service\PaginatorPluginManagerFactory',
                ),
            ))
        );
        $sm->setService('Config', array());
        $adapterPluginManager = $sm->get('PaginatorPluginManager');
        $this->assertInstanceOf('Zend\Paginator\AdapterPluginManager', $adapterPluginManager);
    }
}

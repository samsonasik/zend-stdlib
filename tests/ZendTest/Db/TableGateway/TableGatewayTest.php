<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\TableGateway;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\TableIdentifier;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-03-01 at 21:02:22.
 */
class TableGatewayTest extends \PHPUnit_Framework_TestCase
{
    protected $mockAdapter = null;

    public function setup()
    {
        // mock the adapter, driver, and parts
        $mockResult = $this->getMock('Zend\Db\Adapter\Driver\ResultInterface');
        $mockStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $mockStatement->expects($this->any())->method('execute')->will($this->returnValue($mockResult));
        $mockConnection = $this->getMock('Zend\Db\Adapter\Driver\ConnectionInterface');
        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($mockStatement));
        $mockDriver->expects($this->any())->method('getConnection')->will($this->returnValue($mockConnection));

        // setup mock adapter
        $this->mockAdapter = $this->getMock('Zend\Db\Adapter\Adapter', null, array($mockDriver));
    }

    /**
     * Beside other tests checks for plain string table identifier
     */
    public function test__construct()
    {
        // constructor with only required args
        $table = new TableGateway(
            'foo',
            $this->mockAdapter
        );

        $this->assertEquals('foo', $table->getTable());
        $this->assertSame($this->mockAdapter, $table->getAdapter());
        $this->assertInstanceOf('Zend\Db\TableGateway\Feature\FeatureSet', $table->getFeatureSet());
        $this->assertInstanceOf('Zend\Db\ResultSet\ResultSet', $table->getResultSetPrototype());
        $this->assertInstanceOf('Zend\Db\Sql\Sql', $table->getSql());

        // injecting all args
        $table = new TableGateway(
            'foo',
            $this->mockAdapter,
            $featureSet = new Feature\FeatureSet,
            $resultSet = new ResultSet,
            $sql = new Sql($this->mockAdapter, 'foo')
        );

        $this->assertEquals('foo', $table->getTable());
        $this->assertSame($this->mockAdapter, $table->getAdapter());
        $this->assertSame($featureSet, $table->getFeatureSet());
        $this->assertSame($resultSet, $table->getResultSetPrototype());
        $this->assertSame($sql, $table->getSql());

        // constructor expects exception
        $this->setExpectedException(
            'Zend\Db\TableGateway\Exception\InvalidArgumentException',
            'Table name must be a string or an instance of Zend\Db\Sql\TableIdentifier'
        );
        new TableGateway(
            null,
            $this->mockAdapter
        );
    }

    /**
     * @group 6726
     * @group 6740
     */
    public function testTableAsString()
    {
        $ti = 'fooTable.barSchema';
        // constructor with only required args
        $table = new TableGateway(
            $ti,
            $this->mockAdapter
        );

        $this->assertEquals($ti, $table->getTable());
    }

    /**
     * @group 6726
     * @group 6740
     */
    public function testTableAsTableIdentifierObject()
    {
        $ti = new TableIdentifier('fooTable', 'barSchema');
        // constructor with only required args
        $table = new TableGateway(
            $ti,
            $this->mockAdapter
        );

        $this->assertEquals($ti, $table->getTable());
    }

    /**
     * @group 6726
     * @group 6740
     */
    public function testTableAsAliasedTableIdentifierObject()
    {
        $aliasedTI = array('foo' => new TableIdentifier('fooTable', 'barSchema'));
        // constructor with only required args
        $table = new TableGateway(
            $aliasedTI,
            $this->mockAdapter
        );

        $this->assertEquals($aliasedTI, $table->getTable());
    }
}

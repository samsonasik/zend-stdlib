<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */

namespace ZendTest\Test\PHPUnit\Db\DataSet;
use Zend\Test\PHPUnit\Db\DataSet;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @group      Zend_Test
 */
class QueryDataSetTest extends DataSetTestCase
{
    public function testCreateQueryDataSetWithoutZendDbAdapterThrowsException()
    {
        $connectionMock = $this->getMock('PHPUnit_Extensions_Database_DB_IDatabaseConnection');
        $this->setExpectedException('Zend\Test\PHPUnit\Db\Exception\InvalidArgumentException');
        $queryDataSet = new DataSet\QueryDataSet($connectionMock);
    }

    public function testCreateQueryDataSetWithZendDbAdapter()
    {
        $this->decorateConnectionMockWithZendAdapter();
        $queryDataSet = new DataSet\QueryDataSet($this->connectionMock);
    }

    public function testAddTableWithoutQueryParameterCreatesSelectWildcardAll()
    {
        $fixtureTableName = "foo";

        $adapterMock = $this->getMock('Zend\Test\DbAdapter');
        $selectMock = $this->getMock('Zend\Db\Select', array(), array($adapterMock));

        $adapterMock->expects($this->once())
                    ->method('select')
                    ->will($this->returnValue($selectMock));
        $this->decorateConnectionGetConnectionWith($adapterMock);

        $selectMock->expects($this->once())
                   ->method('from')
                   ->with($fixtureTableName, \Zend\Db\Select::SQL_WILDCARD);
        $selectMock->expects($this->once())
                   ->method('__toString')
                   ->will($this->returnValue('SELECT * FOM foo'));

        $queryDataSet = new DataSet\QueryDataSet($this->connectionMock);
        $queryDataSet->addTable('foo');
    }
}

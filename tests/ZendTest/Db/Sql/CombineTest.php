<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Sql;

use Zend\Db\Sql\Combine;
use Zend\Db\Sql\Select;

class CombineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Combine
     */
    protected $combine;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->combine = new Combine;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testGetSqlString()
    {
        $this->combine
                ->union(new Select('t1'))
                ->intersect(new Select('t2'))
                ->except(new Select('t3'))
                ->union(new Select('t4'));

        $this->assertEquals(
            '(SELECT "t1".* FROM "t1") INTERSECT (SELECT "t2".* FROM "t2") EXCEPT (SELECT "t3".* FROM "t3") UNION (SELECT "t4".* FROM "t4")',
            $this->combine->getSqlString()
        );
    }

    public function testGetSqlStringWithModifier()
    {
        $this->combine
                ->union(new Select('t1'))
                ->union(new Select('t2'), 'ALL');

        $this->assertEquals(
            '(SELECT "t1".* FROM "t1") UNION ALL (SELECT "t2".* FROM "t2")',
            $this->combine->getSqlString()
        );
    }

    public function testGetSqlStringFromArray()
    {
        $this->combine->combine(array(
            array(new Select('t1')),
            array(new Select('t2'), Combine::COMBINE_INTERSECT, 'ALL'),
            array(new Select('t3'), Combine::COMBINE_EXCEPT),
        ));

        $this->assertEquals(
            '(SELECT "t1".* FROM "t1") INTERSECT ALL (SELECT "t2".* FROM "t2") EXCEPT (SELECT "t3".* FROM "t3")',
            $this->combine->getSqlString()
        );

        $this->combine = new Combine();
        $this->combine->combine(array(
            new Select('t1'),
            new Select('t2'),
            new Select('t3'),
        ));

        $this->assertEquals(
            '(SELECT "t1".* FROM "t1") UNION (SELECT "t2".* FROM "t2") UNION (SELECT "t3".* FROM "t3")',
            $this->combine->getSqlString()
        );
    }

    public function testGetSqlStringEmpty()
    {
        $this->assertSame(
            null,
            $this->combine->getSqlString()
        );
    }

    public function testPrepareStatementWithModifier()
    {
        $select1 = new Select('t1');
        $select1->where(array('x1'=>10));
        $select2 = new Select('t2');
        $select2->where(array('x2'=>20));

        $this->combine->combine(array(
            $select1,
            $select2
        ));

        $adapter = new \Zend\Db\Adapter\Adapter(array('driver'=>'mysqli'));

        $statement = $this->combine->prepareStatement($adapter);
        $this->assertInstanceOf('Zend\Db\Adapter\StatementContainerInterface', $statement);
        $this->assertEquals(
            "(SELECT `t1`.* FROM `t1` WHERE `x1` = ?) UNION (SELECT `t2`.* FROM `t2` WHERE `x2` = ?)",
            $statement->getSql()
        );
    }

    public function testGetRawState()
    {
        $select = new Select('t1');
        $this->combine->combine($select);
        $this->assertSame(
            array(
                'combine' => array(
                    array(
                        'select'   => $select,
                        'type'     => Combine::COMBINE_UNION,
                        'modifier' => ''
                    ),
                ),
            ),
            $this->combine->getRawState()
        );
    }
}

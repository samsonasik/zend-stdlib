<?php
namespace ZendTest\Db\Sql;

use Zend\Db\Sql\Select,
    Zend\Db\Sql\Expression,
    Zend\Db\Sql\Where,
    Zend\Db\Sql\TableIdentifier,
    Zend\Db\Adapter\ParameterContainer,
    Zend\Db\Adapter\Platform\Sql92;

class SelectTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @testdox unit test: Test from() returns Select object (is chainable)
     * @covers Zend\Db\Sql\Select::from
     */
    public function testFrom()
    {
        $select = new Select;
        $return = $select->from('foo', 'bar');
        $this->assertSame($select, $return);
        return $return;
    }

    /**
     * @testdox unit test: Test getRawState() returns infromation populated via from()
     * @covers Zend\Db\Sql\Select::getRawState
     * @depends testFrom
     */
    public function testGetRawStateViaFrom(Select $select)
    {
        $this->assertEquals('foo', $select->getRawState('table'));
    }

    /**
     * @testdox unit test: Test columns() returns Select object (is chainable)
     * @covers Zend\Db\Sql\Select::columns
     */
    public function testColumns()
    {
        $select = new Select;
        $return = $select->columns(array('foo', 'bar'));
        $this->assertSame($select, $return);
        return $select;
    }

    /**
     * @testdox unit test: Test getRawState() returns information populated via columns()
     * @covers Zend\Db\Sql\Select::getRawState
     * @depends testColumns
     */
    public function testGetRawStateViaColumns(Select $select)
    {
        $this->assertEquals(array('foo', 'bar'), $select->getRawState('columns'));
    }

    /**
     * @testdox unit test: Test join() returns same Select object (is chainable)
     * @covers Zend\Db\Sql\Select::join
     */
    public function testJoin()
    {
        $select = new Select;
        $return = $select->join('foo', 'x = y', Select::SQL_STAR, Select::JOIN_INNER);
        $this->assertSame($select, $return);
        return $return;
    }

    /**
     * @testdox unit test: Test getRawState() returns information populated via join()
     * @covers Zend\Db\Sql\Select::getRawState
     * @depends testJoin
     */
    public function testGetRawStateViaJoin(Select $select)
    {
        $this->assertEquals(
            array(array(
                'name' => 'foo',
                'on' => 'x = y',
                'columns' => array(Select::SQL_STAR),
                'type' => Select::JOIN_INNER
            )),
            $select->getRawState('joins')
        );
    }

    /**
     * @testdox unit test: Test where() returns Select object (is chainable)
     * @covers Zend\Db\Sql\Select::where
     */
    public function testWhereReturnsSameSelectObject()
    {
        $select = new Select;
        $this->assertSame($select, $select->where('x = y'));
    }

    /**
     * @testdox unit test: Test where() will accept a string for the predicate to create an expression predicate
     * @covers Zend\Db\Sql\Select::where
     */
    public function testWhereArgument1IsString()
    {
        $select = new Select;
        $select->where('x = y');

        /** @var $where Where */
        $where = $select->getRawState('where');
        $predicates = $where->getPredicates();
        $this->assertEquals(1, count($predicates));
        $this->assertInstanceOf('Zend\Db\Sql\Predicate\Expression', $predicates[0][1]);
        $this->assertEquals(Where::OP_AND, $predicates[0][0]);
        $this->assertEquals('x = y', $predicates[0][1]->getExpression());
    }

    /**
     * @testdox unit test: Test where() will accept an array with a string key (containing ?) used as an expression with placeholder
     * @covers Zend\Db\Sql\Select::where
     */
    public function testWhereArgument1IsAssociativeArrayContainingReplacementCharacter()
    {
        $select = new Select;
        $select->where(array('foo > ?' => 5));

        /** @var $where Where */
        $where = $select->getRawState('where');
        $predicates = $where->getPredicates();
        $this->assertEquals(1, count($predicates));
        $this->assertInstanceOf('Zend\Db\Sql\Predicate\Expression', $predicates[0][1]);
        $this->assertEquals(Where::OP_AND, $predicates[0][0]);
        $this->assertEquals('foo > ?', $predicates[0][1]->getExpression());
        $this->assertEquals(array(5), $predicates[0][1]->getParameters());
    }

    /**
     * @testdox unit test: Test where() will accept any array with string key (without ?) to be used as Operator predicate
     * @covers Zend\Db\Sql\Select::where
     */
    public function testWhereArugment1IsAssociativeArrayNotContainingReplacementCharacter()
    {
        $select = new Select;
        $select->where(array('name' => 'Ralph', 'age' => 33));

        /** @var $where Where */
        $where = $select->getRawState('where');
        $predicates = $where->getPredicates();
        $this->assertEquals(2, count($predicates));

        $this->assertInstanceOf('Zend\Db\Sql\Predicate\Operator', $predicates[0][1]);
        $this->assertEquals(Where::OP_AND, $predicates[0][0]);
        $this->assertEquals('name', $predicates[0][1]->getLeft());
        $this->assertEquals('Ralph', $predicates[0][1]->getRight());

        $this->assertInstanceOf('Zend\Db\Sql\Predicate\Operator', $predicates[1][1]);
        $this->assertEquals(Where::OP_AND, $predicates[1][0]);
        $this->assertEquals('age', $predicates[1][1]->getLeft());
        $this->assertEquals(33, $predicates[1][1]->getRight());
    }

    /**
     * @testdox unit test: Test where() will accept an indexed array to be used by joining string expressions
     * @covers Zend\Db\Sql\Select::where
     */
    public function testWhereArugment1IsIndexedArray()
    {
        $select = new Select;
        $select->where(array('name = "Ralph"'));

        /** @var $where Where */
        $where = $select->getRawState('where');
        $predicates = $where->getPredicates();
        $this->assertEquals(1, count($predicates));

        $this->assertInstanceOf('Zend\Db\Sql\Predicate\Expression', $predicates[0][1]);
        $this->assertEquals(Where::OP_AND, $predicates[0][0]);
        $this->assertEquals('name = "Ralph"', $predicates[0][1]->getExpression());
    }

    /**
     * @testdox unit test: Test where() will accept an indexed array to be used by joining string expressions, combined by OR
     * @covers Zend\Db\Sql\Select::where
     */
    public function testWhereArugment1IsIndexedArrayArgument2IsOr()
    {
        $select = new Select;
        $select->where(array('name = "Ralph"'), Where::OP_OR);

        /** @var $where Where */
        $where = $select->getRawState('where');
        $predicates = $where->getPredicates();
        $this->assertEquals(1, count($predicates));

        $this->assertInstanceOf('Zend\Db\Sql\Predicate\Expression', $predicates[0][1]);
        $this->assertEquals(Where::OP_OR, $predicates[0][0]);
        $this->assertEquals('name = "Ralph"', $predicates[0][1]->getExpression());
    }

    /**
     * @testdox unit test: Test where() will accept a closure to be executed with Where object as argument
     * @covers Zend\Db\Sql\Select::where
     */
    public function testWhereArugment1IsClosure()
    {
        $select = new Select;
        $where = $select->getRawState('where');

        $test = $this;
        $select->where(function ($what) use ($test, $where) {
            $test->assertSame($where, $what);
        });
    }

    /**
     * @testdox unit test: Test where() will accept a Where object
     * @covers Zend\Db\Sql\Select::where
     */
    public function testWhereArugment1IsWhereObject()
    {
        $select = new Select;
        $select->where($newWhere = new Where);
        $this->assertSame($newWhere, $select->getRawState('where'));
    }

    /**
     * @author Rob Allen
     * @testdox unit test: Test order()
     * @covers Zend\Db\Sql\Select::order
     */
    public function testOrder()
    {
        $select = new Select;
        $return = $select->order('id DESC');
        $this->assertSame($select, $return); // test fluent interface
        $this->assertEquals(array('id DESC'), $select->getRawState('order'));

        $select = new Select;
        $select->order('id DESC')
            ->order('name ASC, age DESC');
        $this->assertEquals(array('id DESC', 'name ASC', 'age DESC'), $select->getRawState('order'));

        $select = new Select;
        $select->order(array('name ASC', 'age DESC'));
        $this->assertEquals(array('name ASC', 'age DESC'), $select->getRawState('order'));

    }

    /**
     * @testdox unit test: Test prepareStatement() will produce expected sql and parameters based on a variety of provided arguments [uses data provider]
     * @covers Zend\Db\Sql\Select::prepareStatement
     * @dataProvider providerData
     */
    public function testPrepareStatement(Select $select, $expectedSqlString, $expectedParameters)
    {
        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockAdapter = $this->getMock('Zend\Db\Adapter\Adapter', null, array($mockDriver));

        $parameterContainer = new ParameterContainer();

        $mockStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $mockStatement->expects($this->any())->method('getParameterContainer')->will($this->returnValue($parameterContainer));
        $mockStatement->expects($this->any())->method('setSql')->with($this->equalTo($expectedSqlString));

        $select->prepareStatement($mockAdapter, $mockStatement);

        if ($expectedParameters) {
            $this->assertEquals($expectedParameters, $parameterContainer->getNamedArray());
        }
    }

    /**
     * @testdox unit test: Test getSqlString() will produce expected sql and parameters based on a variety of provided arguments [uses data provider]
     * @covers Zend\Db\Sql\Select::getSqlString
     * @dataProvider providerData
     */
    public function testGetSqlString(Select $select, $unused, $unused2, $expectedSqlString)
    {
        $this->assertEquals($expectedSqlString, $select->getSqlString());
    }

    /**
     * @testdox unit test: Test __get() returns expected objects magically
     * @covers Zend\Db\Sql\Select::__get
     */
    public function test__get()
    {
        $select = new Select;
        $this->assertInstanceOf('Zend\Db\Sql\Where', $select->where);
    }

    /**
     * @testdox unit test: Test __clone() will clone the where object so that this select can be used in multiple contexts
     * @covers Zend\Db\Sql\Select::__clone
     */
    public function test__clone()
    {
        $select = new Select;
        $select1 = clone $select;
        $select1->where('id = foo');

        $this->assertEquals(0, $select->where->count());
        $this->assertEquals(1, $select1->where->count());
    }

    /**
     * @testdox unit test: Text process*() methods will return proper array when internally called, part of extension API
     * @dataProvider providerData
     * @covers Zend\Db\Sql\Select::processSelect
     */
    public function testProcessMethods(Select $select, $unused, $unused2, $unused3, $internalTests)
    {
        if (!$internalTests) {
            return;
        }

        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockAdapter = $this->getMock('Zend\Db\Adapter\Adapter', null, array($mockDriver));
        $parameterContainer = new ParameterContainer();

        $sr = new \ReflectionObject($select);

        foreach ($internalTests as $method => $expected) {
            $mr = $sr->getMethod($method);
            $mr->setAccessible(true);
            $return = $mr->invokeArgs($select, array(new Sql92, $mockAdapter, $parameterContainer));
            $this->assertEquals($expected, $return);
        }
    }

    public function providerData()
    {
        // basic table
        $select0 = new Select;
        $select0->from('foo');
        $sqlPrep0 = // same
        $sqlStr0 = 'SELECT "foo".* FROM "foo"';
        $internalTests0 = array(
            'processSelect' => array(array(array('"foo".*')), '"foo"')
        );

        // table as TableIdentifier
        $select1 = new Select();
        $select1->from(new TableIdentifier('foo', 'bar'));
        $sqlPrep1 = // same
        $sqlStr1 = 'SELECT "bar"."foo".* FROM "bar"."foo"';
        $internalTests1 = array(
            'processSelect' => array(array(array('"bar"."foo".*')), '"bar"."foo"')
        );

        // columns
        $select2 = new Select;
        $select2->from('foo')->columns(array('bar', 'baz'));
        $sqlPrep2 = // same
        $sqlStr2 = 'SELECT "foo"."bar" AS "bar", "foo"."baz" AS "baz" FROM "foo"';
        $internalTests2 = array(
            'processSelect' => array(array(array('"foo"."bar"', '"bar"'), array('"foo"."baz"', '"baz"')), '"foo"')
        );

        // columns with AS associative array
        $select3 = new Select;
        $select3->from('foo')->columns(array('bar' => 'baz'));
        $sqlPrep3 = // same
        $sqlStr3 = 'SELECT "foo"."baz" AS "bar" FROM "foo"';
        $internalTests3 = array(
            'processSelect' => array(array(array('"foo"."baz"', '"bar"')), '"foo"')
        );

        // columns with AS associative array mixed
        $select4 = new Select;
        $select4->from('foo')->columns(array('bar' => 'baz', 'bam'));
        $sqlPrep4 = // same
        $sqlStr4 = 'SELECT "foo"."baz" AS "bar", "foo"."bam" AS "bam" FROM "foo"';
        $internalTests4 = array(
            'processSelect' => array(array(array('"foo"."baz"', '"bar"'), array('"foo"."bam"', '"bam"') ), '"foo"')
        );

        // columns where value is Expression, with AS
        $select5 = new Select;
        $select5->from('foo')->columns(array('bar' => new Expression('COUNT(some_column)')));
        $sqlPrep5 = // same
        $sqlStr5 = 'SELECT COUNT(some_column) AS "bar" FROM "foo"';
        $internalTests5 = array(
            'processSelect' => array(array(array('COUNT(some_column)', '"bar"')), '"foo"')
        );

        // columns where value is Expression
        $select6 = new Select;
        $select6->from('foo')->columns(array(new Expression('COUNT(some_column) AS bar')));
        $sqlPrep6 = // same
        $sqlStr6 = 'SELECT COUNT(some_column) AS bar FROM "foo"';
        $internalTests6 = array(
            'processSelect' => array(array(array('COUNT(some_column) AS bar')), '"foo"')
        );

        // columns where value is Expression with parameters
        $select7 = new Select;
        $select7->from('foo')->columns(
            array(
                new Expression(
                    '(COUNT(?) + ?) AS ?',
                    array('some_column', 5, 'bar'),
                    array(Expression::TYPE_IDENTIFIER, Expression::TYPE_VALUE, Expression::TYPE_IDENTIFIER)
                )
            )
        );
        $sqlPrep7 = 'SELECT (COUNT("some_column") + ?) AS "bar" FROM "foo"';
        $sqlStr7 = 'SELECT (COUNT("some_column") + \'5\') AS "bar" FROM "foo"';
        $params7 = array('column1' => 5);
        $internalTests7 = array(
            'processSelect' => array(array(array('(COUNT("some_column") + ?) AS "bar"')), '"foo"')
        );

        // joins (plain)
        $select8 = new Select;
        $select8->from('foo')->join('zac', 'm = n');
        $sqlPrep8 = // same
        $sqlStr8 = 'SELECT "foo".*, "zac".* FROM "foo" INNER JOIN "zac" ON "m" = "n"';
        $internalTests8 = array(
            'processSelect' => array(array(array('"foo".*'), array('"zac".*')), '"foo"'),
            'processJoin'   => array(array(array('INNER', '"zac"', '"m" = "n"')))
        );

        // join with columns
        $select9 = new Select;
        $select9->from('foo')->join('zac', 'm = n', array('bar', 'baz'));
        $sqlPrep9 = // same
        $sqlStr9 = 'SELECT "foo".*, "zac"."bar" AS "bar", "zac"."baz" AS "baz" FROM "foo" INNER JOIN "zac" ON "m" = "n"';
        $internalTests9 = array(
            'processSelect' => array(array(array('"foo".*'), array('"zac"."bar"', '"bar"'), array('"zac"."baz"', '"baz"')), '"foo"'),
            'processJoin'   => array(array(array('INNER', '"zac"', '"m" = "n"')))
        );

        // join with alternate type
        $select10 = new Select;
        $select10->from('foo')->join('zac', 'm = n', array('bar', 'baz'), Select::JOIN_OUTER);
        $sqlPrep10 = // same
        $sqlStr10 = 'SELECT "foo".*, "zac"."bar" AS "bar", "zac"."baz" AS "baz" FROM "foo" OUTER JOIN "zac" ON "m" = "n"';
        $internalTests10 = array(
            'processSelect' => array(array(array('"foo".*'), array('"zac"."bar"', '"bar"'), array('"zac"."baz"', '"baz"')), '"foo"'),
            'processJoin'   => array(array(array('OUTER', '"zac"', '"m" = "n"')))
        );

        // join with column aliases
        $select11 = new Select;
        $select11->from('foo')->join('zac', 'm = n', array('BAR' => 'bar', 'BAZ' => 'baz'));
        $sqlPrep11 = // same
        $sqlStr11 = 'SELECT "foo".*, "zac"."bar" AS "BAR", "zac"."baz" AS "BAZ" FROM "foo" INNER JOIN "zac" ON "m" = "n"';
        $internalTests11 = array(
            'processSelect' => array(array(array('"foo".*'), array('"zac"."bar"', '"BAR"'), array('"zac"."baz"', '"BAZ"')), '"foo"'),
            'processJoin'   => array(array(array('INNER', '"zac"', '"m" = "n"')))
        );

        // join with table aliases
        $select25 = new Select;
        $select25->from('foo')->join(array('b' => 'bar'), 'b.foo_id = foo.foo_id');
        $sqlPrep25 = // same
        $sqlStr25 = 'SELECT "foo".*, "b".* FROM "foo" INNER JOIN "bar" "b" ON "b"."foo_id" = "foo"."foo_id"';
        $internalTests25 = array(
            'processSelect' => array(array(array('"foo".*'), array('"b".*')), '"foo"'),
            'processJoinAlias' => array(array(array('INNER', '"bar"', '"b"', '"b"."foo_id" = "foo"."foo_id"')))
        );

        // where (simple string)
        $select12 = new Select;
        $select12->from('foo')->where('x = 5');
        $sqlPrep12 = // same
        $sqlStr12 = 'SELECT "foo".* FROM "foo" WHERE x = 5';
        $internalTests12 = array(
            'processSelect' => array(array(array('"foo".*')), '"foo"'),
            'processWhere'  => array('x = 5')
        );

        // where (returning parameters)
        $select13 = new Select;
        $select13->from('foo')->where(array('x = ?' => 5));
        $sqlPrep13 = 'SELECT "foo".* FROM "foo" WHERE x = ?';
        $sqlStr13 = 'SELECT "foo".* FROM "foo" WHERE x = \'5\'';
        $params13 = array('where1' => 5);
        $internalTests13 = array(
            'processSelect' => array(array(array('"foo".*')), '"foo"'),
            'processWhere'  => array('x = ?')
        );

        // group
        $select14 = new Select;
        $select14->from('foo')->group(array('col1', 'col2'));
        $sqlPrep14 = // same
        $sqlStr14 = 'SELECT "foo".* FROM "foo" GROUP BY "col1", "col2"';
        $internalTests14 = array(
            'processSelect' => array(array(array('"foo".*')), '"foo"'),
            'processGroup'  => array(array('"col1"', '"col2"'))
        );

        $select15 = new Select;
        $select15->from('foo')->group('col1')->group('col2');
        $sqlPrep15 = // same
        $sqlStr15 = 'SELECT "foo".* FROM "foo" GROUP BY "col1", "col2"';
        $internalTests15 = array(
            'processSelect' => array(array(array('"foo".*')), '"foo"'),
            'processGroup'  => array(array('"col1"', '"col2"'))
        );

        $select16 = new Select;
        $select16->from('foo')->group(new Expression('DAY(?)', array('col1'), array(Expression::TYPE_IDENTIFIER)));
        $sqlPrep16 = // same
        $sqlStr16 = 'SELECT "foo".* FROM "foo" GROUP BY DAY("col1")';
        $internalTests16 = array(
            'processSelect' => array(array(array('"foo".*')), '"foo"'),
            'processGroup'  => array(array('DAY("col1")'))
        );

        // having (simple string)
        $select17 = new Select;
        $select17->from('foo')->having('x = 5');
        $sqlPrep17 = // same
        $sqlStr17 = 'SELECT "foo".* FROM "foo" HAVING x = 5';
        $internalTests17 = array(
            'processSelect' => array(array(array('"foo".*')), '"foo"'),
            'processHaving'  => array('x = 5')
        );

        // having (returning parameters)
        $select18 = new Select;
        $select18->from('foo')->having(array('x = ?' => 5));
        $sqlPrep18 = 'SELECT "foo".* FROM "foo" HAVING x = ?';
        $sqlStr18 = 'SELECT "foo".* FROM "foo" HAVING x = \'5\'';
        $params18 = array('having1' => 5);
        $internalTests18 = array(
            'processSelect' => array(array(array('"foo".*')), '"foo"'),
            'processHaving'  => array('x = ?')
        );

        // order
        $select19 = new Select;
        $select19->from('foo')->order('c1');
        $sqlPrep19 = //
        $sqlStr19 = 'SELECT "foo".* FROM "foo" ORDER BY "c1" ASC';
        $internalTests19 = array(
            'processSelect' => array(array(array('"foo".*')), '"foo"'),
            'processOrder'  => array(array(array('"c1"', Select::ORDER_ASCENDING)))
        );

        $select20 = new Select;
        $select20->from('foo')->order(array('c1', 'c2'));
        $sqlPrep20 = // same
        $sqlStr20 = 'SELECT "foo".* FROM "foo" ORDER BY "c1" ASC, "c2" ASC';
        $internalTests20 = array(
            'processSelect' => array(array(array('"foo".*')), '"foo"'),
            'processOrder'  => array(array(array('"c1"', Select::ORDER_ASCENDING), array('"c2"', Select::ORDER_ASCENDING)))
        );

        $select21 = new Select;
        $select21->from('foo')->order(array('c1' => 'DESC', 'c2' => 'Asc')); // notice partially lower case ASC
        $sqlPrep21 = // same
        $sqlStr21 = 'SELECT "foo".* FROM "foo" ORDER BY "c1" DESC, "c2" ASC';
        $internalTests21 = array(
            'processSelect' => array(array(array('"foo".*')), '"foo"'),
            'processOrder'  => array(array(array('"c1"', Select::ORDER_DESENDING), array('"c2"', Select::ORDER_ASCENDING)))
        );

        $select22 = new Select;
        $select22->from('foo')->order(array('c1' => 'asc'))->order('c2 desc'); // notice partially lower case ASC
        $sqlPrep22 = // same
        $sqlStr22 = 'SELECT "foo".* FROM "foo" ORDER BY "c1" ASC, "c2" DESC';
        $internalTests22 = array(
            'processSelect' => array(array(array('"foo".*')), '"foo"'),
            'processOrder'  => array(array(array('"c1"', Select::ORDER_ASCENDING), array('"c2"', Select::ORDER_DESENDING)))
        );

        // limit
        $select23 = new Select;
        $select23->from('foo')->limit(5);
        $sqlPrep23 = 'SELECT "foo".* FROM "foo" LIMIT ?';
        $sqlStr23 = 'SELECT "foo".* FROM "foo" LIMIT \'5\'';
        $params23 = array('limit' => 5);
        $internalTests23 = array(
            'processSelect' => array(array(array('"foo".*')), '"foo"'),
            'processLimit'  => array('?')
        );

        // limit with offset
        $select24 = new Select;
        $select24->from('foo')->limit(5)->offset(10);
        $sqlPrep24 = 'SELECT "foo".* FROM "foo" LIMIT ? OFFSET ?';
        $sqlStr24 = 'SELECT "foo".* FROM "foo" LIMIT \'5\' OFFSET \'10\'';
        $params24 = array('limit' => 5, 'offset' => 10);
        $internalTests24 = array(
            'processSelect' => array(array(array('"foo".*')), '"foo"'),
            'processLimit'  => array('?'),
            'processOffset' => array('?')
        );

        /**
         * $select = the select object
         * $sqlPrep = the sql as a result of preparation
         * $params = the param container contents result of preparation
         * $sqlStr = the sql as a result of getting a string back
         * $internalTests what the internal functions should return (safe-guarding extension)
         */
        return array(
            //    $select    $sqlPrep    $params     $sqlStr    $internalTests
            array($select0,  $sqlPrep0,  array(),    $sqlStr0,  $internalTests0),
            array($select1,  $sqlPrep1,  array(),    $sqlStr1,  $internalTests1),
            array($select2,  $sqlPrep2,  array(),    $sqlStr2,  $internalTests2),
            array($select3,  $sqlPrep3,  array(),    $sqlStr3,  $internalTests3),
            array($select4,  $sqlPrep4,  array(),    $sqlStr4,  $internalTests4),
            array($select5,  $sqlPrep5,  array(),    $sqlStr5,  $internalTests5),
            array($select6,  $sqlPrep6,  array(),    $sqlStr6,  $internalTests6),
            array($select7,  $sqlPrep7,  $params7,   $sqlStr7,  $internalTests7),
            array($select8,  $sqlPrep8,  array(),    $sqlStr8,  $internalTests8),
            array($select9,  $sqlPrep9,  array(),    $sqlStr9,  $internalTests9),
            array($select10, $sqlPrep10, array(),    $sqlStr10, $internalTests10),
            array($select11, $sqlPrep11, array(),    $sqlStr11, $internalTests11),
            array($select12, $sqlPrep12, array(),    $sqlStr12, $internalTests12),
            array($select13, $sqlPrep13, $params13,  $sqlStr13, $internalTests13),
            array($select14, $sqlPrep14, array(),    $sqlStr14, $internalTests14),
            array($select15, $sqlPrep15, array(),    $sqlStr15, $internalTests15),
            array($select16, $sqlPrep16, array(),    $sqlStr16, $internalTests16),
            array($select17, $sqlPrep17, array(),    $sqlStr17, $internalTests17),
            array($select18, $sqlPrep18, $params18,  $sqlStr18, $internalTests18),
            array($select19, $sqlPrep19, array(),    $sqlStr19, $internalTests19),
            array($select20, $sqlPrep20, array(),    $sqlStr20, $internalTests20),
            array($select21, $sqlPrep21, array(),    $sqlStr21, $internalTests21),
            array($select22, $sqlPrep22, array(),    $sqlStr22, $internalTests22),
            array($select23, $sqlPrep23, $params23,  $sqlStr23, $internalTests23),
            array($select24, $sqlPrep24, $params24,  $sqlStr24, $internalTests24),
            array($select25, $sqlPrep25, array(),    $sqlStr25, $internalTests25),
        );
    }

}

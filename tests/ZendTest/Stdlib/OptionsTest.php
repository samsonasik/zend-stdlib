<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib;

use ArrayObject;
use ZendTest\Stdlib\TestAsset\TestOptions;
use ZendTest\Stdlib\TestAsset\TestOptionsDerived;
use ZendTest\Stdlib\TestAsset\TestOptionsNoStrict;
use Zend\Stdlib\Exception\InvalidArgumentException;

class OptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructionWithArray()
    {
        $options = new TestOptions(array('test_field' => 1));

        $this->assertEquals(1, $options->test_field);
    }

    public function testConstructionWithTraversable()
    {
        $config = new ArrayObject(array('test_field' => 1));
        $options = new TestOptions($config);

        $this->assertEquals(1, $options->test_field);
    }

    public function testConstructionWithOptions()
    {
        $options = new TestOptions(new TestOptions(array('test_field' => 1)));

        $this->assertEquals(1, $options->test_field);
    }

    public function testInvalidFieldThrowsException()
    {
        $this->setExpectedException('BadMethodCallException');
        $options = new TestOptions(array('foo' => 'bar'));
    }

    public function testNonStrictOptionsDoesNotThrowException()
    {
        try {
            $options = new TestOptionsNoStrict(array('foo' => 'bar'));
        } catch (\Exception $e) {
            $this->fail('Nonstrict options should not throw an exception');
        }
    }

    public function testConstructionWithNull()
    {
        try {
            $options = new TestOptions(null);
        } catch (InvalidArgumentException $e) {
            $this->fail("Unexpected InvalidArgumentException raised");
        }
    }

    public function testUnsetting()
    {
        $options = new TestOptions(array('test_field' => 1));

        $this->assertEquals(true, isset($options->test_field));
        unset($options->testField);
        $this->assertEquals(false, isset($options->test_field));
    }

    public function testUnsetThrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $options = new TestOptions;
        unset($options->foobarField);
    }

    public function testGetThrowsBadMethodCallException()
    {
        $this->setExpectedException('BadMethodCallException');
        $options = new TestOptions();
        $options->fieldFoobar;
    }

    public function testSetFromArrayAcceptsArray()
    {
        $array = array('test_field' => 3);
        $options = new TestOptions();

        $this->assertSame($options, $options->setFromArray($array));
        $this->assertEquals(3, $options->test_field);
    }

    public function testSetFromArrayThrowsInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $options = new TestOptions;
        $options->setFromArray('asd');
    }

    public function testParentPublicProperty()
    {
        $options = new TestOptionsDerived(array('parent_public' => 1));

        $this->assertEquals(1, $options->parent_public);
    }

    public function testParentProtectedProperty()
    {
        $options = new TestOptionsDerived(array('parent_protected' => 1));

        $this->assertEquals(1, $options->parent_protected);
    }

    public function testParentPrivateProperty()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\BadMethodCallException');
        $options = new TestOptionsDerived(array('parent_private' => 1));
    }

    public function testDerivedPublicProperty()
    {
        $options = new TestOptionsDerived(array('derived_public' => 1));

        $this->assertEquals(1, $options->derived_public);
    }

    public function testDerivedProtectedProperty()
    {
        $options = new TestOptionsDerived(array('derived_protected' => 1));

        $this->assertEquals(1, $options->derived_protected);
    }

    public function testDerivedPrivateProperty()
    {
        $options = new TestOptionsDerived(array('derived_private' => 1));

        $this->assertEquals(1, $options->derived_private);
    }
}

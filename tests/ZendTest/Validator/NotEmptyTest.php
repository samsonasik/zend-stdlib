<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use stdClass;
use Zend\Validator\NotEmpty;

/**
 * @group      Zend_Validator
 */
class NotEmptyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NotEmpty
     */
    protected $validator;

    public function setUp()
    {
        $this->validator = new NotEmpty();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * ZF-6708 introduces a change for validating integer 0; it is a valid
     * integer value. '0' is also valid.
     *
     * @group ZF-6708
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array('word', true),
            array('', false),
            array('    ', false),
            array('  word  ', true),
            array('0', true),
            array(1, true),
            array(0, true),
            array(true, true),
            array(false, false),
            array(null, false),
            array(array(), false),
            array(array(5), true),
            array(0.0, false),
            array(1.0, true),
            array(new stdClass(), true),
        );
        foreach ($valuesExpected as $i => $element) {
                $this->assertEquals($element[1], $this->validator->isValid($element[0]),
                "Failed test #$i");
        }
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyBoolean()
    {
        $this->validator->setType(NotEmpty::BOOLEAN);
        $this->assertFalse($this->validator->isValid(false));
        $this->assertTrue($this->validator->isValid(true));
        $this->assertTrue($this->validator->isValid(0));
        $this->assertTrue($this->validator->isValid(1));
        $this->assertTrue($this->validator->isValid(0.0));
        $this->assertTrue($this->validator->isValid(1.0));
        $this->assertTrue($this->validator->isValid(''));
        $this->assertTrue($this->validator->isValid('abc'));
        $this->assertTrue($this->validator->isValid('0'));
        $this->assertTrue($this->validator->isValid('1'));
        $this->assertTrue($this->validator->isValid(array()));
        $this->assertTrue($this->validator->isValid(array('xxx')));
        $this->assertTrue($this->validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyInteger()
    {
        $this->validator->setType(NotEmpty::INTEGER);
        $this->assertTrue($this->validator->isValid(false));
        $this->assertTrue($this->validator->isValid(true));
        $this->assertFalse($this->validator->isValid(0));
        $this->assertTrue($this->validator->isValid(1));
        $this->assertTrue($this->validator->isValid(0.0));
        $this->assertTrue($this->validator->isValid(1.0));
        $this->assertTrue($this->validator->isValid(''));
        $this->assertTrue($this->validator->isValid('abc'));
        $this->assertTrue($this->validator->isValid('0'));
        $this->assertTrue($this->validator->isValid('1'));
        $this->assertTrue($this->validator->isValid(array()));
        $this->assertTrue($this->validator->isValid(array('xxx')));
        $this->assertTrue($this->validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyFloat()
    {
        $this->validator->setType(NotEmpty::FLOAT);
        $this->assertTrue($this->validator->isValid(false));
        $this->assertTrue($this->validator->isValid(true));
        $this->assertTrue($this->validator->isValid(0));
        $this->assertTrue($this->validator->isValid(1));
        $this->assertFalse($this->validator->isValid(0.0));
        $this->assertTrue($this->validator->isValid(1.0));
        $this->assertTrue($this->validator->isValid(''));
        $this->assertTrue($this->validator->isValid('abc'));
        $this->assertTrue($this->validator->isValid('0'));
        $this->assertTrue($this->validator->isValid('1'));
        $this->assertTrue($this->validator->isValid(array()));
        $this->assertTrue($this->validator->isValid(array('xxx')));
        $this->assertTrue($this->validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyString()
    {
        $this->validator->setType(NotEmpty::STRING);
        $this->assertTrue($this->validator->isValid(false));
        $this->assertTrue($this->validator->isValid(true));
        $this->assertTrue($this->validator->isValid(0));
        $this->assertTrue($this->validator->isValid(1));
        $this->assertTrue($this->validator->isValid(0.0));
        $this->assertTrue($this->validator->isValid(1.0));
        $this->assertFalse($this->validator->isValid(''));
        $this->assertTrue($this->validator->isValid('abc'));
        $this->assertTrue($this->validator->isValid('0'));
        $this->assertTrue($this->validator->isValid('1'));
        $this->assertTrue($this->validator->isValid(array()));
        $this->assertTrue($this->validator->isValid(array('xxx')));
        $this->assertTrue($this->validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyZero()
    {
        $this->validator->setType(NotEmpty::ZERO);
        $this->assertTrue($this->validator->isValid(false));
        $this->assertTrue($this->validator->isValid(true));
        $this->assertTrue($this->validator->isValid(0));
        $this->assertTrue($this->validator->isValid(1));
        $this->assertTrue($this->validator->isValid(0.0));
        $this->assertTrue($this->validator->isValid(1.0));
        $this->assertTrue($this->validator->isValid(''));
        $this->assertTrue($this->validator->isValid('abc'));
        $this->assertFalse($this->validator->isValid('0'));
        $this->assertTrue($this->validator->isValid('1'));
        $this->assertTrue($this->validator->isValid(array()));
        $this->assertTrue($this->validator->isValid(array('xxx')));
        $this->assertTrue($this->validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyArray()
    {
        $this->validator->setType(NotEmpty::EMPTY_ARRAY);
        $this->assertTrue($this->validator->isValid(false));
        $this->assertTrue($this->validator->isValid(true));
        $this->assertTrue($this->validator->isValid(0));
        $this->assertTrue($this->validator->isValid(1));
        $this->assertTrue($this->validator->isValid(0.0));
        $this->assertTrue($this->validator->isValid(1.0));
        $this->assertTrue($this->validator->isValid(''));
        $this->assertTrue($this->validator->isValid('abc'));
        $this->assertTrue($this->validator->isValid('0'));
        $this->assertTrue($this->validator->isValid('1'));
        $this->assertFalse($this->validator->isValid(array()));
        $this->assertTrue($this->validator->isValid(array('xxx')));
        $this->assertTrue($this->validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyNull()
    {
        $this->validator->setType(NotEmpty::NULL);
        $this->assertTrue($this->validator->isValid(false));
        $this->assertTrue($this->validator->isValid(true));
        $this->assertTrue($this->validator->isValid(0));
        $this->assertTrue($this->validator->isValid(1));
        $this->assertTrue($this->validator->isValid(0.0));
        $this->assertTrue($this->validator->isValid(1.0));
        $this->assertTrue($this->validator->isValid(''));
        $this->assertTrue($this->validator->isValid('abc'));
        $this->assertTrue($this->validator->isValid('0'));
        $this->assertTrue($this->validator->isValid('1'));
        $this->assertTrue($this->validator->isValid(array()));
        $this->assertTrue($this->validator->isValid(array('xxx')));
        $this->assertFalse($this->validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyPHP()
    {
        $this->validator->setType(NotEmpty::PHP);
        $this->assertFalse($this->validator->isValid(false));
        $this->assertTrue($this->validator->isValid(true));
        $this->assertFalse($this->validator->isValid(0));
        $this->assertTrue($this->validator->isValid(1));
        $this->assertFalse($this->validator->isValid(0.0));
        $this->assertTrue($this->validator->isValid(1.0));
        $this->assertFalse($this->validator->isValid(''));
        $this->assertTrue($this->validator->isValid('abc'));
        $this->assertFalse($this->validator->isValid('0'));
        $this->assertTrue($this->validator->isValid('1'));
        $this->assertFalse($this->validator->isValid(array()));
        $this->assertTrue($this->validator->isValid(array('xxx')));
        $this->assertFalse($this->validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlySpace()
    {
        $this->validator->setType(NotEmpty::SPACE);
        $this->assertTrue($this->validator->isValid(false));
        $this->assertTrue($this->validator->isValid(true));
        $this->assertTrue($this->validator->isValid(0));
        $this->assertTrue($this->validator->isValid(1));
        $this->assertTrue($this->validator->isValid(0.0));
        $this->assertTrue($this->validator->isValid(1.0));
        $this->assertTrue($this->validator->isValid(''));
        $this->assertTrue($this->validator->isValid('abc'));
        $this->assertTrue($this->validator->isValid('0'));
        $this->assertTrue($this->validator->isValid('1'));
        $this->assertTrue($this->validator->isValid(array()));
        $this->assertTrue($this->validator->isValid(array('xxx')));
        $this->assertTrue($this->validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyAll()
    {
        $this->validator->setType(NotEmpty::ALL);
        $this->assertFalse($this->validator->isValid(false));
        $this->assertTrue($this->validator->isValid(true));
        $this->assertFalse($this->validator->isValid(0));
        $this->assertTrue($this->validator->isValid(1));
        $this->assertFalse($this->validator->isValid(0.0));
        $this->assertTrue($this->validator->isValid(1.0));
        $this->assertFalse($this->validator->isValid(''));
        $this->assertTrue($this->validator->isValid('abc'));
        $this->assertFalse($this->validator->isValid('0'));
        $this->assertTrue($this->validator->isValid('1'));
        $this->assertFalse($this->validator->isValid(array()));
        $this->assertTrue($this->validator->isValid(array('xxx')));
        $this->assertFalse($this->validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testArrayConstantNotation()
    {
        $filter = new NotEmpty(
            array(
                'type' => array(
                    NotEmpty::ZERO,
                    NotEmpty::STRING,
                    NotEmpty::BOOLEAN
                )
            )
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid(array()));
        $this->assertTrue($filter->isValid(array('xxx')));
        $this->assertTrue($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testArrayConfigNotation()
    {
        $filter = new NotEmpty(
            array(
                'type' => array(
                    NotEmpty::ZERO,
                    NotEmpty::STRING,
                    NotEmpty::BOOLEAN),
                'test' => false
            )
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid(array()));
        $this->assertTrue($filter->isValid(array('xxx')));
        $this->assertTrue($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testMultiConstantNotation()
    {
        $filter = new NotEmpty(
            NotEmpty::ZERO + NotEmpty::STRING + NotEmpty::BOOLEAN
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid(array()));
        $this->assertTrue($filter->isValid(array('xxx')));
        $this->assertTrue($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testStringNotation()
    {
        $filter = new NotEmpty(
            array(
                'type' => array('zero', 'string', 'boolean')
            )
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid(array()));
        $this->assertTrue($filter->isValid(array('xxx')));
        $this->assertTrue($filter->isValid(null));
    }


    /**
     * Ensures that the validator follows expected behavior so if a string is specified more than once, it doesn't
     * cause different validations to run
     *
     * @param string  $string   Array of string type values
     * @param integer $expected Expected type setting value
     *
     * @return void
     *
     * @dataProvider duplicateStringSettingProvider
     */
    public function testStringNotationWithDuplicate($string, $expected)
    {
        $type = array($string, $string);
        $this->validator->setType($type);

        $this->assertEquals($expected, $this->validator->getType());
    }

    /**
     * Data provider for testStringNotationWithDuplicate method. Provides a string which will be duplicated. The test
     * ensures that setting a string value more than once only turns on the appropriate bit once
     *
     * @return array
     */
    public function duplicateStringSettingProvider()
    {
        return array(
            array('boolean',      NotEmpty::BOOLEAN),
            array('integer',      NotEmpty::INTEGER),
            array('float',        NotEmpty::FLOAT),
            array('string',       NotEmpty::STRING),
            array('zero',         NotEmpty::ZERO),
            array('array',        NotEmpty::EMPTY_ARRAY),
            array('null',         NotEmpty::NULL),
            array('php',          NotEmpty::PHP),
            array('space',        NotEmpty::SPACE),
            array('object',       NotEmpty::OBJECT),
            array('objectstring', NotEmpty::OBJECT_STRING),
            array('objectcount',  NotEmpty::OBJECT_COUNT),
            array('all',          NotEmpty::ALL),
        );
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testSingleStringNotation()
    {
        $filter = new NotEmpty(
            'boolean'
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertTrue($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertTrue($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid(array()));
        $this->assertTrue($filter->isValid(array('xxx')));
        $this->assertTrue($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testConfigObject()
    {
        $options = array('type' => 'all');
        $config  = new \Zend\Config\Config($options);

        $filter = new NotEmpty(
            $config
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertFalse($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertFalse($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertFalse($filter->isValid(array()));
        $this->assertTrue($filter->isValid(array('xxx')));
        $this->assertFalse($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testSettingFalseType()
    {
        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'Unknown');
        $this->validator->setType(true);
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testGetType()
    {
        $this->assertEquals(493, $this->validator->getType());
    }

    /**
     * @group ZF-3236
     */
    public function testStringWithZeroShouldNotBeTreatedAsEmpty()
    {
        $this->assertTrue($this->validator->isValid('0'));
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->validator->getMessages());
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $v2 = new NotEmpty();
        $this->assertTrue($this->validator->isValid($v2));
    }

    /**
     * @ZF-8767
     *
     * @return void
     */
    public function testZF8767()
    {
        $valid = new NotEmpty(NotEmpty::STRING);

        $this->assertFalse($valid->isValid(''));
        $messages = $valid->getMessages();
        $this->assertTrue(array_key_exists('isEmpty', $messages));
        $this->assertContains("can't be empty", $messages['isEmpty']);
    }

    /**
     * @return void
     */
    public function testObjects()
    {
        $valid = new NotEmpty(NotEmpty::STRING);
        $object = new stdClass();

        $this->assertFalse($valid->isValid($object));

        $valid = new NotEmpty(NotEmpty::OBJECT);
        $this->assertTrue($valid->isValid($object));
    }

    /**
     * @return void
     */
    public function testStringObjects()
    {
        $valid = new NotEmpty(NotEmpty::STRING);
        $object = new ClassTest2();

        $this->assertFalse($valid->isValid($object));

        $valid = new NotEmpty(NotEmpty::OBJECT_STRING);
        $this->assertTrue($valid->isValid($object));

        $object = new ClassTest3();
        $this->assertFalse($valid->isValid($object));
    }

    /**
     * @group ZF-11566
     */
    public function testArrayConfigNotationWithoutKey()
    {
        $filter = new NotEmpty(
            array('zero', 'string', 'boolean')
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid(array()));
        $this->assertTrue($filter->isValid(array('xxx')));
        $this->assertTrue($filter->isValid(null));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    public function testTypeAutoDetectionHasNoSideEffect()
    {
        $validator = new NotEmpty(array('translatorEnabled' => true));
        $this->assertEquals(493, $validator->getType());
    }
}

class ClassTest2
{
    public function __toString()
    {
        return 'Test';
    }
}

class ClassTest3
{
    public function toString()
    {
        return '';
    }
}

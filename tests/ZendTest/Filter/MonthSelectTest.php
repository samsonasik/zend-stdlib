<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Filter;

use Zend\Filter\MonthSelect as MonthSelectFilter;

/**
 * @group      Zend_Filter
 */
class MonthSelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideFilter
     * @param $options
     * @param $input
     * @param $expected
     */
    public function testFilter($options, $input, $expected)
    {
        $sut = new MonthSelectFilter();
        $sut->setOptions($options);
        $this->assertEquals($expected, $sut->filter($input));
    }

    public function provideFilter()
    {
        return array(
            array(array(), array('year' => '2014', 'month' => '10'), '2014-10'),
            array(array('nullOnEmpty' => true), array('year' => null, 'month' => '10'), null),
            array(array('nullOnAllEmpty' => true), array('year' => null, 'month' => null), null),
        );
    }
}

<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\I18n\View\Helper;

use DateTime;
use Locale;
use IntlDateFormatter;
use Zend\I18n\View\Helper\DateFormat as DateFormatHelper;

/**
 * Test class for Zend_View_Helper_Currency
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class DateFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DateFormatHelper
     */
    public $helper;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->helper = new DateFormatHelper();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
    }

    public function dateTestsDataProvider()
    {
        $date = new DateTime('2012-07-02T22:44:03Z');
        return array(
            // FULL format varies based on OS
            // array(
            //     'de_DE',
            //     'Europe/Berlin',
            //     IntlDateFormatter::FULL,
            //     IntlDateFormatter::FULL,
            //     $date,
            //     'Dienstag, 3. Juli 2012 00:44:03 Deutschland',
            // ),
            array(
                'de_DE',
                'Europe/Berlin',
                IntlDateFormatter::LONG,
                IntlDateFormatter::LONG,
                $date,
                '3. Juli 2012 00:44:03 MESZ',
            ),
            array(
                'de_DE',
                'Europe/Berlin',
                IntlDateFormatter::MEDIUM,
                IntlDateFormatter::MEDIUM,
                $date,
                '03.07.2012 00:44:03',
            ),
            array(
                'de_DE',
                'Europe/Berlin',
                IntlDateFormatter::SHORT,
                IntlDateFormatter::SHORT,
                $date,
                '03.07.12 00:44',
            ),
            // FULL format varies based on OS
            // array(
            //     'ru_RU',
            //     'Europe/Moscow',
            //     IntlDateFormatter::FULL,
            //     IntlDateFormatter::FULL,
            //     $date,
            //     '3 июля 2012 г. 2:44:03 Россия (Москва)',
            // ),
            // LONG format varies based on OS for ru_RU locale
            // array(
            //     'ru_RU',
            //     'Europe/Moscow',
            //     IntlDateFormatter::LONG,
            //     IntlDateFormatter::LONG,
            //     $date,
            //     '3 июля 2012 г. 2:44:03 GMT+04:00',
            // ),
            array(
                'ru_RU',
                'Europe/Moscow',
                IntlDateFormatter::MEDIUM,
                IntlDateFormatter::MEDIUM,
                $date,
                '03.07.2012 2:44:03',
            ),
            array(
                'ru_RU',
                'Europe/Moscow',
                IntlDateFormatter::SHORT,
                IntlDateFormatter::SHORT,
                $date,
                '03.07.12 2:44',
            ),
            // FULL format varies based on OS
            // array(
            //     'en_US',
            //     'America/New_York',
            //     IntlDateFormatter::FULL,
            //     IntlDateFormatter::FULL,
            //     $date,
            //     'Monday, July 2, 2012 6:44:03 PM ET',
            // ),
            array(
                'en_US',
                'America/New_York',
                IntlDateFormatter::LONG,
                IntlDateFormatter::LONG,
                $date,
                'July 2, 2012 6:44:03 PM EDT',
            ),
            array(
                'en_US',
                'America/New_York',
                IntlDateFormatter::MEDIUM,
                IntlDateFormatter::MEDIUM,
                $date,
                'Jul 2, 2012 6:44:03 PM',
            ),
            array(
                'en_US',
                'America/New_York',
                IntlDateFormatter::SHORT,
                IntlDateFormatter::SHORT,
                $date,
                '7/2/12 6:44 PM',
            ),
        );
    }

    public function dateTestsDataProviderWithPattern()
    {
        $date = new DateTime('2012-07-02T22:44:03Z');
        return array(
            // FULL format varies based on OS
            // array(
            //     'de_DE',
            //     'Europe/Berlin',
            //     IntlDateFormatter::FULL,
            //     IntlDateFormatter::FULL,
            //     $date,
            //     'Dienstag, 3. Juli 2012 00:44:03 Deutschland',
            // ),
            array(
                'de_DE',
                'Europe/Berlin',
                null,
                null,
                'MMMM',
                $date,
                'Juli',
            ),
            array(
                'de_DE',
                'Europe/Berlin',
                null,
                null,
                'MMMM.Y',
                $date,
                'Juli.2012',
            ),
            array(
                'de_DE',
                'Europe/Berlin',
                null,
                null,
                'dd/Y',
                $date,
                '03/2012',
            ),
        );
    }

    /**
     * @dataProvider dateTestsDataProvider
     */
    public function testBasic($locale, $timezone, $timeType, $dateType, $date, $expected)
    {
        $this->helper->setTimezone($timezone);
        $this->assertMbStringEquals($expected, $this->helper->__invoke(
            $date, $dateType, $timeType, $locale, null
        ));
    }

    /**
     * @dataProvider dateTestsDataProvider
     */
    public function testSettersProvideDefaults($locale, $timezone, $timeType, $dateType, $date, $expected)
    {
        $this->helper
            ->setTimezone($timezone)
            ->setLocale($locale);

        $this->assertMbStringEquals($expected, $this->helper->__invoke(
            $date, $dateType, $timeType
        ));
    }

    /**
     * @dataProvider dateTestsDataProviderWithPattern
     */
    public function testUseCustomPattern($locale, $timezone, $timeType, $dateType, $pattern, $date, $expected)
    {
        $this->helper->setTimezone($timezone);
        $this->assertMbStringEquals($expected, $this->helper->__invoke(
            $date, $dateType, $timeType, $locale, $pattern
        ));
    }

    public function testDefaultLocale()
    {
        $this->assertEquals(Locale::getDefault(), $this->helper->getLocale());
    }
    
    public function testBugTwoPatternOnSameHelperInstance()
    {
    	$date = new DateTime('2012-07-02T22:44:03Z');
    	
    	$helper = new DateFormatHelper();
    	$this->assertEquals('03/2012', $helper->__invoke($date, null, null, 'it_IT', 'dd/Y'));
    	$this->assertEquals('03-2012', $helper->__invoke($date, null, null, 'it_IT', 'dd-Y'));
    }

    public function assertMbStringEquals($expected, $test, $message = '')
    {
        $expected = str_replace(array("\xC2\xA0", ' '), '', $expected);
        $test     = str_replace(array("\xC2\xA0", ' '), '', $test);
        $this->assertEquals($expected, $test, $message);
    }
}

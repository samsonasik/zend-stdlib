<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData\App;

use Zend\GData\App\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_App
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_App
 */
class ControlTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->controlText = file_get_contents(
                'Zend/GData/App/_files/ControlElementSample1.xml',
                true);
        $this->control = new Extension\Control();
    }

    public function testEmptyControlShouldHaveEmptyExtensionsList() {
        $this->assertTrue(is_array($this->control->extensionElements));
        $this->assertTrue(count($this->control->extensionElements) == 0);
    }

    public function testEmptyControlToAndFromStringShouldMatch() {
        $controlXml = $this->control->saveXML();
        $newControl = new Extension\Control();
        $newControl->transferFromXML($controlXml);
        $newControlXml = $newControl->saveXML();
        $this->assertTrue($controlXml == $newControlXml);
    }

    public function testControlWithDraftToAndFromStringShouldMatch() {
        $draft = new Extension\Draft('yes');
        $this->control->draft = $draft;
        $controlXml = $this->control->saveXML();
        $newControl = new Extension\Control();
        $newControl->transferFromXML($controlXml);
        $newControlXml = $newControl->saveXML();
        $this->assertEquals($newControlXml, $controlXml);
        $this->assertEquals('yes', $newControl->draft->text);
    }

    public function testConvertControlWithDraftToAndFromString() {
        $this->control->transferFromXML($this->controlText);
        $this->assertEquals('yes', $this->control->draft->text);
    }

}

<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage UnitTests
 */

namespace ZendTest\Captcha;

/**
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage UnitTests
 * @group      Zend_Captcha
 */
abstract class CommonWordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Word adapter class name
     *
     * @var string
     */
    protected $wordClass;

    /**
     * @group ZF2-91
     */
    public function testLoadInvalidSessionClass()
    {
        $wordAdapter = new $this->wordClass;
        $wordAdapter->setSessionClass('ZendTest\Captcha\InvalidClassName');
        $this->setExpectedException('Zend\Captcha\Exception\InvalidArgumentException', 'not found');
        $wordAdapter->getSession();
    }
}

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
 * @package    Zend_XmlRpc
 */

namespace Zend\XmlRpc\Generator;

/**
 * XML generator adapter interface
 */
interface GeneratorInterface
{
    public function getEncoding();
    public function setEncoding($encoding);
    public function openElement($name, $value = null);
    public function closeElement($name);

    /**
     * Return XML as a string
     *
     * @return string
     */
    public function saveXml();

    public function stripDeclaration($xml);
    public function flush();
    public function __toString();
}

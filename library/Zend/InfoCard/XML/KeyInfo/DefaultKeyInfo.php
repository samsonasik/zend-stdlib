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
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
 */

namespace Zend\InfoCard\XML\KeyInfo;

use Zend\InfoCard\XML;

/**
 * An object representation of a XML <KeyInfo> block which doesn't provide a namespace
 * In this context, it is assumed to mean that it is the type of KeyInfo block which
 * contains the SecurityTokenReference
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
 */
class DefaultKeyInfo extends AbstractKeyInfo
{
    /**
     * Returns the object representation of the SecurityTokenReference block
     *
     * @throws XML\Exception\RuntimeException
     * @return XML\SecurityTokenReference
     */
    public function getSecurityTokenReference()
    {
        $this->registerXPathNamespace('o', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');

        list($sectokenref) = $this->xpath('//o:SecurityTokenReference');

        if(!($sectokenref instanceof XML\AbstractElement)) {
            throw new XML\Exception\RuntimeException('Could not locate the Security Token Reference');
        }

        return XML\SecurityTokenReference::getInstance($sectokenref);
    }
}

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
 * @package    Zend_Gdata
 * @subpackage Analytics
 */

namespace Zend\GData\Analytics\Extension;

/**
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Analytics
 */
class Metric extends Property
{
    protected $_rootNamespace = 'ga';
    protected $_rootElement = 'metric';
    protected $_value = null;
    protected $_name = null;

	protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
	        case 'name':
	        	$this->_name = $attribute->nodeValue;
		        break;
	        case 'value':
	            $this->_value = $attribute->nodeValue;
	            break;
	        default:
	            parent::takeAttributeFromDOM($attribute);
        }
    }
}

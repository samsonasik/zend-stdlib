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
 * @subpackage App
 */

namespace Zend\GData\App\Extension;

/**
 * Represents the atom:generator element
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage App
 */
class Generator extends AbstractExtension
{

    protected $_rootElement = 'generator';
    protected $_uri = null;
    protected $_version = null;

    public function __construct($text = null, $uri = null, $version = null)
    {
        parent::__construct();
        $this->_text = $text;
        $this->_uri = $uri;
        $this->_version = $version;
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_uri !== null) {
            $element->setAttribute('uri', $this->_uri);
        }
        if ($this->_version !== null) {
            $element->setAttribute('version', $this->_version);
        }
        return $element;
    }

    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'uri':
            $this->_uri = $attribute->nodeValue;
            break;
        case 'version':
            $this->_version= $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * @return Uri
     */
    public function getUri()
    {
        return $this->_uri;
    }

    /**
     * @param Uri $value
     * @return \Zend\GData\App\Entry Provides a fluent interface
     */
    public function setUri($value)
    {
        $this->_uri = $value;
        return $this;
    }

    /**
     * @return Version
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * @param Version $value
     * @return \Zend\GData\App\Entry Provides a fluent interface
     */
    public function setVersion($value)
    {
        $this->_version = $value;
        return $this;
    }

}

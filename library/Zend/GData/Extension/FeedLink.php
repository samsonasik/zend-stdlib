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
 * @subpackage Gdata
 */

namespace Zend\GData\Extension;

use Zend\GData\Extension;

/**
 * Represents the gd:feedLink element
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Gdata
 */
class FeedLink extends Extension
{

    protected $_rootElement = 'feedLink';
    protected $_countHint = null;
    protected $_href = null;
    protected $_readOnly = null;
    protected $_rel = null;
    protected $_feed = null;

    public function __construct($href = null, $rel = null,
            $countHint = null, $readOnly = null, $feed = null)
    {
        parent::__construct();
        $this->_countHint = $countHint;
        $this->_href = $href;
        $this->_readOnly = $readOnly;
        $this->_rel = $rel;
        $this->_feed = $feed;
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_countHint !== null) {
            $element->setAttribute('countHint', $this->_countHint);
        }
        if ($this->_href !== null) {
            $element->setAttribute('href', $this->_href);
        }
        if ($this->_readOnly !== null) {
            $element->setAttribute('readOnly', $this->_readOnly);
        }
        if ($this->_rel !== null) {
            $element->setAttribute('rel', $this->_rel);
        }
        if ($this->_feed !== null) {
            $element->appendChild($this->_feed->getDOM($element->ownerDocument));
        }
        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
            case $this->lookupNamespace('atom') . ':' . 'feed';
                $feed = new \Zend\GData\Feed();
                $feed->transferFromDOM($child);
                $this->_feed = $feed;
                break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'countHint':
            $this->_countHint = $attribute->nodeValue;
            break;
        case 'href':
            $this->_href = $attribute->nodeValue;
            break;
        case 'readOnly':
            if ($attribute->nodeValue != "true" && $attribute->nodeValue != "false") {
                throw new \Zend\GData\App\InvalidArgumentException(
                    "Expected 'true' or 'false' for gCal:selected#value.");
            }
            $this->_readOnly = $attribute->nodeValue;
            break;
        case 'rel':
            $this->_rel = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->_href;
    }

    public function setHref($value)
    {
        $this->_href = $value;
        return $this;
    }

    public function getReadOnly()
    {
        return $this->_readOnly;
    }

    public function setReadOnly($value)
    {
        $this->_readOnly = $value;
        return $this;
    }

    public function getRel()
    {
        return $this->_rel;
    }

    public function setRel($value)
    {
        $this->_rel = $value;
        return $this;
    }

    public function getFeed()
    {
        return $this->_feed;
    }

    public function setFeed($value)
    {
        $this->_feed = $value;
        return $this;
    }

}

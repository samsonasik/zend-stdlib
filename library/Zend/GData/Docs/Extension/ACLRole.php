<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Docs\Extension;

/**
 * Represents the gAcl:role element used by the Docs data API.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Gapps
 */
class ACLRole extends \Zend\GData\Extension
{

    protected $_rootNamespace = 'gAcl';
    protected $_rootElement = 'role';

    /**
     * The permission to give to the user.  Possible roles include
     * 'writer', 'owner', or 'reader'
     *
     * @var string
     */
    protected $_value = null;

    /**
     * Constructs a new \Zend\GData\Docs\Extension\ACLRole object.
     *
     * @param string $value The role of the acl.
     */
    public function __construct($value = null)
    {
        $this->registerAllNamespaces(\Zend\GData\Docs::$namespaces);
        parent::__construct();
        $this->_value = $value;
    }


    /**
     * Retrieves a DOMElement which corresponds to this element and all
     * child properties.  This is used to build an entry back into a DOM
     * and eventually XML text for sending to the server upon updates, or
     * for application storage/persistence.
     *
     * @param DOMDocument $doc The DOMDocument used to construct DOMElements
     * @return DOMElement The DOMElement representing this element and all
     * child properties.
     */
    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_value !== null) {
            $element->setAttribute('value', $this->_value);
        }

        return $element;
    }

    /**
     * Given a DOMNode representing an attribute, tries to map the data into
     * instance members.  If no mapping is defined, the name and value are
     * stored in an array.
     *
     * @param DOMNode $attribute The DOMNode attribute needed to be handled
     */
    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'value':
            $this->_value = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * Get the value for this element's value attribute.
     *
     * @see setName
     * @return string The requested attribute.
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set the value for this element's value attribute.
     *
     * @param string $value The desired value for this attribute.
     * @return \Zend\GData\Docs\Extension\ACLRole The element being modified.
     */
    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * Magic toString method allows using this directly via echo
     * Works best in PHP >= 4.2.0
     *
     * @return string
     */
    public function __toString()
    {
        return "Value: " . $this->getValue();
    }
}

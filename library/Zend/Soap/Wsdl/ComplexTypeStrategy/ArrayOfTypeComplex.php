<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Soap\Wsdl\ComplexTypeStrategy;

use Zend\Soap\Exception;
use Zend\Soap\Wsdl;

/**
 * ArrayOfTypeComplex strategy
 *
 */
class ArrayOfTypeComplex extends DefaultComplexType
{
    /**
     * Add an ArrayOfType based on the xsd:complexType syntax if type[] is
     * detected in return value doc comment.
     *
     * @param string $type
     * @throws Exception\InvalidArgumentException
     * @return string tns:xsd-type
     */
    public function addComplexType($type)
    {
        if (($soapType = $this->scanRegisteredTypes($type)) !== null) {
            return $soapType;
        }

        $singularType = $this->_getSingularPhpType($type);
        $nestingLevel = $this->_getNestedCount($type);

        if ($nestingLevel == 0) {
            return parent::addComplexType($singularType);
        } elseif ($nestingLevel == 1) {
            // The following blocks define the Array of Object structure
            return $this->_addArrayOfComplexType($singularType, $type);
        } else {
            throw new Exception\InvalidArgumentException(
                'ArrayOfTypeComplex cannot return nested ArrayOfObject deeper than one level. Use array object properties to return deep nested data.'
            );
        }
    }

    /**
     * Add an ArrayOfType based on the xsd:complexType syntax if type[] is
     * detected in return value doc comment.
     *
     * @param string $singularType   e.g. '\MyNamespace\MyClassname'
     * @param string $type           e.g. '\MyNamespace\MyClassname[]'
     * @return string tns:xsd-type   e.g. 'tns:ArrayOfMyNamespace.MyClassname'
     */
    protected function _addArrayOfComplexType($singularType, $type)
    {
        if (($soapType = $this->scanRegisteredTypes($type)) !== null) {
            return $soapType;
        }

        $xsdComplexTypeName = 'ArrayOf' . $this->getContext()->translateType($singularType);
        $xsdComplexType     = 'tns:' . $xsdComplexTypeName;

        // Register type here to avoid recursion
        $this->getContext()->addType($type, $xsdComplexType);

        // Process singular type using DefaultComplexType strategy
        parent::addComplexType($singularType);


        // Add array type structure to WSDL document
        $dom = $this->getContext()->toDomDocument();

        $complexType = $dom->createElementNS(Wsdl::NS_SCHEMA, 'complexType');
        $this->getContext()->getSchema()->appendChild($complexType);

        $complexType->setAttribute('name', $xsdComplexTypeName);

        $complexContent = $dom->createElementNS(Wsdl::NS_SCHEMA, 'complexContent');
        $complexType->appendChild($complexContent);

        $xsdRestriction = $dom->createElementNS(Wsdl::NS_SCHEMA, 'restriction');
        $xsdRestriction->setAttribute('base', 'soap-enc:Array');
        $complexContent->appendChild($xsdRestriction);

        $xsdAttribute = $dom->createElementNS(Wsdl::NS_SCHEMA, 'attribute');
        $xsdRestriction->appendChild($xsdAttribute);

        $xsdAttribute->setAttribute('ref', 'soap-enc:arrayType');
        $xsdAttribute->setAttributeNS(
            Wsdl::NS_WSDL,
            'arrayType',
            'tns:' . $this->getContext()->translateType($singularType) . '[]'
        );

        return $xsdComplexType;
    }

    /**
     * From a nested definition with type[], get the singular PHP Type
     *
     * @param  string $type
     * @return string
     */
    protected function _getSingularPhpType($type)
    {
        return str_replace('[]', '', $type);
    }

    /**
     * Return the array nesting level based on the type name
     *
     * @param  string $type
     * @return integer
     */
    protected function _getNestedCount($type)
    {
        return substr_count($type, '[]');
    }
}

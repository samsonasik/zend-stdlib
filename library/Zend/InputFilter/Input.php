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
 * @package    Zend_InputFilter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\InputFilter;

use Zend\Filter\FilterChain;
use Zend\Validator\ValidatorChain;

/**
 * @category   Zend
 * @package    Zend_InputFilter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Input implements InputInterface
{
    protected $allowEmpty = false;
    protected $filterChain;
    protected $name;
    protected $required = true;
    protected $validatorChain;
    protected $value;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function setAllowEmpty($allowEmpty)
    {
        $this->allowEmpty = (bool) $allowEmpty;
        return $this;
    }

    public function setFilterChain(FilterChain $filterChain)
    {
        $this->filterChain = $filterChain;
        return $this;
    }

    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }

    public function setRequired($required)
    {
        $this->required = (bool) $required;
        return $this;
    }

    public function setValidatorChain(ValidatorChain $validatorChain)
    {
        $this->validatorChain = $validatorChain;
        return $this;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }


    public function allowEmpty()
    {
        return $this->allowEmpty;
    }

    public function getFilterChain()
    {
        if (!$this->filterChain) {
            $this->setFilterChain(new FilterChain());
        }
        return $this->filterChain;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRawValue()
    {
        return $this->value;
    }

    public function isRequired()
    {
        return $this->required;
    }

    public function getValidatorChain()
    {
        if (!$this->validatorChain) {
            $this->setValidatorChain(new ValidatorChain());
        }
        return $this->validatorChain;
    }

    public function getValue()
    {
        $filter = $this->getFilterChain();
        return $filter->filter($this->value);
    }


    public function isValid()
    {
        $validator = $this->getValidatorChain();
        $value     = $this->getValue();
        return $validator->isValid($value);
    }

    public function getMessages()
    {
        $validator = $this->getValidatorChain();
        return $validator->getMessages();
    }
}


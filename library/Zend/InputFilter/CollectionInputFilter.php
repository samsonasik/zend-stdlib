<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

use Traversable;

class CollectionInputFilter extends InputFilter
{
    /*
     * @var bool
     */
    protected $isRequired = false;

    /*
     * @var int
     */
    protected $count = null;

    /*
     * @var array
     */
    protected $collectionValues = array();

    /*
     * @var array
     */
    protected $collectionRawValues = array();

    /*
     * @var array
     */
    protected $collectionMessages = array();

    /**
     * @var BaseInputFilter
     */
    protected $inputFilter;

    /**
     * Set the input filter to use when looping the data
     *
     * @param BaseInputFilter|array|Traversable $inputFilter
     * @return CollectionInputFilter
     */
    public function setInputFilter($inputFilter)
    {
        if (is_array($inputFilter) || $inputFilter instanceof Traversable) {
            $inputFilter = $this->getFactory()->createInputFilter($inputFilter);
        }

        if (!$inputFilter instanceof BaseInputFilter) {
            throw new Exception\RuntimeException(sprintf(
                '%s expects an instance of %s; received "%s"',
                __METHOD__,
                'Zend\InputFilter\BaseInputFilter',
                (is_object($inputFilter) ? get_class($inputFilter) : gettype($inputFilter))
            ));
        }

        $this->inputFilter = $inputFilter;

        return $this;
    }

    /**
     * Get the input filter used when looping the data
     *
     * @return BaseInputFilter
     */
    public function getInputFilter()
    {
        if (null === $this->inputFilter) {
            $this->setInputFilter(new InputFilter());
        }

        return $this->inputFilter;
    }

    /**
     * Set if the collection can be empty
     *
     * @param bool $isRequired
     * @return CollectionInputFilter
     */
    public function setIsRequired($isRequired)
    {
        $this->isRequired = $isRequired;

        return $this;
    }

    /**
     * Get if collection can be empty
     *
     * @return bool
     */
    public function getIsRequired()
    {
        return $this->isRequired;
    }

    /**
     * Set the count of data to validate
     *
     * @param int $count
     * @return CollectionInputFilter
     */
    public function setCount($count)
    {
        $this->count = $count > 0 ? $count : 0;

        return $this;
    }

    /**
     * Get the count of data to validate, use the count of data by default
     *
     * @return int
     */
    public function getCount()
    {
        if (null === $this->count) {
            $this->count = count($this->data);
        }

        return $this->count;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        $valid = true;

        if ($this->getCount() < 1) {
            if ($this->isRequired) {
                $valid = false;
            }
        }

        if (count($this->data) < $this->getCount()) {
            $valid = false;
        }

        if (empty($this->data)) {
            $this->clearValues();
            $this->clearRawValues();

            return $valid;
        }

        foreach ($this->data as $key => $data) {
            if (!is_array($data)) {
                $data = array();
            }
            $this->getInputFilter()->setData($data);

            if (null !== $this->validationGroup) {
                $this->getInputFilter()->setValidationGroup($this->validationGroup[$key]);
            }

            if ($this->getInputFilter()->isValid()) {
                $this->validInputs[$key] = $this->getInputFilter()->getValidInput();
            } else {
                $valid = false;
                $this->collectionMessages[$key] = $this->getInputFilter()->getMessages();
                $this->invalidInputs[$key] = $this->getInputFilter()->getInvalidInput();
            }

            $this->collectionValues[$key] = $this->getInputFilter()->getValues();
            $this->collectionRawValues[$key] = $this->getInputFilter()->getRawValues();
        }

        return $valid;
    }

    /**
     * {@inheritdoc}
     */
    public function setValidationGroup($name)
    {
        if ($name === self::VALIDATE_ALL) {
            $this->validationGroup = null;

            return $this;
        }

        $this->validationGroup = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return $this->collectionValues;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawValues()
    {
        return $this->collectionRawValues;
    }

    /**
     * Clear collectionValues
     *
     * @access public
     */
    public function clearValues()
    {
        return $this->collectionValues = array();
    }

    /**
     * Clear collectionRawValues
     *
     * @access public
     */
    public function clearRawValues()
    {
        return $this->collectionRawValues = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        return $this->collectionMessages;
    }

}

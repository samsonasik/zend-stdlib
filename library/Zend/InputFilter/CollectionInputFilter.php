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
     * @var array
     */
    protected $collectionData;

    /*
     * @var array
     */
    protected $collectionValidInputs;

    /*
     * @var array
     */
    protected $collectionInvalidInputs;

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
            $this->count = count($this->collectionData);
        }
        return $this->count;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data)
    {
        $this->collectionData = $data;
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

        if (count($this->collectionData) < $this->getCount()) {
            $valid = false;
        }

        if (empty($this->collectionData)) {
            $this->clearValues();
            $this->clearRawValues();
            return $valid;
        }

        $inputs = $this->validationGroup ?: array_keys($this->inputFilter->getInputs());
        foreach ($this->collectionData as $key => $data) {
            if (!is_array($data)) {
                $data = array();
            }
            $this->inputFilter->setData($data);

            if ($this->inputFilter->isValid()) {
                $this->collectionValidInputs[$key] = $this->inputFilter->getValidInput();
            } else {
                $valid = false;
                $this->collectionMessages[$key] = $this->inputFilter->getMessages();
                $this->collectionInvalidInputs[$key] = $this->inputFilter->getInvalidInput();
            }

            $this->collectionValues[$key] = $this->inputFilter->getValues();
            $this->collectionRawValues[$key] = $this->inputFilter->getRawValues();

        }

        return $valid;
    }

    /**
     * {@inheritdoc}
     */
    public function setValidationGroup($groups)
    {
        if ($groups === self::VALIDATE_ALL) {
            $this->validationGroup = null;
            return $this;
        }

        if (is_array($groups)) {
            foreach($groups as $group) {
                $this->inputFilter->setValidationGroup($group);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getInvalidInput()
    {
        return (is_array($this->collectionInvalidInputs) ? $this->collectionInvalidInputs : array());
    }

    /**
     * {@inheritdoc}
     */
    public function getValidInput()
    {
        return (is_array($this->collectionValidInputs) ? $this->collectionValidInputs : array());
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

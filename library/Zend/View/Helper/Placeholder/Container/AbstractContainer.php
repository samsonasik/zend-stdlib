<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\View\Helper\Placeholder\Container;

use Zend\View\Exception;

/**
 * Abstract class representing container for placeholder values
 *
 * @package    Zend_View
 * @subpackage Helper
 */
abstract class AbstractContainer extends \ArrayObject
{
    /**
     * Whether or not to override all contents of placeholder
     * @const string
     */
    const SET    = 'SET';

    /**
     * Whether or not to append contents to placeholder
     * @const string
     */
    const APPEND = 'APPEND';

    /**
     * Whether or not to prepend contents to placeholder
     * @const string
     */
    const PREPEND = 'PREPEND';

    /**
     * What text to prefix the placeholder with when rendering
     * @var string
     */
    protected $prefix    = '';

    /**
     * What text to append the placeholder with when rendering
     * @var string
     */
    protected $postfix   = '';

    /**
     * What string to use between individual items in the placeholder when rendering
     * @var string
     */
    protected $separator = '';

    /**
     * What string to use as the indentation of output, this will typically be spaces. Eg: '    '
     * @var string
     */
    protected $indent = '';

    /**
     * Whether or not we're already capturing for this given container
     * @var bool
     */
    protected $captureLock = false;

    /**
     * What type of capture (overwrite (set), append, prepend) to use
     * @var string
     */
    protected $captureType;

    /**
     * Key to which to capture content
     * @var string
     */
    protected $captureKey;

    /**
     * Constructor - This is needed so that we can attach a class member as the ArrayObject container
     *
     */
    public function __construct()
    {
        parent::__construct(array(), parent::ARRAY_AS_PROPS);
    }

    /**
     * Set a single value
     *
     * @param  mixed $value
     * @return void
     */
    public function set($value)
    {
        $this->exchangeArray(array($value));
    }

    /**
     * Prepend a value to the top of the container
     *
     * @param  mixed $value
     * @return void
     */
    public function prepend($value)
    {
        $values = $this->getArrayCopy();
        array_unshift($values, $value);
        $this->exchangeArray($values);
    }

    /**
     * Retrieve container value
     *
     * If single element registered, returns that element; otherwise,
     * serializes to array.
     *
     * @return mixed
     */
    public function getValue()
    {
        if (1 == count($this)) {
            $keys = $this->getKeys();
            $key  = array_shift($keys);
            return $this[$key];
        }

        return $this->getArrayCopy();
    }

    /**
     * Set prefix for __toString() serialization
     *
     * @param  string $prefix
     * @return \Zend\View\Helper\Placeholder\Container\AbstractContainer
     */
    public function setPrefix($prefix)
    {
        $this->prefix = (string) $prefix;
        return $this;
    }

    /**
     * Retrieve prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set postfix for __toString() serialization
     *
     * @param  string $postfix
     * @return \Zend\View\Helper\Placeholder\Container\AbstractContainer
     */
    public function setPostfix($postfix)
    {
        $this->postfix = (string) $postfix;
        return $this;
    }

    /**
     * Retrieve postfix
     *
     * @return string
     */
    public function getPostfix()
    {
        return $this->postfix;
    }

    /**
     * Set separator for __toString() serialization
     *
     * Used to implode elements in container
     *
     * @param  string $separator
     * @return \Zend\View\Helper\Placeholder\Container\AbstractContainer
     */
    public function setSeparator($separator)
    {
        $this->separator = (string) $separator;
        return $this;
    }

    /**
     * Retrieve separator
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * Set the indentation string for __toString() serialization,
     * optionally, if a number is passed, it will be the number of spaces
     *
     * @param  string|int $indent
     * @return \Zend\View\Helper\Placeholder\Container\AbstractContainer
     */
    public function setIndent($indent)
    {
        $this->indent = $this->getWhitespace($indent);
        return $this;
    }

    /**
     * Retrieve indentation
     *
     * @return string
     */
    public function getIndent()
    {
        return $this->indent;
    }

    /**
     * Retrieve whitespace representation of $indent
     *
     * @param  int|string $indent
     * @return string
     */
    public function getWhitespace($indent)
    {
        if (is_int($indent)) {
            $indent = str_repeat(' ', $indent);
        }

        return (string) $indent;
    }

    /**
     * Start capturing content to push into placeholder
     *
     * @param  int $type How to capture content into placeholder; append, prepend, or set
     * @return void
     * @throws Exception\RuntimeException if nested captures detected
     */
    public function captureStart($type = AbstractContainer::APPEND, $key = null)
    {
        if ($this->captureLock) {
            throw new Exception\RuntimeException(
                'Cannot nest placeholder captures for the same placeholder'
            );
        }

        $this->captureLock = true;
        $this->captureType = $type;
        if ((null !== $key) && is_scalar($key)) {
            $this->captureKey = (string) $key;
        }
        ob_start();
    }

    /**
     * End content capture
     *
     * @return void
     */
    public function captureEnd()
    {
        $data               = ob_get_clean();
        $key                = null;
        $this->captureLock = false;
        if (null !== $this->captureKey) {
            $key = $this->captureKey;
        }
        switch ($this->captureType) {
            case self::SET:
                if (null !== $key) {
                    $this[$key] = $data;
                } else {
                    $this->exchangeArray(array($data));
                }
                break;
            case self::PREPEND:
                if (null !== $key) {
                    $array  = array($key => $data);
                    $values = $this->getArrayCopy();
                    $final  = $array + $values;
                    $this->exchangeArray($final);
                } else {
                    $this->prepend($data);
                }
                break;
            case self::APPEND:
            default:
                if (null !== $key) {
                    if (empty($this[$key])) {
                        $this[$key] = $data;
                    } else {
                        $this[$key] .= $data;
                    }
                } else {
                    $this[$this->nextIndex()] = $data;
                }
                break;
        }
    }

    /**
     * Get keys
     *
     * @return array
     */
    public function getKeys()
    {
        $array = $this->getArrayCopy();
        return array_keys($array);
    }

    /**
     * Next Index
     *
     * as defined by the PHP manual
     * @return int
     */
    public function nextIndex()
    {
        $keys = $this->getKeys();
        if (0 == count($keys)) {
            return 0;
        }

        return $nextIndex = max($keys) + 1;
    }

    /**
     * Render the placeholder
     *
     * @param null|int|string $indent
     * @return string
     */
    public function toString($indent = null)
    {
        $indent = ($indent !== null)
                ? $this->getWhitespace($indent)
                : $this->getIndent();

        $items  = $this->getArrayCopy();
        $return = $indent
                . $this->getPrefix()
                . implode($this->getSeparator(), $items)
                . $this->getPostfix();
        $return = preg_replace("/(\r\n?|\n)/", '$1' . $indent, $return);
        return $return;
    }

    /**
     * Serialize object to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}

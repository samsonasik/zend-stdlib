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
 * @package    Zend_Mail
 * @subpackage Header
 */

namespace Zend\Mail\Header;

use Zend\Mail\Headers;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Header
 */
class ContentType implements HeaderInterface
{
    /**
     * Header encoding
     * 
     * @var string
     */
    protected $encoding = 'ASCII';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $parameters = array();

    public static function fromString($headerLine)
    {
        $headerLine = iconv_mime_decode($headerLine, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');
        list($name, $value) = explode(': ', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'content-type') {
            throw new Exception\InvalidArgumentException('Invalid header line for Content-Type string');
        }

        $value  = str_replace(Headers::FOLDING, " ", $value);
        $values = preg_split('#\s*;\s*#', $value);
        $type   = array_shift($values);

        $header = new static();
        $header->setType($type);

        if (count($values)) {
            foreach ($values as $keyValuePair) {
                list($key, $value) = explode('=', $keyValuePair);
                $value = trim($value, "\"\' \t\n\r\0\x0B");
                $header->addParameter($key, $value);
            }
        }
        
        return $header;
    }

    public function getFieldName()
    {
        return 'Content-Type';
    }

    public function getFieldValue($format = HeaderInterface::FORMAT_RAW)
    {
        $prepared = $this->type;
        if (empty($this->parameters)) {
            return $prepared;
        }

        $values = array($prepared);
        foreach ($this->parameters as $attribute => $value) {
            $values[] = sprintf('%s="%s"', $attribute, $value);
        }

        return implode(';' . Headers::FOLDING, $values);
    }

    public function setEncoding($encoding) 
    {
        $this->encoding = $encoding;
        return $this;
    }

    public function getEncoding()
    {
        return $this->encoding;
    }

    public function toString()
    {
        return 'Content-Type: ' . $this->getFieldValue(HeaderInterface::FORMAT_RAW);
    }

    /**
     * Set the content type
     *
     * @param  string $type
     * @throws Exception\InvalidArgumentException
     * @return ContentType
     */
    public function setType($type)
    {
        if (!preg_match('/^[a-z_-]+\/[a-z_-]+$/i', $type)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a value in the format "type/subtype"; received "%s"',
                __METHOD__,
                (string) $type
            ));
        }
        $this->type = $type;
        return $this;
    }

    /**
     * Retrieve the content type
     * 
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add a parameter pair
     * 
     * @param  string $name 
     * @param  string $value 
     * @return ContentType
     */
    public function addParameter($name, $value)
    {
        $name = strtolower($name);
        $this->parameters[$name] = (string) $value;
        return $this;
    }

    /**
     * Get all parameters
     * 
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get a parameter by name
     * 
     * @param  string $name 
     * @return null|string
     */
    public function getParameter($name)
    {
        $name = strtolower($name);
        if (isset($this->parameters[$name])) {
            return $this->parameters[$name];
        }
        return null;
    }

    /**
     * Remove a named parameter
     * 
     * @param  string $name 
     * @return bool
     */
    public function removeParameter($name)
    {
        $name = strtolower($name);
        if (isset($this->parameters[$name])) {
            unset($this->parameters[$name]);
            return true;
        }
        return false;
    }
}

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
 * @package    Zend_Feed_Writer
 */
 
namespace Zend\Feed\Writer\Extension\ITunes;

use Zend\Feed\Writer;
use Zend\Uri;

/**
* @category Zend
* @package Zend_Feed_Writer
*/
class Feed
{
    /**
     * Array of Feed data for rendering by Extension's renderers
     *
     * @var array
     */
    protected $_data = array();
    
    /**
     * Encoding of all text values
     *
     * @var string
     */
    protected $_encoding = 'UTF-8';
    
    /**
     * Set feed encoding
     * 
     * @param  string $enc 
     * @return Feed
     */
    public function setEncoding($enc)
    {
        $this->_encoding = $enc;
        return $this;
    }
    
    /**
     * Get feed encoding
     * 
     * @return string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }
    
    /**
     * Set a block value of "yes" or "no". You may also set an empty string.
     *
     * @param  string
     * @return Feed
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesBlock($value)
    {
        if (!ctype_alpha($value) && strlen($value) > 0) {
            throw new Writer\Exception\InvalidArgumentException('invalid parameter: "block" may only'
            . ' contain alphabetic characters');
        }
        if (iconv_strlen($value, $this->getEncoding()) > 255) {
            throw new Writer\Exception\InvalidArgumentException('invalid parameter: "block" may only'
            . ' contain a maximum of 255 characters');
        }
        $this->_data['block'] = $value;
        return $this;
    }
    
    /**
     * Add feed authors
     * 
     * @param  array $values 
     * @return Feed
     */
    public function addItunesAuthors(array $values)
    {
        foreach ($values as $value) {
            $this->addItunesAuthor($value);
        }
        return $this;
    }
    
    /**
     * Add feed author
     * 
     * @param  string $value 
     * @return Feed
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function addItunesAuthor($value)
    {
        if (iconv_strlen($value, $this->getEncoding()) > 255) {
            throw new Writer\Exception\InvalidArgumentException('invalid parameter: any "author" may only'
            . ' contain a maximum of 255 characters each');
        }
        if (!isset($this->_data['authors'])) {
            $this->_data['authors'] = array();
        }
        $this->_data['authors'][] = $value;   
        return $this;
    }
    
    /**
     * Set feed categories
     * 
     * @param  array $values 
     * @return Feed
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesCategories(array $values)
    {
        if (!isset($this->_data['categories'])) {
            $this->_data['categories'] = array();
        }
        foreach ($values as $key=>$value) {
            if (!is_array($value)) {
                if (iconv_strlen($value, $this->getEncoding()) > 255) {
                    throw new Writer\Exception\InvalidArgumentException('invalid parameter: any "category" may only'
                    . ' contain a maximum of 255 characters each');
                }
                $this->_data['categories'][] = $value;
            } else {
                if (iconv_strlen($key, $this->getEncoding()) > 255) {
                    throw new Writer\Exception\InvalidArgumentException('invalid parameter: any "category" may only'
                    . ' contain a maximum of 255 characters each');
                }
                $this->_data['categories'][$key] = array();
                foreach ($value as $val) {
                    if (iconv_strlen($val, $this->getEncoding()) > 255) {
                        throw new Writer\Exception\InvalidArgumentException('invalid parameter: any "category" may only'
                        . ' contain a maximum of 255 characters each');
                    }
                    $this->_data['categories'][$key][] = $val;
                } 
            }
        }
        return $this;
    }
    
    /**
     * Set feed image (icon)
     * 
     * @param  string $value 
     * @return Feed
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesImage($value)
    {
        if (!Uri\UriFactory::factory($value)->isValid()) {
            throw new Writer\Exception\InvalidArgumentException('invalid parameter: "image" may only'
            . ' be a valid URI/IRI');
        }
        if (!in_array(substr($value, -3), array('jpg','png'))) {
            throw new Writer\Exception\InvalidArgumentException('invalid parameter: "image" may only'
            . ' use file extension "jpg" or "png" which must be the last three'
            . ' characters of the URI (i.e. no query string or fragment)');
        }
        $this->_data['image'] = $value;
        return $this;
    }
    
    /**
     * Set feed cumulative duration
     * 
     * @param  string $value 
     * @return Feed
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesDuration($value)
    {
        $value = (string) $value;
        if (!ctype_digit($value)
            && !preg_match("/^\d+:[0-5]{1}[0-9]{1}$/", $value)
            && !preg_match("/^\d+:[0-5]{1}[0-9]{1}:[0-5]{1}[0-9]{1}$/", $value)
        ) {
            throw new Writer\Exception\InvalidArgumentException('invalid parameter: "duration" may only'
            . ' be of a specified [[HH:]MM:]SS format');
        }
        $this->_data['duration'] = $value;
        return $this;
    }
    
    /**
     * Set "explicit" flag
     * 
     * @param  bool $value 
     * @return Feed
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesExplicit($value)
    {
        if (!in_array($value, array('yes','no','clean'))) {
            throw new Writer\Exception\InvalidArgumentException('invalid parameter: "explicit" may only'
            . ' be one of "yes", "no" or "clean"');
        }
        $this->_data['explicit'] = $value;
        return $this;
    }
    
    /**
     * Set feed keywords
     * 
     * @param  array $value 
     * @return Feed
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesKeywords(array $value)
    {
        if (count($value) > 12) {
            throw new Writer\Exception\InvalidArgumentException('invalid parameter: "keywords" may only'
            . ' contain a maximum of 12 terms');
        }
        $concat = implode(',', $value);
        if (iconv_strlen($concat, $this->getEncoding()) > 255) {
            throw new Writer\Exception\InvalidArgumentException('invalid parameter: "keywords" may only'
            . ' have a concatenated length of 255 chars where terms are delimited'
            . ' by a comma');
        }
        $this->_data['keywords'] = $value;
        return $this;
    }
    
    /**
     * Set new feed URL
     * 
     * @param  string $value 
     * @return Feed
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesNewFeedUrl($value)
    {
        if (!Uri\UriFactory::factory($value)->isValid()) {
            throw new Writer\Exception\InvalidArgumentException('invalid parameter: "newFeedUrl" may only'
            . ' be a valid URI/IRI');
        }
        $this->_data['newFeedUrl'] = $value;
        return $this;
    }
    
    /**
     * Add feed owners
     * 
     * @param  array $values 
     * @return Feed
     */
    public function addItunesOwners(array $values)
    {
        foreach ($values as $value) {
            $this->addItunesOwner($value); 
        }
        return $this;
    }
    
    /**
     * Add feed owner
     * 
     * @param  string $value 
     * @return Feed
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function addItunesOwner(array $value)
    {
        if (!isset($value['name']) || !isset($value['email'])) {
            throw new Writer\Exception\InvalidArgumentException('invalid parameter: any "owner" must'
            . ' be an array containing keys "name" and "email"');
        }
        if (iconv_strlen($value['name'], $this->getEncoding()) > 255
            || iconv_strlen($value['email'], $this->getEncoding()) > 255
        ) {
            throw new Writer\Exception\InvalidArgumentException('invalid parameter: any "owner" may only'
            . ' contain a maximum of 255 characters each for "name" and "email"');
        }
        if (!isset($this->_data['owners'])) {
            $this->_data['owners'] = array();
        }
        $this->_data['owners'][] = $value;
        return $this;
    }
    
    /**
     * Set feed subtitle
     * 
     * @param  string $value 
     * @return Feed
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesSubtitle($value)
    {
        if (iconv_strlen($value, $this->getEncoding()) > 255) {
            throw new Writer\Exception\InvalidArgumentException('invalid parameter: "subtitle" may only'
            . ' contain a maximum of 255 characters');
        }
        $this->_data['subtitle'] = $value;
        return $this;
    }
    
    /**
     * Set feed summary
     * 
     * @param  string $value 
     * @return Feed
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesSummary($value)
    {
        if (iconv_strlen($value, $this->getEncoding()) > 4000) {
            throw new Writer\Exception\InvalidArgumentException('invalid parameter: "summary" may only'
            . ' contain a maximum of 4000 characters');
        }
        $this->_data['summary'] = $value;
        return $this;
    }
    
    /**
     * Overloading: proxy to internal setters
     * 
     * @param  string $method 
     * @param  array $params 
     * @return mixed
     * @throws Writer\Exception\BadMethodCallException
     */
    public function __call($method, array $params)
    {
        $point = lcfirst(substr($method, 9));
        if (!method_exists($this, 'setItunes' . ucfirst($point))
            && !method_exists($this, 'addItunes' . ucfirst($point))
        ) {
            throw new Writer\Exception\BadMethodCallException(
                'invalid method: ' . $method
            );
        }
        if (!array_key_exists($point, $this->_data) || empty($this->_data[$point])) {
            return null;
        }
        return $this->_data[$point];
    }
}

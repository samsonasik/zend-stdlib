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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Feed\Writer;

use Zend\Uri,
    Zend\Date,
    Zend\Validator;

/**
 * @category Zend
 * @package Zend_Feed_Writer
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */
class AbstractFeed
{
    /**
     * Contains all Feed level date to append in feed output
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Holds the value "atom" or "rss" depending on the feed type set when
     * when last exported.
     *
     * @var string
     */
    protected $_type = null;

    /**
     * Constructor: Primarily triggers the registration of core extensions and
     * loads those appropriate to this data container.
     *
     * @return void
     */
    public function __construct()
    {
        Writer::registerCoreExtensions();
        $this->_loadExtensions();
    }

    /**
     * Set a single author
     *
     * The following option keys are supported:
     * 'name'  => The name
     * 'email' => An optional email
     * 'uri'   => An optional uri
     *
     * @param array $author
     * @throws Exception\InvalidArgumentException If any value of $author not follow the format.
     * @return void
     */
    public function addAuthor(array $author)
    {
        // Check array values
        if (!array_key_exists('name', $author)
            || empty($author['name'])
            || !is_string($author['name'])
        ) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: author array must include a "name" key with a non-empty string value');
        }

        if (isset($author['email'])) {
            if (empty($author['email']) || !is_string($author['email'])) {
                throw new Exception\InvalidArgumentException(
                    'Invalid parameter: "email" array value must be a non-empty string');
            }
        }
        if (isset($author['uri'])) {
            if (empty($author['uri']) || !is_string($author['uri']) ||
                !Uri\UriFactory::factory($author['uri'])->isValid()
            ) {
                throw new Exception\InvalidArgumentException(
                    'Invalid parameter: "uri" array value must be a non-empty string and valid URI/IRI');
            }
        }

        $this->_data['authors'][] = $author;
    }

    /**
     * Set an array with feed authors
     *
     * @see addAuthor
     * @param array $authors
     * @return void
     */
    public function addAuthors(array $authors)
    {
        foreach($authors as $author) {
            $this->addAuthor($author);
        }
    }

    /**
     * Set the copyright entry
     *
     * @return string|null
     */
    public function setCopyright($copyright)
    {
        if (empty($copyright) || !is_string($copyright)) {
            throw new Exception('Invalid parameter: parameter must be a non-empty string');
        }
        $this->_data['copyright'] = $copyright;
    }

    /**
     * Set the feed creation date
     *
     * @param null|integer|Date\Date
     */
    public function setDateCreated($date = null)
    {
        $zdate = null;
        if ($date === null) {
            $zdate = new Date\Date;
        } elseif (ctype_digit((string)$date)) {
            $zdate = new Date\Date($date, Date\Date::TIMESTAMP);
        } elseif ($date instanceof Date\Date) {
            $zdate = $date;
        } else {
            throw new Exception('Invalid Date\Date object or UNIX Timestamp passed as parameter');
        }
        $this->_data['dateCreated'] = $zdate;
    }

    /**
     * Set the feed modification date
     *
     * @param null|integer|Date\Date
     */
    public function setDateModified($date = null)
    {
        $zdate = null;
        if ($date === null) {
            $zdate = new Date\Date;
        } elseif (ctype_digit((string)$date)) {
            $zdate = new Date\Date($date, Date\Date::TIMESTAMP);
        } elseif ($date instanceof Date\Date) {
            $zdate = $date;
        } else {
            throw new Exception('Invalid Date\Date object or UNIX Timestamp passed as parameter');
        }
        $this->_data['dateModified'] = $zdate;
    }

    /**
     * Set the feed last-build date. Ignored for Atom 1.0.
     *
     * @param null|integer|Date\Date
     */
    public function setLastBuildDate($date = null)
    {
        $zdate = null;
        if ($date === null) {
            $zdate = new Date\Date;
        } elseif (ctype_digit((string)$date)) {
            $zdate = new Date\Date($date, Date\Date::TIMESTAMP);
        } elseif ($date instanceof Date\Date) {
            $zdate = $date;
        } else {
            throw new Exception('Invalid Date\Date object or UNIX Timestamp passed as parameter');
        }
        $this->_data['lastBuildDate'] = $zdate;
    }

    /**
     * Set the feed description
     *
     * @return string|null
     */
    public function setDescription($description)
    {
        if (empty($description) || !is_string($description)) {
            throw new Exception('Invalid parameter: parameter must be a non-empty string');
        }
        $this->_data['description'] = $description;
    }

    /**
     * Set the feed generator entry
     *
     * @return string|null
     */
    public function setGenerator($name, $version = null, $uri = null)
    {
        if (is_array($name)) {
            $data = $name;
            if (empty($data['name']) || !is_string($data['name'])) {
                throw new Exception('Invalid parameter: "name" must be a non-empty string');
            }
            $generator = array('name' => $data['name']);
            if (isset($data['version'])) {
                if (empty($data['version']) || !is_string($data['version'])) {
                    throw new Exception('Invalid parameter: "version" must be a non-empty string');
                }
                $generator['version'] = $data['version'];
            }
            if (isset($data['uri'])) {
                if (empty($data['uri']) || !is_string($data['uri']) || !Uri\UriFactory::factory($data['uri'])->isValid()) {
                    throw new Exception('Invalid parameter: "uri" must be a non-empty string and a valid URI/IRI');
                }
                $generator['uri'] = $data['uri'];
            }
        } else {
            if (empty($name) || !is_string($name)) {
                throw new Exception('Invalid parameter: "name" must be a non-empty string');
            }
            $generator = array('name' => $name);
            if (isset($version)) {
                if (empty($version) || !is_string($version)) {
                    throw new Exception('Invalid parameter: "version" must be a non-empty string');
                }
                $generator['version'] = $version;
            }
            if (isset($uri)) {
                if (empty($uri) || !is_string($uri) || !Uri\UriFactory::factory($uri)->isValid()) {
                    throw new Exception('Invalid parameter: "uri" must be a non-empty string and a valid URI/IRI');
                }
                $generator['uri'] = $uri;
            }
        }
        $this->_data['generator'] = $generator;
    }

    /**
     * Set the feed ID - URI or URN (via PCRE pattern) supported
     *
     * @param string $id
     */
    public function setId($id)
    {
        if ((empty($id) || !is_string($id) || !Uri\UriFactory::factory($id)->isValid())
            && !preg_match("#^urn:[a-zA-Z0-9][a-zA-Z0-9\-]{1,31}:([a-zA-Z0-9\(\)\+\,\.\:\=\@\;\$\_\!\*\-]|%[0-9a-fA-F]{2})*#", $id)
            && !$this->_validateTagUri($id)
        ) {
            throw new Exception('Invalid parameter: parameter must be a non-empty string and valid URI/IRI');
        }
        $this->_data['id'] = $id;
    }

    /**
     * Validate a URI using the tag scheme (RFC 4151)
     *
     * @param string $id
     * @return bool
     */
    protected function _validateTagUri($id)
    {
        if (preg_match('/^tag:(?P<name>.*),(?P<date>\d{4}-?\d{0,2}-?\d{0,2}):(?P<specific>.*)(.*:)*$/', $id, $matches)) {
            $dvalid = false;
            $nvalid = false;
            $date = $matches['date'];
            $d6 = strtotime($date);
            if ((strlen($date) == 4) && $date <= date('Y')) {
                $dvalid = true;
            } elseif ((strlen($date) == 7) && ($d6 < strtotime("now"))) {
                $dvalid = true;
            } elseif ((strlen($date) == 10) && ($d6 < strtotime("now"))) {
                $dvalid = true;
            }
            $validator = new Validator\EmailAddress;
            if ($validator->isValid($matches['name'])) {
                $nvalid = true;
            } else {
                $nvalid = $validator->isValid('info@' . $matches['name']);
            }
            return $dvalid && $nvalid;

        }
        return false;
    }

    /**
     * Set a feed image (URI at minimum). Parameter is a single array with the
     * required key 'uri'. When rendering as RSS, the required keys are 'uri',
     * 'title' and 'link'. RSS also specifies three optional parameters 'width',
     * 'height' and 'description'. Only 'uri' is required and used for Atom rendering.
     *
     * @param array $data
     */
    public function setImage(array $data)
    {
        if (empty($data['uri']) || !is_string($data['uri'])
            || !Uri\UriFactory::factory($data['uri'])->isValid()
        ) {
            throw new Exception('Invalid parameter: parameter \'uri\''
            . ' must be a non-empty string and valid URI/IRI');
        }
        $this->_data['image'] = $data;
    }

    /**
     * Set the feed language
     *
     * @return string|null
     */
    public function setLanguage($language)
    {
        if (empty($language) || !is_string($language)) {
            throw new Exception('Invalid parameter: parameter must be a non-empty string');
        }
        $this->_data['language'] = $language;
    }

    /**
     * Set a link to the HTML source
     *
     * @param string $link
     */
    public function setLink($link)
    {
        if (empty($link) || !is_string($link) || !Uri\UriFactory::factory($link)->isValid()) {
            throw new Exception('Invalid parameter: parameter must be a non-empty string and valid URI/IRI');
        }
        $this->_data['link'] = $link;
    }

    /**
     * Set a link to an XML feed for any feed type/version
     *
     * @return string|null
     */
    public function setFeedLink($link, $type)
    {
        if (empty($link) || !is_string($link) || !Uri\UriFactory::factory($link)->isValid()) {
            throw new Exception('Invalid parameter: "link"" must be a non-empty string and valid URI/IRI');
        }
        if (!in_array(strtolower($type), array('rss', 'rdf', 'atom'))) {
            throw new Exception('Invalid parameter: "type"; You must declare the type of feed the link points to, i.e. RSS, RDF or Atom');
        }
        $this->_data['feedLinks'][strtolower($type)] = $link;
    }

    /**
     * Set the feed title
     *
     * @return string|null
     */
    public function setTitle($title)
    {
        if (empty($title) || !is_string($title)) {
            throw new Exception('Invalid parameter: parameter must be a non-empty string');
        }
        $this->_data['title'] = $title;
    }

    /**
     * Set the feed character encoding
     *
     * @param string $encoding
     */
    public function setEncoding($encoding)
    {
        if (empty($encoding) || !is_string($encoding)) {
            throw new Exception('Invalid parameter: parameter must be a non-empty string');
        }
        $this->_data['encoding'] = $encoding;
    }

    /**
     * Set the feed's base URL
     *
     * @param string $url
     */
    public function setBaseUrl($url)
    {
        if (empty($url) || !is_string($url) || !Uri\UriFactory::factory($url)->isValid()) {
            throw new Exception('Invalid parameter: "url" array value'
            . ' must be a non-empty string and valid URI/IRI');
        }
        $this->_data['baseUrl'] = $url;
    }

    /**
     * Add a Pubsubhubbub hub endpoint URL
     *
     * @param string $url
     */
    public function addHub($url)
    {
        if (empty($url) || !is_string($url) || !Uri\UriFactory::factory($url)->isValid()) {
            throw new Exception('Invalid parameter: "url" array value'
            . ' must be a non-empty string and valid URI/IRI');
        }
        if (!isset($this->_data['hubs'])) {
            $this->_data['hubs'] = array();
        }
        $this->_data['hubs'][] = $url;
    }

    /**
     * Add Pubsubhubbub hub endpoint URLs
     *
     * @param array $urls
     */
    public function addHubs(array $urls)
    {
        foreach ($urls as $url) {
            $this->addHub($url);
        }
    }

    /**
     * Add a feed category
     *
     * @param string $category
     */
    public function addCategory(array $category)
    {
        if (!isset($category['term'])) {
            throw new Exception('Each category must be an array and '
            . 'contain at least a "term" element containing the machine '
            . ' readable category name');
        }
        if (isset($category['scheme'])) {
            if (empty($category['scheme'])
                || !is_string($category['scheme'])
                || !Uri\UriFactory::factory($category['scheme'])->isValid()
            ) {
                throw new Exception('The Atom scheme or RSS domain of'
                . ' a category must be a valid URI');
            }
        }
        if (!isset($this->_data['categories'])) {
            $this->_data['categories'] = array();
        }
        $this->_data['categories'][] = $category;
    }

    /**
     * Set an array of feed categories
     *
     * @param array $categories
     */
    public function addCategories(array $categories)
    {
        foreach ($categories as $category) {
            $this->addCategory($category);
        }
    }

    /**
     * Get a single author
     *
     * @param  int $index
     * @return string|null
     */
    public function getAuthor($index = 0)
    {
        if (isset($this->_data['authors'][$index])) {
            return $this->_data['authors'][$index];
        } else {
            return null;
        }
    }

    /**
     * Get an array with feed authors
     *
     * @return array
     */
    public function getAuthors()
    {
        if (!array_key_exists('authors', $this->_data)) {
            return null;
        }
        return $this->_data['authors'];
    }

    /**
     * Get the copyright entry
     *
     * @return string|null
     */
    public function getCopyright()
    {
        if (!array_key_exists('copyright', $this->_data)) {
            return null;
        }
        return $this->_data['copyright'];
    }

    /**
     * Get the feed creation date
     *
     * @return string|null
     */
    public function getDateCreated()
    {
        if (!array_key_exists('dateCreated', $this->_data)) {
            return null;
        }
        return $this->_data['dateCreated'];
    }

    /**
     * Get the feed modification date
     *
     * @return string|null
     */
    public function getDateModified()
    {
        if (!array_key_exists('dateModified', $this->_data)) {
            return null;
        }
        return $this->_data['dateModified'];
    }

    /**
     * Get the feed last-build date
     *
     * @return string|null
     */
    public function getLastBuildDate()
    {
        if (!array_key_exists('lastBuildDate', $this->_data)) {
            return null;
        }
        return $this->_data['lastBuildDate'];
    }

    /**
     * Get the feed description
     *
     * @return string|null
     */
    public function getDescription()
    {
        if (!array_key_exists('description', $this->_data)) {
            return null;
        }
        return $this->_data['description'];
    }

    /**
     * Get the feed generator entry
     *
     * @return string|null
     */
    public function getGenerator()
    {
        if (!array_key_exists('generator', $this->_data)) {
            return null;
        }
        return $this->_data['generator'];
    }

    /**
     * Get the feed ID
     *
     * @return string|null
     */
    public function getId()
    {
        if (!array_key_exists('id', $this->_data)) {
            return null;
        }
        return $this->_data['id'];
    }

    /**
     * Get the feed image URI
     *
     * @return array
     */
    public function getImage()
    {
        if (!array_key_exists('image', $this->_data)) {
            return null;
        }
        return $this->_data['image'];
    }

    /**
     * Get the feed language
     *
     * @return string|null
     */
    public function getLanguage()
    {
        if (!array_key_exists('language', $this->_data)) {
            return null;
        }
        return $this->_data['language'];
    }

    /**
     * Get a link to the HTML source
     *
     * @return string|null
     */
    public function getLink()
    {
        if (!array_key_exists('link', $this->_data)) {
            return null;
        }
        return $this->_data['link'];
    }

    /**
     * Get a link to the XML feed
     *
     * @return string|null
     */
    public function getFeedLinks()
    {
        if (!array_key_exists('feedLinks', $this->_data)) {
            return null;
        }
        return $this->_data['feedLinks'];
    }

    /**
     * Get the feed title
     *
     * @return string|null
     */
    public function getTitle()
    {
        if (!array_key_exists('title', $this->_data)) {
            return null;
        }
        return $this->_data['title'];
    }

    /**
     * Get the feed character encoding
     *
     * @return string|null
     */
    public function getEncoding()
    {
        if (!array_key_exists('encoding', $this->_data)) {
            return 'UTF-8';
        }
        return $this->_data['encoding'];
    }

    /**
     * Get the feed's base url
     *
     * @return string|null
     */
    public function getBaseUrl()
    {
        if (!array_key_exists('baseUrl', $this->_data)) {
            return null;
        }
        return $this->_data['baseUrl'];
    }

    /**
     * Get the URLs used as Pubsubhubbub hubs endpoints
     *
     * @return string|null
     */
    public function getHubs()
    {
        if (!array_key_exists('hubs', $this->_data)) {
            return null;
        }
        return $this->_data['hubs'];
    }

    /**
     * Get the feed categories
     *
     * @return string|null
     */
    public function getCategories()
    {
        if (!array_key_exists('categories', $this->_data)) {
            return null;
        }
        return $this->_data['categories'];
    }

    /**
     * Resets the instance and deletes all data
     *
     * @return void
     */
    public function reset()
    {
        $this->_data = array();
    }

    /**
     * Set the current feed type being exported to "rss" or "atom". This allows
     * other objects to gracefully choose whether to execute or not, depending
     * on their appropriateness for the current type, e.g. renderers.
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * Retrieve the current or last feed type exported.
     *
     * @return string Value will be "rss" or "atom"
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Unset a specific data point
     *
     * @param string $name
     */
    public function remove($name)
    {
        if (isset($this->_data[$name])) {
            unset($this->_data[$name]);
        }
    }

    /**
     * Method overloading: call given method on first extension implementing it
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws Exception if no extensions implements the method
     */
    public function __call($method, $args)
    {
        foreach ($this->_extensions as $extension) {
            try {
                return call_user_func_array(array($extension, $method), $args);
            } catch (Exception\InvalidMethodException $e) {
            }
        }
        throw new Exception('Method: ' . $method
            . ' does not exist and could not be located on a registered Extension');
    }

    /**
     * Load extensions from Zend_Feed_Writer
     *
     * @return void
     */
    protected function _loadExtensions()
    {
        $all = Writer::getExtensions();
        $exts = $all['feed'];
        foreach ($exts as $ext) {
            if (!$className = Writer::getPluginLoader()->getClassName($ext)) {
                throw new Exception(sprintf('Unable to load extension "%s"; could not resolve to class', $ext));
            }
            $this->_extensions[$ext] = new $className();
            $this->_extensions[$ext]->setEncoding($this->getEncoding());
        }
    }
}

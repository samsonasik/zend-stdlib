<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

use MongoCollection as MongoResource;
use MongoException as MongoResourceException;
use stdClass;
use Zend\Cache\Exception;
use Zend\Cache\Storage\Capabilities;
use Zend\Cache\Storage\FlushableInterface;

class MongoDB extends AbstractAdapter implements FlushableInterface
{
    /**
     * Has this instance be initialized
     *
     * @var bool
     */
    protected $initialized = false;

    /**
     * the mongodb resource manager
     *
     * @var null|MongoDBResourceManager
     */
    protected $resourceManager;

    /**
     * The mongodb resource id
     *
     * @var null|string
     */
    protected $resourceId;

    /**
     * The namespace prefix
     *
     * @var string
     */
    protected $namespacePrefix = '';

    /**
     * __construct
     *
     * @param mixed $options
     * @throws \Zend\Cache\Exception\ExtensionNotLoadedException
     */
    public function __construct($options = null)
    {
        if (!extension_loaded('mongo')) {
            throw new Exception\ExtensionNotLoadedException('MongoDB extension not loaded');
        }

        parent::__construct($options);

        $initialized = & $this->initialized;
        $this->getEventManager()->attach('option', function ($event) use (& $initialized) {
            $initialized = false;
        });
    }

    /**
     * get mongodb resource
     *
     * @return MongoResource
     */
    protected function getMongoDBResource()
    {
        if (!$this->initialized) {
            $options = $this->getOptions();

            $this->resourceManager = $options->getResourceManager();
            $this->resourceId      = $options->getResourceId();

            $namespace = $options->getNamespace();
            if ($namespace !== '') {
                $this->namespacePrefix = $namespace . $options->getNamespaceSeparator();
            } else {
                $this->namespacePrefix = '';
            }

            $this->initialized = true;
        }

        return $this->resourceManager->getResource($this->resourceId);
    }

    /**
     * Set options.
     *
     * @param  array|Traversable|MongoDBOptions $options
     * @return MongoDB
     * @see    getOptions()
     */
    public function setOptions($options)
    {
        if (!$options instanceof MongoDBOptions) {
            $options = new MongoDBOptions($options);
        }

        return parent::setOptions($options);
    }

    /**
     * Get options.
     *
     * @return MongoDBOptions
     * @see setOptions()
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Internal method to get an item.
     *
     * @param string $normalizedKey
     * @param bool $success
     * @param mixed $casToken
     * @return string|null
     * @throws Exception\RuntimeException
     */
    protected function internalGetItem(& $normalizedKey, & $success = null, & $casToken = null)
    {
        $collection = $this->getMongoDBResource();

        $key = $this->prefixNamespaceToKey($normalizedKey);

        try {
            $result = $collection->findOne(
                array('key' => $key)
            );

        } catch (MongoResourceException $e) {
            throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        // MongoCollection::findOne returns record matching search or null
        if ($result === null) {
            $success = false;

            return null;
        }

        if ($result['expires'] !== null && $result['expires']->sec < time()) {
            $success = false;
            $this->internalRemoveItem($key);

            return null;
        }

        $value = $result['value'];

        $success = true;
        $casToken = $value;

        return $value;
    }

    /**
     * Internal method to store an item.
     *
     * @param  string $normalizedKey
     * @param  mixed $value
     * @return bool
     * @throws Exception\RuntimeException
     */
    protected function internalSetItem(& $normalizedKey, & $value)
    {
        $mongo = $this->getMongoDBResource();

        $key = $this->prefixNamespaceToKey($normalizedKey);

        $ttl = $this->getOptions()->getTTl();

        if ($ttl > 0) {
            $expires = $this->getOptions()->getResourceManager()->createMongoDate(time() + $ttl);
        } else {
            $expires = null;
        }

        $cacheItem = array(
            'key' => $key,
            'value' => $value,
            'expires' => $expires,
        );



        try {
            $mongo->remove(array('key' => $key));
            $result = $mongo->insert($cacheItem);
        } catch (MongoResourceException $e) {
            throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        if ($result === null) {
            return false;
        }

        if ($result['ok'] === (double) 1) {
            return true;
        }

        return false;
    }

    /**
     * Internal method to remove an item.
     *
     * @param  string $normalizedKey
     * @return bool
     * @throws Exception\RuntimeException
     */
    protected function internalRemoveItem(& $normalizedKey)
    {
        $mongo = $this->getMongoDBResource();

        $key = $this->prefixNamespaceToKey($normalizedKey);

        $deleteItem = array('key' => $key);

        try {
            $result = $mongo->remove($deleteItem);
        } catch (MongoResourceException $e) {
            throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        if ($result === false) {
            return false;
        }

        if ($result['ok'] === (double) 1 && $result['n'] > 0) {
            return true;
        }

        return false;
    }

    /**
     * Prefix namespace to key
     *
     * @param string $key
     * @return void
     */
    protected function prefixNamespaceToKey($key)
    {
        $namespace = $this->getOptions()->getNamespace();
        $prefix = ($namespace === '') ? '' : $namespace . $this->namespacePrefix;
        return $prefix . $key;
    }

    /**
     * Flush the whole storage
     *
     * @return bool
     */
    public function flush()
    {
        $result = $this->getMongoDBResource()->drop();

        if ($result['ok'] === (double) 1) {
            return true;
        }

        return false;
    }

    /**
     * Internal method to get capabilities
     *
     * @return void|\Zend\Cache\Storage\Capabilities
     */
    protected function internalGetCapabilities()
    {
        if ($this->capabilities === null) {
            $this->capabilityMarker = new stdClass();
            $this->capabilities = new Capabilities(
                $this,
                $this->capabilityMarker,
                array(
                    'supportedDatatypes' => array(
                        'NULL'     => true,
                        'boolean'  => true,
                        'integer'  => true,
                        'double'   => true,
                        'string'   => true,
                        'array'    => true,
                        'object'   => false,
                        'resource' => false,
                    ),
                    'supportedMetadata'  => array(),
                    'minTtl'             => 0,
                    'maxTtl'             => 0,
                    'staticTtl'          => true,
                    'ttlPrecision'       => 1,
                    'useRequestTime'     => false,
                    'expiredRead'        => false,
                    'maxKeyLength'       => 255,
                    'namespaceIsPrefix'  => true,
                )
            );
        }

        return $this->capabilities;
    }
}

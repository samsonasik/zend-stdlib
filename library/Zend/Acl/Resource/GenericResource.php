<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Acl
 */

namespace Zend\Acl\Resource;

use Zend\Acl\Resource\ResourceInterface;

/**
 * @category   Zend
 * @package    Zend_Acl
 */
class GenericResource implements ResourceInterface
{
    /**
     * Unique id of Resource
     *
     * @var string
     */
    protected $resourceId;

    /**
     * Sets the Resource identifier
     *
     * @param  string $resourceId
     */
    public function __construct($resourceId)
    {
        $this->resourceId = (string) $resourceId;
    }

    /**
     * Defined by Zend\Acl\Resource; returns the Resource identifier
     *
     * @return string
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * Defined by Zend\Acl\Resource; returns the Resource identifier
     * Proxies to getResourceId()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getResourceId();
    }
}

<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace ZendTest\ServiceManager\TestAsset;

use Zend\ServiceManager\DelegateFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Mock factory that logs delegated instances and returns itself
 */
class MockSelfReturningDelegateFactory implements DelegateFactoryInterface
{
    /**
     * @var mixed[]
     */
    public $instances = array();

    /**
     * {@inheritDoc}
     */
    public function createDelegateWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        $this->instances[] = call_user_func($callback);

        return $this;
    }
}
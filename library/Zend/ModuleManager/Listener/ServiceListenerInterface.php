<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ModuleManager
 */

namespace Zend\ModuleManager\Listener;

/**
 * @category   Zend
 * @package    Zend_ModuleManager
 * @subpackage Listener
 */
interface ServiceListenerInterface
{
    /**
     * @param  ServiceManager|string $serviceManager  Service Manager instance or name
     * @param  string                $key             Configuration key
     * @param  string                $moduleInterface FQCN as string
     * @param  string                $method          Method name
     * @return ServiceListenerInterface
     */
    public function addServiceManager($serviceManager, $key, $moduleInterface, $method);

    /**
     * @param  array $configuration
     * @return ServiceListenerInterface
     */
    public function setDefaultServiceConfig($configuration);
}

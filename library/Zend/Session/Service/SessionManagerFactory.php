<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Session\Service;

use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Config\ConfigInterface;
use Zend\Session\Container;
use Zend\Session\SaveHandler\SaveHandlerInterface;
use Zend\Session\SessionManager;
use Zend\Session\Storage\StorageInterface;

class SessionManagerFactory implements FactoryInterface
{
    /**
     * Create session manager object
     *
     * Will consume any combination (or zero) of the following services, when
     * present, to construct the SessionManager instance:
     *
     * - Zend\Session\Config\ConfigInterface
     * - Zend\Session\Storage\StorageInterface
     * - Zend\Session\SaveHandler\SaveHandlerInterface
     *
     * The first two have corresponding factories inside this namespace. The
     * last, however, does not, due to the differences in implementations, and
     * the fact that save handlers will often be written in userland. As such
     * if you wish to attach a save handler to the manager, you will need to
     * write your own factory, and assign it to the service name
     * "Zend\Session\SaveHandler\SaveHandlerInterface", (or alias that name
     * to your own service).
     *
     * @param  ServiceLocatorInterface $services
     * @return SessionManager
     * @throws ServiceNotCreatedException if any collaborators are not of the
     *         correct type
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $config      = null;
        $storage     = null;
        $saveHandler = null;

        if ($services->has('Zend\Session\Config\ConfigInterface')) {
            $config = $services->get('Zend\Session\Config\ConfigInterface');
            if (!$config instanceof ConfigInterface) {
                throw new ServiceNotCreatedException(sprintf(
                    'SessionManager requires that the %s service implement %s; received "%s"',
                    'Zend\Session\Config\ConfigInterface',
                    'Zend\Session\Config\ConfigInterface',
                    (is_object($config) ? get_class($config) : gettype($config))
                ));
            }
        }

        if ($services->has('Zend\Session\Storage\StorageInterface')) {
            $storage = $services->get('Zend\Session\Storage\StorageInterface');
            if (!$storage instanceof StorageInterface) {
                throw new ServiceNotCreatedException(sprintf(
                    'SessionManager requires that the %s service implement %s; received "%s"',
                    'Zend\Session\Storage\StorageInterface',
                    'Zend\Session\Storage\StorageInterface',
                    (is_object($storage) ? get_class($storage) : gettype($storage))
                ));
            }
        }

        if ($services->has('Zend\Session\SaveHandler\SaveHandlerInterface')) {
            $saveHandler = $services->get('Zend\Session\SaveHandler\SaveHandlerInterface');
            if (!$saveHandler instanceof SaveHandlerInterface) {
                throw new ServiceNotCreatedException(sprintf(
                    'SessionManager requires that the %s service implement %s; received "%s"',
                    'Zend\Session\SaveHandler\SaveHandlerInterface',
                    'Zend\Session\SaveHandler\SaveHandlerInterface',
                    (is_object($saveHandler) ? get_class($saveHandler) : gettype($saveHandler))
                ));
            }
        }

        $manager = new SessionManager($config, $storage, $saveHandler);
        Container::setDefaultManager($manager);
        return $manager;
    }
}

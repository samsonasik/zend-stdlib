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

use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\ModuleEvent;

/**
 * Autoloader listener
 *
 * @category   Zend
 * @package    Zend_ModuleManager
 * @subpackage Listener
 */
class OnBootstrapListener extends AbstractListener
{

    /**
     * @param  ModuleEvent $e
     * @return void
     */
    public function __invoke(ModuleEvent $e)
    {
        $module = $e->getModule();
        if (!$module instanceof BootstrapListenerInterface
            && !method_exists($module, 'onBootstrap')
        ) {
            return;
        }

        $moduleManager = $e->getTarget();
        $events        = $moduleManager->getEventManager();
        $sharedEvents  = $events->getSharedManager();
        $sharedEvents->attach('application', 'bootstrap', array($module, 'onBootstrap'));
    }
}

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
 * @package    Zend_XmlRpc
 * @subpackage Server
 */

namespace Zend\XmlRpc\Server;

/**
 * Zend_XmlRpc_Server_Cache: cache Zend_XmlRpc_Server server definition
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage Server
 */
class Cache extends \Zend\Server\Cache
{
    /**
     * @var array Skip system methods when caching XML-RPC server
     */
    protected static $_skipMethods = array(
        'system.listMethods',
        'system.methodHelp',
        'system.methodSignature',
        'system.multicall',
    );
}

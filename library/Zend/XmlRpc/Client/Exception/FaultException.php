<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\XmlRpc\Client\Exception;

use Zend\XmlRpc\Exception;

/**
 * Thrown by Zend_XmlRpc_Client when an XML-RPC fault response is returned.
 */
class FaultException extends Exception\BadMethodCallException implements ExceptionInterface
{
}

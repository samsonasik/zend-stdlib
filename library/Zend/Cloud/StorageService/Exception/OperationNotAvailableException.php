<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace Zend\Cloud\StorageService\Exception;

use Zend\Cloud\Exception;

/**
 * @category   Zend
 * @package    Zend_Cloud
 * @subpackage StorageService
 */
class OperationNotAvailableException extends Exception\OperationNotAvailableException implements 
    ExceptionInterface
{}

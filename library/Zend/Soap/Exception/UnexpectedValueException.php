<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Soap\Exception;

use UnexpectedValueException as SPLUnexpectedValueException;

/**
 * Exception thrown when provided arguments are invalid
 */
class UnexpectedValueException extends SPLUnexpectedValueException implements ExceptionInterface
{}

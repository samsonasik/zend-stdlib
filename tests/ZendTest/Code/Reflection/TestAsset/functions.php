<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace ZendTest\Code\Reflection\TestAsset;

/**
 * /!\ Don't fix this file with the coding style.
 * The class Zend\Code\Reflection\FunctionReflection must parse a lot of closure formats
 */

function function1()
{
    return 'function1';
}


/**
 * Zend Function Two
 *
 * This is the long description for funciton two
 *
 * @param unknown_type $one
 * @param unknown_type $two
 * @return string
 */
function function2($one, $two = 'two')
{
    return 'blah';
}


/**
 * Enter description here...
 *
 * @param string $one
 * @param int $two
 * @return true
 */
function function3($one, $two = 2)
{
    return true;
}

function function4($arg) {
    return 'function4';
}

function function5() { return 'function5'; }

function function6()
{
    $closure = function() { return 'bar'; };
    return 'function6';
}

$foo = 'foo'; function function7() { return 'function7'; }

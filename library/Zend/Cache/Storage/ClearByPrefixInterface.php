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
 * @package    Zend_Cache
 * @subpackage Storage
 */

namespace Zend\Cache\Storage;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 */
interface ClearByPrefixInterface
{
    /**
     * Remove items matching given prefix
     *
     * @param string $prefix
     * @return boolean
     */
    public function clearByPrefix($prefix);
}

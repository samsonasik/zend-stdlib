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
 * @package    Zend_Mail
 * @subpackage Storage
 */

namespace Zend\Mail\Storage\Message;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Storage
 */
interface MessageInterface
{
    /**
     * return toplines as found after headers
     *
     * @return string toplines
     */
    public function getTopLines();

    /**
     * check if flag is set
     *
     * @param mixed $flag a flag name, use constants defined in Zend\Mail\Storage
     * @return bool true if set, otherwise false
     */
    public function hasFlag($flag);

    /**
     * get all set flags
     *
     * @return array array with flags, key and value are the same for easy lookup
     */
    public function getFlags();
}

<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-webat this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Session
 */

namespace Zend\Session\Storage;

use Traversable,
    ArrayAccess,
    Serializable,
    Countable;

/**
 * Session storage interface
 *
 * Defines the minimum requirements for handling userland, in-script session 
 * storage (e.g., the $_SESSION superglobal array).
 *
 * @category   Zend
 * @package    Zend_Session
 */
interface StorageInterface extends Traversable, ArrayAccess, Serializable, Countable
{
    public function getRequestAccessTime();
    public function lock($key = null);
    public function isLocked($key = null);
    public function unlock($key = null);
    public function markImmutable();
    public function isImmutable();

    public function setMetadata($key, $value, $overwriteArray = false);
    public function getMetadata($key = null);

    public function clear($key = null);

    public function toArray();
    public function fromArray(array $array);
}

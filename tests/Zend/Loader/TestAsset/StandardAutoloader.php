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
 * @package    Loader
 * @subpackage UnitTests
 * @version    $Id$
 */

namespace ZendTest\Loader\TestAsset;

use Zend\Loader\StandardAutoloader as Psr0Autoloader;

/**
 * @category   Zend
 * @package    Loader
 * @subpackage UnitTests
 * @group      Loader
 */
class StandardAutoloader extends Psr0Autoloader
{
    /**
     * Get registered namespaces
     * 
     * @return array
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * Get registered prefixes
     * 
     * @return array
     */
    public function getPrefixes()
    {
        return $this->prefixes;
    }
}

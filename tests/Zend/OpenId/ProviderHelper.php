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
 * @package    Zend_OpenId
 * @subpackage UnitTests
 */

namespace ZendTest\OpenId;

use Zend\OpenId;

/**
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage UnitTests
 */
class ProviderHelper extends OpenId\Provider\GenericProvider
{
    public function genSecret($func)
    {
        return $this->_genSecret($func);
    }
}

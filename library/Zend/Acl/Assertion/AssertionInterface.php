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
 * @package    Zend_Acl
 */

namespace Zend\Acl\Assertion;

use Zend\Acl\Acl;
use Zend\Acl\Resource\ResourceInterface;
use Zend\Acl\Role\RoleInterface;

/**
 * @category   Zend
 * @package    Zend_Acl
 */
interface AssertionInterface
{
    /**
     * Returns true if and only if the assertion conditions are met
     *
     * This method is passed the ACL, Role, Resource, and privilege to which the authorization query applies. If the
     * $role, $resource, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param  Acl\Acl                        $acl
     * @param  Acl\Role\RoleInterface         $role
     * @param  Acl\Resource\ResourceInterface $resource
     * @param  string                         $privilege
     * @return boolean
     */
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null);
}

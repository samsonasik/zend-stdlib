<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Acl
 */

namespace Zend\Acl\Role;

use Zend\Acl\Role\RoleInterface;

/**
 * @category   Zend
 * @package    Zend_Acl
 */
class GenericRole implements RoleInterface
{
    /**
     * Unique id of Role
     *
     * @var string
     */
    protected $roleId;

    /**
     * Sets the Role identifier
     *
     * @param string $roleId
     */
    public function __construct($roleId)
    {
        $this->roleId = (string) $roleId;
    }

    /**
     * Defined by Zend\Acl\Role\RoleInterface; returns the Role identifier
     *
     * @return string
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * Defined by Zend\Acl\Role\RoleInterface; returns the Role identifier
     * Proxies to getRoleId()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getRoleId();
    }
}

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
 * @package    Zend_Ldap
 * @subpackage Schema
 */

namespace Zend\Ldap\Node\Schema\AttributeType;

use Zend\Ldap\Node\Schema;

/**
 * Zend\Ldap\Node\Schema\AttributeType\ActiveDirectory provides access to the attribute type
 * schema information on an Active Directory server.
 *
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage Schema
 */
class ActiveDirectory extends Schema\AbstractItem implements AttributeTypeInterface
{
    /**
     * Gets the attribute name
     *
     * @return string
     */
    public function getName()
    {
        return $this->ldapdisplayname[0];
    }

    /**
     * Gets the attribute OID
     *
     * @return string
     */
    public function getOid()
    {

    }

    /**
     * Gets the attribute syntax
     *
     * @return string
     */
    public function getSyntax()
    {

    }

    /**
     * Gets the attribute maximum length
     *
     * @return int|null
     */
    public function getMaxLength()
    {

    }

    /**
     * Returns if the attribute is single-valued.
     *
     * @return boolean
     */
    public function isSingleValued()
    {

    }

    /**
     * Gets the attribute description
     *
     * @return string
     */
    public function getDescription()
    {

    }
}

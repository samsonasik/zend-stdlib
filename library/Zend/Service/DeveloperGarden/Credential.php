<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 * @author     Marco Kaiser
 */
class Zend_Service_DeveloperGarden_Credential
{
    /**
     * Service Auth Username
     *
     * @var string
     */
    protected $_username = null;

    /**
     * Service Password
     *
     * @var string
     */
    protected $_password = null;

    /**
     * Service Realm - default t-online.de
     *
     * @var string
     */
    protected $_realm = 't-online.de';

    /**
     * constructor to init the internal data
     *
     * @param string $username
     * @param string $password
     * @param string $realm
     * @return Zend_Service_DeveloperGarden_Credential
     */
    public function __construct($username = null, $password = null, $realm = null)
    {
        if (!empty($username)) {
            $this->setUsername($username);
        }
        if (!empty($password)) {
            $this->setPassword($password);
        }
        if (!empty($realm)) {
            $this->setRealm($realm);
        }
    }

    /**
     * split the password into an array
     *
     * @param string $password
     * @throws Zend_Service_DeveloperGarden_Client_Exception
     * @return Zend_Service_DeveloperGarden_Client_AbstractClient
     */
    public function setPassword($password = null)
    {
        if (empty($password)) {
            throw new Zend_Service_DeveloperGarden_Client_Exception('Empty password not permitted.');
        }

        if (!is_string($password)) {
            throw new Zend_Service_DeveloperGarden_Client_Exception('Password must be a string.');
        }

        $this->_password = $password;
        return $this;
    }

    /**
     * returns the current configured password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * set the new login
     *
     * @param string $username
     * @throws Zend_Service_DeveloperGarden_Client_Exception
     * @return Zend_Service_DeveloperGarden_Client_AbstractClient
     */
    public function setUsername($username = null)
    {
        if (empty($username)) {
            throw new Zend_Service_DeveloperGarden_Client_Exception('Empty username not permitted.');
        }

        if (!is_string($username)) {
            throw new Zend_Service_DeveloperGarden_Client_Exception('Username must be a string.');
        }

        $this->_username = $username;
        return $this;
    }

    /**
     * returns the username
     *
     * if $withRealm == true we combine username and realm like
     * username@realm
     *
     * @param $boolean withRealm
     * @return string|null
     */
    public function getUsername($withRealm = false)
    {
        $retValue = $this->_username;
        if ($withRealm) {
            $retValue = sprintf(
                '%s@%s',
                $this->_username,
                $this->_realm
            );
        }
        return $retValue;
    }

    /**
     * set the new realm
     *
     * @param string $realm
     * @throws Zend_Service_DeveloperGarden_Client_Exception
     * @return Zend_Service_DeveloperGarden_Client_AbstractClient
     */
    public function setRealm($realm = null)
    {
        if (empty($realm)) {
            throw new Zend_Service_DeveloperGarden_Client_Exception('Empty realm not permitted.');
        }

        if (!is_string($realm)) {
            throw new Zend_Service_DeveloperGarden_Client_Exception('Realm must be a string.');
        }

        $this->_realm = $realm;
        return $this;
    }

    /**
     * returns the realm
     *
     * @return string|null
     */
    public function getRealm()
    {
        return $this->_realm;
    }
}


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
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 */

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 * @author     Marco Kaiser
 */
class Zend_Service_DeveloperGarden_SecurityTokenServer
    extends Zend_Service_DeveloperGarden_Client_AbstractClient
{
    /**
     * wsdl file
     *
     * @var string
     */
    protected $_wsdlFile = 'https://sts.idm.telekom.com/TokenService?wsdl';

    /**
     * wsdl file local
     *
     * @var string
     */
    protected $_wsdlFileLocal = 'Wsdl/TokenService.wsdl';

    /**
     * Response, Request Classmapping
     *
     * @var array
     *
     */
    protected $_classMap = array(
        'SecurityTokenResponse' => 'Zend_Service_DeveloperGarden_Response_SecurityTokenServer_SecurityTokenResponse',
        'getTokensResponse'     => 'Zend_Service_DeveloperGarden_Response_SecurityTokenServer_GetTokensResponse'
    );

    /**
     * does the login and return the specific response
     *
     * @return Zend_Service_DeveloperGarden_Response_SecurityTokenServer_SecurityTokenResponse
     */
    public function getLoginToken()
    {
        $token = Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getTokenFromCache(
            'securityToken'
        );

        if ($token === null
            || !$token->isValid()
        ) {
            $token = $this->getSoapClient()->login('login');
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setTokenToCache(
                'securityToken',
                $token
            );
        }

        return $token;
    }

    /**
     * returns the fetched token from token server
     *
     * @return Zend_Service_DeveloperGarden_Response_SecurityTokenServer_GetTokensResponse
     */
    public function getTokens()
    {
        $token = Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getTokenFromCache(
            'getTokens'
        );

        if ($token === null
            || !$token->isValid()
        ) {
            $token = $this->getSoapClient()->getTokens(array(
                'serviceId' => $this->_serviceAuthId
            ));
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setTokenToCache(
                'getTokens',
                $token
            );
        }
        return $token;
    }
}

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
 * @package    Zend_OAuth;
 */

namespace Zend\OAuth\Signature;
use Zend\Crypt\Hmac as HMACEncryption;

/**
 * @category   Zend
 * @package    Zend_OAuth
 */
class Hmac extends AbstractSignature
{
    /**
     * Sign a request
     * 
     * @param  array $params 
     * @param  mixed $method 
     * @param  mixed $url 
     * @return string
     */
    public function sign(array $params, $method = null, $url = null)
    {
        $binaryHash = HMACEncryption::compute(
            $this->_key,
            $this->_hashAlgorithm,
            $this->_getBaseSignatureString($params, $method, $url),
            HMACEncryption::OUTPUT_BINARY
        );
        return base64_encode($binaryHash);
    }
}

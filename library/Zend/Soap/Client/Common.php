<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Soap
 */

namespace Zend\Soap\Client;

if (extension_loaded('soap')) {

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage Client
 */
class Common extends \SoapClient
{
    /**
     * doRequest() pre-processing method
     *
     * @var callable
     */
    protected $doRequestCallback;

    /**
     * Common Soap Client constructor
     *
     * @param callable $doRequestCallback
     * @param string $wsdl
     * @param array $options
     */
    public function __construct($doRequestCallback, $wsdl, $options)
    {
        $this->doRequestCallback = $doRequestCallback;

        parent::__construct($wsdl, $options);
    }

    /**
     * Performs SOAP request over HTTP.
     * Overridden to implement different transport layers, perform additional
     * XML processing or other purpose.
     *
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int    $version
     * @param int    $oneWay
     * @return mixed
     */
    public function __doRequest($request, $location, $action, $version, $oneWay = null)
    {
        if ($oneWay === null) {
            return call_user_func($this->doRequestCallback, $this, $request,
                $location, $action, $version);
        }

        return call_user_func($this->doRequestCallback, $this, $request,
            $location, $action, $version, $oneWay);
    }
}

} // end if (extension_loaded('soap')

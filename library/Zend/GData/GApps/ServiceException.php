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
 * @package    Zend_Gdata
 * @subpackage GApps
 */

namespace Zend\GData\GApps;
use Zend\GData\App;

/**
 * Gdata GApps Exception class. This is thrown when an
 * AppsForYourDomainErrors message is received from the Google Apps
 * servers.
 *
 * Several different errors may be represented by this exception. For a list
 * of error codes available, see getErrorCode.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage GApps
 */
class ServiceException extends \Exception
{

    protected $_rootElement = "AppsForYourDomainErrors";

    /**
     * Array of Zend_Gdata_Error objects indexed by error code.
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Create a new ServiceException.
     *
     * @return array An array containing a collection of
     *          Zend_Gdata_GApps_Error objects.
     */
    public function __construct($errors = null) {
        parent::__construct("Server errors encountered");
        if ($errors !== null) {
            $this->setErrors($errors);
        }
    }

    /**
     * Add a single Error object to the list of errors received by the
     * server.
     *
     * @param \Zend\GData\GApps\Error $error An instance of an error returned
     *          by the server. The error's errorCode must be set.
     * @throws \Zend\GData\App\Exception
     */
    public function addError($error) {
        // Make sure that we don't try to index an error that doesn't
        // contain an index value.
        if ($error->getErrorCode() == null) {
            throw new App\Exception("Error encountered without corresponding error code.");
        }

        $this->_errors[$error->getErrorCode()] = $error;
    }

    /**
     * Set the list of errors as sent by the server inside of an
     * AppsForYourDomainErrors tag.
     *
     * @param array $array An associative array containing a collection of
     *          Zend_Gdata_GApps_Error objects. All errors must have their
     *          errorCode value set.
     * @throws \Zend\GData\App\Exception
     */
    public function setErrors($array) {
        $this->_errors = array();
        foreach ($array as $error) {
            $this->addError($error);
        }
    }

    /**
     * Get the list of errors as sent by the server inside of an
     * AppsForYourDomainErrors tag.
     *
     * @return array An associative array containing a collection of
     *          Zend_Gdata_GApps_Error objects, indexed by error code.
     */
    public function getErrors() {
        return $this->_errors;
    }

    /**
     * Return the Error object associated with a specific error code.
     *
     * @return \Zend\GData\GApps\Error The Error object requested, or null
     *              if not found.
     */
    public function getError($errorCode) {
        if (array_key_exists($errorCode, $this->_errors)) {
            $result = $this->_errors[$errorCode];
            return $result;
        } else {
            return null;
        }
    }

    /**
     * Check whether or not a particular error code was returned by the
     * server.
     *
     * @param integer $errorCode The error code to check against.
     * @return boolean Whether or not the supplied error code was returned
     *          by the server.
     */
    public function hasError($errorCode) {
        return array_key_exists($errorCode, $this->_errors);
    }

    /**
     * Import an AppsForYourDomain error from XML.
     *
     * @param string $string The XML data to be imported
     * @return \Zend\GData\GApps\ServiceException Provides a fluent interface.
     * @throws \Zend\GData\App\Exception
     */
    public function importFromString($string) {
        if ($string) {
            // Check to see if an AppsForYourDomainError exists
            //
            // track_errors is temporarily enabled so that if an error
            // occurs while parsing the XML we can append it to an
            // exception by referencing $php_errormsg
            @ini_set('track_errors', 1);
            $doc = new \DOMDocument();
            $success = @$doc->loadXML($string);
            @ini_restore('track_errors');

            if (!$success) {
                // $php_errormsg is automatically generated by PHP if
                // an error occurs while calling loadXML(), above.
                throw new App\Exception("DOMDocument cannot parse XML: $php_errormsg");
            }

            // Ensure that the outermost node is an AppsForYourDomain error.
            // If it isn't, something has gone horribly wrong.
            $rootElement = $doc->getElementsByTagName($this->_rootElement)->item(0);
            if (!$rootElement) {
                throw new App\Exception('No root <' . $this->_rootElement . '> element found, cannot parse feed.');
            }

            foreach ($rootElement->childNodes as $errorNode) {
                if (!($errorNode instanceof \DOMText)) {
                    $error = new Error();
                    $error->transferFromDom($errorNode);
                    $this->addError($error);
                }
            }
            return $this;
        } else {
            throw new App\Exception('XML passed to transferFromXML cannot be null');
        }

    }

    /**
     * Get a human readable version of this exception.
     *
     * @return string
     */
    public function __toString() {
        $result = "The server encountered the following errors processing the request:";
        foreach ($this->_errors as $error) {
            $result .= "\n" . $error->__toString();
        }
        return $result;
    }
}

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
 * @subpackage Photos
 */

namespace Zend\GData\Photos\Extension;

/**
 * Represents the gphoto:timestamp element used by the API.
 * The timestamp of a photo in milliseconds since January 1, 1970.
 * This date is either set externally or based on EXIF data.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Photos
 */
class Timestamp extends \Zend\GData\Extension
{

    protected $_rootNamespace = 'gphoto';
    protected $_rootElement = 'timestamp';

    /**
     * Constructs a new Zend_Gdata_Photos_Extension_Timestamp object.
     *
     * @param string $text (optional) The value to represent.
     */
    public function __construct($text = null)
    {
        $this->registerAllNamespaces(\Zend\GData\Photos::$namespaces);
        parent::__construct();
        $this->setText($text);
    }

}

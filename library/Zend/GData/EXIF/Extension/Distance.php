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
 * @subpackage Exif
 */

namespace Zend\GData\EXIF\Extension;

/**
 * Represents the exif:distance element used by the Gdata Exif extensions.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Exif
 */
class Distance extends \Zend\GData\Extension
{

    protected $_rootNamespace = 'exif';
    protected $_rootElement = 'distance';

    /**
     * Constructs a new Zend_Gdata_Exif_Extension_Distance object.
     *
     * @param string $text (optional) The value to use for this element.
     */
    public function __construct($text = null)
    {
        $this->registerAllNamespaces(\Zend\GData\EXIF::$namespaces);
        parent::__construct();
        $this->setText($text);
    }

}

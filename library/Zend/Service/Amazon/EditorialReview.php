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
 * @subpackage Amazon
 */

namespace Zend\Service\Amazon;

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon
 */
class EditorialReview
{
    /**
     * @var string
     */
    public $Source;

    /**
     * @var string
     */
    public $Content;

    /**
     * Assigns values to properties relevant to EditorialReview
     *
     * @param  DOMElement $dom
     * @return void
     */
    public function __construct(\DOMElement $dom)
    {
        $xpath = new \DOMXPath($dom->ownerDocument);
        $xpath->registerNamespace('az', 'http://webservices.amazon.com/AWSECommerceService/2011-08-01');
        foreach (array('Source', 'Content') as $el) {
            $this->$el = (string) $xpath->query("./az:$el/text()", $dom)->item(0)->data;
        }
    }
}

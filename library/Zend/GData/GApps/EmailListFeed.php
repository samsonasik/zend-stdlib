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

/**
 * Data model for a collection of Google Apps email list entries, usually
 * provided by the Google Apps servers.
 *
 * For information on requesting this feed from a server, see the Google
 * Apps service class, Zend_Gdata_GApps.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage GApps
 */
class EmailListFeed extends \Zend\GData\Feed
{

    protected $_entryClassName = '\Zend\GData\GApps\EmailListEntry';
    protected $_feedClassName = '\Zend\GData\GApps\EmailListFeed';

}

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
class Zend_Service_DeveloperGarden_Request_ConferenceCall_GetConferenceTemplateListRequest
    extends Zend_Service_DeveloperGarden_Request_AbstractRequest
{
    /**
     * unique owner id
     *
     * @var string
     */
    public $ownerId = null;

    /**
     * constructor
     *
     * @param integer $environment
     * @param string $ownerId
     */
    public function __construct($environment, $ownerId = null)
    {
        parent::__construct($environment);
        $this->setOwnerId($ownerId);
    }

    /**
     * sets $ownerId
     *
     * @param $ownerId
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_GetConferenceTemplateListRequest
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
        return $this;
    }
}

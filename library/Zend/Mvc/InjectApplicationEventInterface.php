<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc;

use Zend\EventManager\EventInterface as Event;

/**
 * @category   Zend
 * @package    Zend_Mvc
 */
interface InjectApplicationEventInterface
{
    /**
     * Compose an Event
     *
     * @param  Event $event
     * @return void
     */
    public function setEvent(Event $event);

    /**
     * Retrieve the composed event
     *
     * @return Event
     */
    public function getEvent();
}

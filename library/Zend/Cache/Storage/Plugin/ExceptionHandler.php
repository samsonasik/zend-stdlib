<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Cache\Storage\Plugin;

use Zend\Cache\Exception;
use Zend\Cache\Storage\ExceptionEvent;
use Zend\EventManager\EventManagerInterface;

class ExceptionHandler extends AbstractPlugin
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $callback = array($this, 'onException');

        // read
        $this->callbacks[] = $events->attach('getItem.exception', $callback, $priority);
        $this->callbacks[] = $events->attach('getItems.exception', $callback, $priority);

        $this->callbacks[] = $events->attach('hasItem.exception', $callback, $priority);
        $this->callbacks[] = $events->attach('hasItems.exception', $callback, $priority);

        $this->callbacks[] = $events->attach('getMetadata.exception', $callback, $priority);
        $this->callbacks[] = $events->attach('getMetadatas.exception', $callback, $priority);

        // write
        $this->callbacks[] = $events->attach('setItem.exception', $callback, $priority);
        $this->callbacks[] = $events->attach('setItems.exception', $callback, $priority);

        $this->callbacks[] = $events->attach('addItem.exception', $callback, $priority);
        $this->callbacks[] = $events->attach('addItems.exception', $callback, $priority);

        $this->callbacks[] = $events->attach('replaceItem.exception', $callback, $priority);
        $this->callbacks[] = $events->attach('replaceItems.exception', $callback, $priority);

        $this->callbacks[] = $events->attach('touchItem.exception', $callback, $priority);
        $this->callbacks[] = $events->attach('touchItems.exception', $callback, $priority);

        $this->callbacks[] = $events->attach('removeItem.exception', $callback, $priority);
        $this->callbacks[] = $events->attach('removeItems.exception', $callback, $priority);

        $this->callbacks[] = $events->attach('checkAndSetItem.exception', $callback, $priority);

        // increment / decrement item(s)
        $this->callbacks[] = $events->attach('incrementItem.exception', $callback, $priority);
        $this->callbacks[] = $events->attach('incrementItems.exception', $callback, $priority);

        $this->callbacks[] = $events->attach('decrementItem.exception', $callback, $priority);
        $this->callbacks[] = $events->attach('decrementItems.exception', $callback, $priority);
    }

    /**
     * On exception
     *
     * @param  ExceptionEvent $event
     * @return void
     */
    public function onException(ExceptionEvent $event)
    {
        $options  = $this->getOptions();
        $callback = $options->getExceptionCallback();
        if ($callback) {
            call_user_func($callback, $event->getException());
        }

        $event->setThrowException($options->getThrowExceptions());
    }
}

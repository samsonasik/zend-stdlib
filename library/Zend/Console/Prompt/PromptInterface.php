<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Console
 */

namespace Zend\Console\Prompt;

use Zend\Console\Adapter\AdapterInterface as ConsoleAdapter;

/**
 * @category   Zend
 * @package    Zend_Console
 */
interface PromptInterface
{
    /**
     * Show the prompt to user and return the answer.
     *
     * @return mixed
     */
    public function show();

    /**
     * Return last answer to this prompt.
     *
     * @return mixed
     */
    public function getLastResponse();

    /**
     * Return console adapter to use when showing prompt.
     *
     * @return ConsoleAdapter
     */
    public function getConsole();

    /**
     * Set console adapter to use when showing prompt.
     *
     * @param ConsoleAdapter $adapter
     */
    public function setConsole(ConsoleAdapter $adapter);
}

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
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form;

use Countable;
use IteratorAggregate;

/**
 * @category   Zend
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface FieldsetInterface extends 
    Countable,
    IteratorAggregate, 
    ElementInterface
{
    /**
     * Add an element or fieldset
     *
     * $flags could contain metadata such as the alias under which to register 
     * the element or fieldset, order in which to prioritize it, etc.
     * 
     * @param  ElementInterface $elementOrFieldset 
     * @param  array $flags
     * @return FieldsetInterface
     */
    public function add(ElementInterface $elementOrFieldset, array $flags = array());

    /**
     * Does the fieldset have an element/fieldset by the given name?
     * 
     * @param  string $elementOrFieldset 
     * @return bool
     */
    public function has($elementOrFieldset);

    /**
     * Retrieve a named element or fieldset
     * 
     * @param  string $elementOrFieldset 
     * @return ElementInterface
     */
    public function get($elementOrFieldset);

    /**
     * Remove a named element or fieldset
     * 
     * @param  string $elementOrFieldset 
     * @return void
     */
    public function remove($elementOrFieldset);

    /**
     * Retrieve all attached elements
     *
     * Storage is an implementation detail of the concrete class.
     * 
     * @return array|\Traversable
     */
    public function getElements();

    /**
     * Retrieve all attached fieldsets
     * 
     * Storage is an implementation detail of the concrete class.
     * 
     * @return array|\Traversable
     */
    public function getFieldsets();

    /**
     * Set a hash of element names/messages to use when validation fails
     *
     * @param  array|\Traversable $messages
     * @return FieldsetInterface
     */
    public function setMessages($messages); // hash of element names => messages

    /**
     * Get validation error messages, if any
     *
     * Returns a hash of element names/messages for all elements failing 
     * validation, or, if $elementName is provided, messages for that element 
     * only.
     * 
     * @param  null|string $elementName 
     * @return array|\Traversable
     */
    public function getMessages($elementName = null);
}

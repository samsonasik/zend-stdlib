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
 * @package    Zend_Dojo
 * @subpackage Form_Element
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Dojo\Form\Element;

use Zend\View\Renderer\RendererInterface as View;

/**
 * Base element for dijit elements
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage Form_Element
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Dijit extends \Zend\Form\Element
{
    /**
     * Dijit parameters
     * @var array
     */
    public $dijitParams = array();

    /**
     * View helper to use
     * @var string
     */
    public $helper;

    /**
     * Constructor
     *
     * @todo Should we set dojo view helper paths here?
     * @param  array|string|\Traversable $spec
     * @param  array|string|\Traversable $options
     */
    public function __construct($spec, $options = null)
    {
        $this->addPrefixPath('Zend\Dojo\Form\Decorator', 'Zend/Dojo/Form/Decorator', 'decorator');
        parent::__construct($spec, $options);
    }

    /**
     * Set a dijit parameter
     *
     * @param  string $key
     * @param  mixed $value
     * @return \Zend\Dojo\Form\Element\Dijit
     */
    public function setDijitParam($key, $value)
    {
        $key = (string) $key;
        $this->dijitParams[$key] = $value;
        return $this;
    }

    /**
     * Set multiple dijit params at once
     *
     * @param  array $params
     * @return \Zend\Dojo\Form\Element\Dijit
     */
    public function setDijitParams(array $params)
    {
        $this->dijitParams = array_merge($this->dijitParams, $params);
        return $this;
    }

    /**
     * Does the given dijit parameter exist?
     *
     * @param  string $key
     * @return bool
     */
    public function hasDijitParam($key)
    {
        return array_key_exists($key, $this->dijitParams);
    }

    /**
     * Get a single dijit parameter
     *
     * @param  string $key
     * @return mixed
     */
    public function getDijitParam($key)
    {
        $key = (string) $key;
        if ($this->hasDijitParam($key)) {
            return $this->dijitParams[$key];
        }
        return null;
    }

    /**
     * Retrieve all dijit parameters
     *
     * @return array
     */
    public function getDijitParams()
    {
        return $this->dijitParams;
    }

    /**
     * Remove a single dijit parameter
     *
     * @param  string $key
     * @return \Zend\Dojo\Form\Element\Dijit
     */
    public function removeDijitParam($key)
    {
        $key = (string) $key;
        if (array_key_exists($key, $this->dijitParams)) {
            unset($this->dijitParams[$key]);
        }
        return $this;
    }

    /**
     * Clear all dijit parameters
     *
     * @return \Zend\Dojo\Form\Element\Dijit
     */
    public function clearDijitParams()
    {
        $this->dijitParams = array();
        return $this;
    }

    /**
     * Load default decorators
     *
     * @return void
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('DijitElement')
                 ->addDecorator('Errors')
                 ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
                 ->addDecorator('HtmlTag', array('tag' => 'dd'))
                 ->addDecorator('Label', array('tag' => 'dt'));
        }
    }

    /**
     * Set the view object
     *
     * Ensures that the view object has the dojo view helper path set.
     *
     * @param  View $view
     * @return \Zend\Dojo\Form\Element\Dijit
     */
    public function setView(View $view = null)
    {
        if (null !== $view) {
            if(false === $view->getBroker()->isLoaded('dojo')) {
                $loader = new \Zend\Dojo\View\HelperLoader();
                $view->getBroker()->getClassLoader()->registerPlugins($loader);
            }
        }
        return parent::setView($view);
    }
}

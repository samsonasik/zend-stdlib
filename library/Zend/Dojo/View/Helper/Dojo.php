<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace Zend\Dojo\View\Helper;

use Zend\Dojo\View\Exception;
use Zend\Registry;
use Zend\View\Renderer\RendererInterface as View;
use Zend\View\Helper\AbstractHelper as AbstractViewHelper;

/**
 * Zend_Dojo_View_Helper_Dojo: Dojo View Helper
 *
 * Allows specifying stylesheets, path to dojo, module paths, and onLoad
 * events.
 *
 * @package    Zend_Dojo
 * @subpackage View
 */
class Dojo extends AbstractViewHelper
{
    /**#@+
     * Programmatic dijit creation style constants
     */
    const PROGRAMMATIC_SCRIPT = 1;
    const PROGRAMMATIC_NOSCRIPT = -1;
    /**#@-*/

    /**
     * @var View
     */
    public $view;

    /**
     * @var \Zend\Dojo\View\Helper\Dojo\Container
     */
    protected $_container;

    /**
     * @var bool Whether or not dijits should be declared programmatically
     */
    protected static $_useProgrammatic = true;

    /**
     * Initialize helper
     *
     * Retrieve container from registry or create new container and store in
     * registry.
     *
     * @return void
     */
    public function __construct()
    {
        $registry = Registry::getInstance();
        $key      = __CLASS__;
        if (!isset($registry[$key])) {
            $container = new Dojo\Container();
            $registry[$key] = $container;
        }
        $this->_container = $registry[$key];
    }

    /**
     * Set view object
     *
     * @param  View $view
     * @return void
     */
    public function setView(View $view)
    {
        $this->view = $view;
        $this->_container->setView($view);
    }

    /**
     * Return dojo container
     *
     * @return \Zend\Dojo\View\Helper\Dojo\Container
     */
    public function __invoke()
    {
        return $this->_container;
    }

    /**
     * Proxy to container methods
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws \Zend\Dojo\View\Exception\BadMethodCallException For invalid method calls
     */
    public function __call($method, $args)
    {
        if (!method_exists($this->_container, $method)) {
            throw new Exception\BadMethodCallException(sprintf('Invalid method "%s" called on dojo view helper', $method));
        }

        return call_user_func_array(array($this->_container, $method), $args);
    }

    /**
     * Set whether or not dijits should be created declaratively
     *
     * @return void
     */
    public static function setUseDeclarative()
    {
        self::$_useProgrammatic = false;
    }

    /**
     * Set whether or not dijits should be created programmatically
     *
     * Optionally, specifiy whether or not dijit helpers should generate the
     * programmatic dojo.
     *
     * @param  int $style
     * @return void
     */
    public static function setUseProgrammatic($style = self::PROGRAMMATIC_SCRIPT)
    {
        if (!in_array($style, array(self::PROGRAMMATIC_SCRIPT, self::PROGRAMMATIC_NOSCRIPT))) {
            $style = self::PROGRAMMATIC_SCRIPT;
        }
        self::$_useProgrammatic = $style;
    }

    /**
     * Should dijits be created declaratively?
     *
     * @return bool
     */
    public static function useDeclarative()
    {
        return (false === self::$_useProgrammatic);
    }

    /**
     * Should dijits be created programmatically?
     *
     * @return bool
     */
    public static function useProgrammatic()
    {
        return (false !== self::$_useProgrammatic);
    }

    /**
     * Should dijits be created programmatically but without scripts?
     *
     * @return bool
     */
    public static function useProgrammaticNoScript()
    {
        return (self::PROGRAMMATIC_NOSCRIPT === self::$_useProgrammatic);
    }
}

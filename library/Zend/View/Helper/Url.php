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
 * @package    Zend_View
 * @subpackage Helper
 */

namespace Zend\View\Helper;

use Zend\Mvc\Router\RouteStackInterface;
use Zend\Mvc\Router\RouteMatch;
use Zend\View\Exception;

/**
 * Helper for making easy links and getting urls that depend on the routes and router.
 *
 * @package    Zend_View
 * @subpackage Helper
 */
class Url extends AbstractHelper
{
    /**
     * RouteStackInterface instance.
     * 
     * @var RouteStackInterface
     */
    protected $router;
    
    /**
     * RouteInterface match returned by the router.
     * 
     * @var RouteMatch.
     */
    protected $routeMatch;

    /**
     * Set the router to use for assembling.
     * 
     * @param RouteStackInterface $router
     * @return Url
     */
    public function setRouter(RouteStackInterface $router)
    {
        $this->router = $router;
        return $this;
    }
    
    /**
     * Set route match returned by the router.
     * 
     * @param  RouteMatch $routeMatch
     * @return self
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
        return $this;
    }

    /**
     * Generates an url given the name of a route.
     *
     * @see    Zend\Mvc\Router\RouteInterface::assemble()
     * @param  string  $name               Name of the route
     * @param  array   $params             Parameters for the link
     * @param  array   $options            Options for the route
     * @param  boolean $reuseMatchedParams Whether to reuse matched parameters
     * @return string Url                  For the link href attribute
     * @throws Exception\RuntimeException  If no RouteStackInterface was provided
     * @throws Exception\RuntimeException  If no RouteMatch was provided
     * @throws Exception\RuntimeException  If RouteMatch didn't contain a matched route name
     */
    public function __invoke($name = null, array $params = array(), array $options = array(), $reuseMatchedParams = false)
    {
        if (null === $this->router) {
            throw new Exception\RuntimeException('No RouteStackInterface instance provided');
        }

        if ($name === null) {
            if ($this->routeMatch === null) {
                throw new Exception\RuntimeException('No RouteMatch instance provided');
            }
            
            $name = $this->routeMatch->getMatchedRouteName();
            
            if ($name === null) {
                throw new Exception\RuntimeException('RouteMatch does not contain a matched route name');
            }
        }
        
        if ($reuseMatchedParams && $this->routeMatch !== null) {
            $params = array_merge($this->routeMatch->getParams(), $params);
        }
        
        $options['name'] = $name;

        return $this->router->assemble($params, $options);
    }
}

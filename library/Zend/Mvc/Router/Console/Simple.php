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
 * @package    Zend_Mvc_Router
 * @subpackage Http
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Mvc\Router\Console;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Mvc\Router\Exception;
use Zend\Console\Request as ConsoleRequest;
use Zend\Filter\FilterChain;
use Zend\Validator\ValidatorChain;
use Zend\Mvc\Exception\InvalidArgumentException;

/**
 * Segment route.
 *
 * @package    Zend_Mvc_Router
 * @subpackage Http
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Simple implements RouteInterface
{
    /**
     * Parts of the route.
     *
     * @var array
     */
    protected $parts;

    /**
     * Default values.
     *
     * @var array
     */
    protected $defaults;

    /**
     * Parameters' name aliases.
     *
     * @var array
     */
    protected $aliases;

    /**
     * List of assembled parameters.
     *
     * @var array
     */
    protected $assembledParams = array();

    /**
     * @var \Zend\Validator\ValidatorChain
     */
    protected $validators;

    /**
     * @var \Zend\Filter\FilterChain
     */
    protected $filters;

    /**
     * Create a new simple console route.
     *
     * @param  string                                   $route
     * @param  array                                    $constraints
     * @param  array                                    $defaults
     * @param  array                                    $aliases
     * @param  null|array|Traversable|FilterChain       $filters
     * @param  null|array|Traversable|ValidatorChain    $validators
     * @throws \Zend\Mvc\Exception\InvalidArgumentException
     * @return \Zend\Mvc\Router\Console\Simple
     */
    public function __construct(
        $route,
        array $constraints = array(),
        array $defaults = array(),
        array $aliases = array(),
        $filters = null,
        $validators = null
    ){
        $this->defaults = $defaults;
        $this->constraints = $constraints;
        $this->aliases = $aliases;

        if($filters !== null){
            if($filters instanceof FilterChain){
                $this->filters = $filters;
            }elseif($filters instanceof Traversable){
                $this->filters = new FilterChain(array(
                    'filters' => ArrayUtils::iteratorToArray($filters, false)
                ));
            }elseif(is_array($filters)){
                $this->filters = new FilterChain(array(
                    'filters' => $filters
                ));
            }else{
                throw new InvalidArgumentException('Cannot use '.gettype($filters).' as filters for '.__CLASS__);
            }
        }

        if($validators !== null){
            if($validators instanceof ValidatorChain){
                $this->validators = $validators;
            }elseif($validators instanceof Traversable || is_array($validators)){
                $this->validators = new ValidatorChain();
                foreach($validators as $v){
                    $this->validators->addValidator($v);
                }
            }else{
                throw new InvalidArgumentException('Cannot use '.gettype($validators).' as validators for '.__CLASS__);
            }
        }

        $this->parts = $this->parseRouteDefinition($route);
    }

    /**
     * factory(): defined by Route interface.
     *
     * @see    Route::factory()
     * @param  array|Traversable $options
     * @throws \Zend\Mvc\Router\Exception\InvalidArgumentException
     * @return Simple
     */
    public static function factory($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable set of options');
        }

        if (!isset($options['route'])) {
            throw new Exception\InvalidArgumentException('Missing "route" in options array');
        }

        foreach(array(
            'constraints',
            'defaults',
            'aliases',
        ) as $opt){
            if (!isset($options[$opt])) {
                $options[$opt] = array();
            }
        }

        if (!isset($options['validators'])) {
            $options['validators'] = null;
        }

        if (!isset($options['filters'])) {
            $options['filters'] = null;
        }


        return new static(
            $options['route'],
            $options['constraints'],
            $options['defaults'],
            $options['aliases'],
            $options['filters'],
            $options['validators']
        );
    }

    /**
     * Parse a route definition.
     *
     * @param  string $def
     * @return array
     * @throws Exception\InvalidArgumentException
     */
    protected function parseRouteDefinition($def)
    {
        $def    = trim($def);
        $pos    = 0;
        $length = strlen($def);
        $parts  = array();
        $unnamedGroupCounter = 1;

        while ($pos < $length) {
            /**
             * Mandatory long param
             *    --param=
             *    --param=whatever
             */
            if (preg_match( '/\G--(?<name>[a-zA-Z0-9][a-zA-Z0-9\_\-]+)(?<hasValue>=\S*?)?(?: +|$)/s', $def, $m, 0, $pos )) {
                $item = array(
                    'name'       => strtolower( $m['name'] ),
                    'short'      => false,
                    'literal'    => false,
                    'required'   => true,
                    'positional' => false,
                    'hasValue'   => !empty($m['hasValue']),
                );
            }
            /**
             * Optional long param
             *    [--param=]
             *    [--param=whatever]
             */
            elseif (preg_match(
                '/\G\[ *?--(?<name>[a-zA-Z0-9][a-zA-Z0-9\_\-]+)(?<hasValue>=\S*?)? *?\](?: +|$)/s', $def, $m, 0, $pos
            )) {
                $item = array(
                    'name'       => strtolower( $m['name'] ),
                    'short'      => false,
                    'literal'    => false,
                    'required'   => false,
                    'positional' => false,
                    'hasValue'   => !empty($m['hasValue']),
                );
            }
            /**
             * Mandatory short param
             *    -a
             *    -a=i
             *    -a=s
             *    -a=w
             */
            elseif (preg_match( '/\G-(?<name>[a-zA-Z0-9])(?:=(?<type>[ns]))?(?: +|$)/s', $def, $m, 0, $pos )) {
                $item = array(
                    'name'       => strtolower( $m['name'] ),
                    'short'      => true,
                    'literal'    => false,
                    'required'   => true,
                    'positional' => false,
                    'hasValue'  => !empty($m['type']) ? $m['type'] : null,
                );
            }
            /**
             * Optional short param
             *    [-a]
             *    [-a=n]
             *    [-a=s]
             */
            elseif (preg_match('/\G\[ *?-(?<name>[a-zA-Z0-9])(?:=(?<type>[ns]))? *?\](?: +|$)/s', $def, $m, 0, $pos)) {
                $item = array(
                    'name'       => strtolower( $m['name'] ),
                    'short'      => true,
                    'literal'    => false,
                    'required'   => false,
                    'positional' => false,
                    'hasValue'  => !empty($m['type']) ? $m['type'] : null,
                );
            }
            /**
             * Optional literal param alternative
             *    [ something | somethingElse | anotherOne ]
             *    [ something | somethingElse | anotherOne ]:namedGroup
             */
            elseif (preg_match( '/
                \G
                \[
                    (?<options>
                        (?:
                            \ *?
                            (?<name>[a-z0-9][a-zA-Z0-9_]*?)
                            \ *?
                            (?:\||(?=\]))
                            \ *?
                        )+
                    )
                \]
                (?:\:(?<groupName>[a-zA-Z0-9]+))?
                (?:\ +|$)
                /sx', $def, $m, 0, $pos
            )
            ) {
                // extract available options
                $options = preg_split('/ *\| */',trim($m['options']),0,PREG_SPLIT_NO_EMPTY);

                // remove dupes
                array_unique($options);

                // prepare item
                $item = array(
                    'name'          => isset($m['groupName']) ? $m['groupName'] : 'unnamedGroup'.$unnamedGroupCounter++,
                    'literal'       => true,
                    'required'      => false,
                    'positional'    => true,
                    'alternatives'  => $options,
                    'hasValue'      => false,
                );
            }

            /**
             * Required literal param alternative
             *    ( something | somethingElse | anotherOne )
             *    ( something | somethingElse | anotherOne ):namedGroup
             */
            elseif (preg_match( '/
                \G
                \(
                    (?<options>
                        (?:
                            \ *?
                            (?<name>[a-z0-9][a-zA-Z0-9_]+)
                            \ *?
                            (?:\||(?=\)))
                            \ *?
                        )+
                    )
                \)
                (?:\:(?<groupName>[a-zA-Z0-9]+))?
                (?:\ +|$)
                /sx', $def, $m, 0, $pos
            )) {
                // extract available options
                $options = preg_split('/ *\| */',trim($m['options']),0,PREG_SPLIT_NO_EMPTY);

                // remove dupes
                array_unique($options);

                // prepare item
                $item = array(
                    'name'          => isset($m['groupName']) ? $m['groupName']:'unnamedGroupAt'.$unnamedGroupCounter++,
                    'literal'       => true,
                    'required'      => true,
                    'positional'    => true,
                    'alternatives'  => $options,
                    'hasValue'      => false,
                );
            }
            /**
             * Required long flag alternative
             *    ( --something | --somethingElse | --anotherOne )
             *    ( --something | --somethingElse | --anotherOne ):namedGroup
             */
            elseif (preg_match( '/
                \G
                \(
                    (?<options>
                        (?:
                            \ *?
                            \-+(?<name>[a-zA-Z0-9][a-zA-Z0-9_\-]*?)
                            \ *?
                            (?:\||(?=\)))
                            \ *?
                        )+
                    )
                \)
                (?:\:(?<groupName>[a-zA-Z0-9]+))?
                (?:\ +|$)
                /sx', $def, $m, 0, $pos
            )) {
                // extract available options
                $options = preg_split('/ *\| */',trim($m['options']),0,PREG_SPLIT_NO_EMPTY);

                // remove dupes
                array_unique($options);

                // remove prefix
                array_walk($options,function(&$val,$key){$val = ltrim($val,'-');});

                // prepare item
                $item = array(
                    'name'          => isset($m['groupName']) ? $m['groupName']:'unnamedGroupAt'.$unnamedGroupCounter++,
                    'literal'       => false,
                    'required'      => true,
                    'positional'    => false,
                    'alternatives'  => $options,
                    'hasValue'      => false,
                );
            }
            /**
             * Optional literal param, i.e.
             *    [something]
             */
            elseif (preg_match( '/\G\[ *?(?<name>[a-z0-9][a-zA-Z0-9\_]*?) *?\](?: +|$)/s', $def, $m, 0, $pos )) {
                $item = array(
                    'name'       => $m['name'],
                    'literal'    => true,
                    'required'   => false,
                    'positional' => true,
                    'hasValue'   => false,
                );
            }
            /**
             * Optional value param, i.e.
             *    [SOMETHING]
             */
            elseif (preg_match( '/\G\[(?<name>[A-Z0-9\_]+)\](?: +|$)/s', $def, $m, 0, $pos )) {
                $item = array(
                    'name'       => strtolower( $m['name'] ),
                    'literal'    => false,
                    'required'   => false,
                    'positional' => true,
                    'hasValue'   => true,
                );
            }
            /**
             * Optional value param, syntax 2, i.e.
             *    [<SOMETHING>]
             */
            elseif (preg_match( '/\G\[ *\<(?<name>[a-zA-Z0-9\_]+)\> *\](?: +|$)/s', $def, $m, 0, $pos )) {
                $item = array(
                    'name'       => strtolower( $m['name'] ),
                    'literal'    => false,
                    'required'   => false,
                    'positional' => true,
                    'hasValue'   => true,
                );
            }
            /**
             * Mandatory value param, i.e.
             *    <something>
             */
            elseif (preg_match( '/\G\< *(?<name>[a-zA-Z0-9\_]+) *\>(?: +|$)/s', $def, $m, 0, $pos )) {
                $item = array(
                    'name'       => $m['name'],
                    'literal'    => false,
                    'required'   => true,
                    'positional' => true,
                    'hasValue'   => true,
                );
            }
            /**
             * Mandatory value param, i.e.
             *   SOMETHING
             */
            elseif (preg_match( '/\G(?<name>[A-Z0-9\_]*?)(?: +|$)/s', $def, $m, 0, $pos )) {
                $item = array(
                    'name'       => strtolower( $m['name'] ),
                    'literal'    => false,
                    'required'   => true,
                    'positional' => true,
                    'hasValue'   => true,
                );
            }
            /**
             * Mandatory literal param, i.e.
             *   something
             */
            elseif (preg_match( '/\G(?<name>[a-z0-9][a-zA-Z0-9\_]*?)(?: +|$)/s', $def, $m, 0, $pos )) {
                $item = array(
                    'name'       => $m['name'],
                    'literal'    => true,
                    'required'   => true,
                    'positional' => true,
                    'hasValue'   => false,
                );
            } else {
                throw new Exception\InvalidArgumentException(
                    'Cannot understand Console route at "' . substr( $def, $pos ) . '"'
                );
            }

            $pos += strlen( $m[0] );
            $parts[] = $item;
        }

        return $parts;
    }

    /**
     * match(): defined by Route interface.
     *
     * @see     Route::match()
     * @param   Request             $request
     * @param   null|int            $pathOffset
     * @return  RouteMatch
     */
    public function match(Request $request, $pathOffset = null)
    {
        if (!$request instanceof ConsoleRequest) {
            return null;
        }

        /** @var $request ConsoleRequest */
        /** @var $params \Zend\Stdlib\Parameters */
        $params = $request->getParams()->toArray();
        $matches = array();

        /**
         * Extract positional and named parts
         */
        $positional = $named = array();
        foreach($this->parts as &$part){
            if($part['positional'])
                $positional[] = &$part;
            else
                $named[] = &$part;
        }

        /**
         * Scan for named parts inside Console params
         */
        foreach($named as &$part){
            /**
             * Prepare match regex
             */
            if(isset($part['alternatives'])){
                // an alternative of flags
                $regex = '/^\-+(?<name>';
                $regex .= join('|',$part['alternatives']);

                if($part['hasValue']){
                    $regex .= ')(?:\=(?<value>.*?)$)?$/';
                }else{
                    $regex .= ')$/';
                }
            }else{
                // a single named flag
                if($part['short'] === true){
                    // short variant
                    if($part['hasValue']){
                        $regex = '/^\-'.$part['name'].'(?:\=(?<value>.*?)$)?$/';
                    }else{
                        $regex = '/^\-'.$part['name'].'$/';
                    }
                }elseif($part['short'] === false){
                    // long variant
                    if($part['hasValue']){
                        $regex = '/^\-{2,}'.$part['name'].'(?:\=(?<value>.*?)$)?$/';
                    }else{
                        $regex = '/^\-{2,}'.$part['name'].'$/';
                    }
                }
            }

            /**
             * Look for param
             */
            $value = $param = null;
            for($x=0;$x<count($params);$x++){
                if(preg_match($regex,$params[$x],$m)){
                    // found param
                    $param = $params[$x];

                    // prevent further scanning of this param
                    array_splice($params,$x,1);

                    if(isset($m['value'])){
                        $value = $m['value'];
                    }

                    if(isset($m['name'])){
                        $matchedName = $m['name'];
                    }

                    break;
                }
            }


            if(!$param){
                /**
                 * Drop out if that was a mandatory param
                 */
                if($part['required']){
                    return;
                }

                /**
                 * Continue to next positional param
                 */
                else{
                    continue;
                }
            }


            /**
             * Value for flags is always boolean
             */
            if($param && !$part['hasValue']){
                $value = true;
            }

            /**
             * Try to retrieve value if it is expected
             */
            if(!$value && $part['hasValue']){
                if($x < count($params)+1){
                    // retrieve value from adjacent param
                    $value = $params[$x];

                    // prevent further scanning of this param
                    array_splice($params,$x,1);
                }else{
                    // there are no more params available
                    return;
                }
            }

            /**
             * Validate the value against constraints
             */
            if($part['hasValue'] && isset($this->constraints[$part['name']])){
                if(
                    !preg_match($this->constraints[$part['name']],$value)
                ){
                    // constraint failed
                    return;
                }
            }

            /**
             * Store the value
             */
            if($part['hasValue']){
                $matches[$part['name']] = $value;
            }else{
                $matches[$part['name']] = true;
            }

            /**
             * If there are alternatives, fill them
             */
            if(isset($part['alternatives'])){
                if($part['hasValue']){
                    foreach($part['alternatives'] as $alt){
                        if($alt == $matchedName){
                            $matches[$alt] = $value;
                        } else {
                            $matches[$alt] = null;
                        }
                    }
                }else{
                    foreach($part['alternatives'] as $alt){
                        if($alt == $matchedName){
                            $matches[$alt] = true;
                        } else {
                            $matches[$alt] = false;
                        }
                    }
                }
            }
        }

        /**
         * Go through all positional params
         */
        $argPos = 0;
        foreach($positional as &$part){
            /**
             * Check if param exists
             */
            if(!isset($params[$argPos])){
                if($part['required']){
                    // cannot find required positional param
                    return;
                }else{
                    // stop matching
                    break;
                }
            }

            $value = $params[$argPos];

            /**
             * Check if literal param matches
             */
            if($part['literal']){
                if(
                    (isset($part['alternatives']) && !in_array($value,$part['alternatives'])) ||
                    (!isset($part['alternatives']) && $value != $part['name'])
                ){
                    return;
                }
            }

            /**
             * Validate the value against constraints
             */
            if($part['hasValue'] && isset($this->constraints[$part['name']])){
                if(
                    !preg_match($this->constraints[$part['name']],$value)
                ){
                    // constraint failed
                    return;
                }
            }

            /**
             * Store the value
             */
            if($part['hasValue']){
                $matches[$part['name']] = $value;

            }elseif(isset($part['alternatives'])){
                // from all alternativesm set matching parameter to TRUE and the rest to FALSE
                foreach($part['alternatives'] as $alt){
                    $matches[$alt] = $alt == $value;
                }

                // set alternatives group value
                $matches[$part['name']] = $value;

            }else{
                // set matching parameter flag to true
                $matches[$part['name']] = true;
            }

            /**
             * Advance to next argument
             */
            $argPos++;

        }

        /**
         * Check if we have consumed all positional parameters
         */
        if($argPos < count($params)){
            return; // there are extraneous params that were not consumed
        }



        return new RouteMatch(array_merge($this->defaults, $matches));
    }

    /**
     * assemble(): Defined by Route interface.
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     */
    public function assemble(array $params = array(), array $options = array())
    {
        $this->assembledParams = array();
    }

    /**
     * getAssembledParams(): defined by Route interface.
     *
     * @see    Route::getAssembledParams
     * @return array
     */
    public function getAssembledParams()
    {
        return $this->assembledParams;
    }
}

<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Markup
 */

namespace ZendTest\Markup\Renderer;

use ZendTest\Markup\Renderer\TestAsset\SimpleRenderer,
    Zend\Markup\Token,
    Zend\Markup\TokenList,
    Zend\Markup\Renderer\Markup\Replace as ReplaceMarkup,
    Zend\Filter\StringToUpper as StringToUpperFilter;

/**
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage UnitTests
 * @group      Zend_Markup
 */
class AbstractRendererTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Renderer
     *
     * @var \ZendTest\Markup\Renderer\TestAsset\SimpleRenderer
     */
    protected $_renderer;


    /**
     * Setup a new test
     *
     * @return void
     */
    public function setUp()
    {
        $this->_renderer = new SimpleRenderer();

        $this->_renderer->addMarkup('test', new ReplaceMarkup('foo', 'bar'));
        $this->_renderer->addMarkup('Zend_Markup_Root', new ReplaceMarkup('', ''));
    }

    /**
     * Test replacement of a simple tag
     *
     * @return void
     */
    public function testMarkupReplacement()
    {
        // create a token list
        $tokenList = new TokenList();

        // first create a root token
        $root = new Token('', Token::TYPE_MARKUP, 'Zend_Markup_Root');

        $tokenList->addChild($root);

        // now add the actual markups to the root token
        $root->addChild(new Token('baz ', Token::TYPE_NONE, '', array(), $root));

        $testMarkup = new Token('foohooo', Token::TYPE_MARKUP, 'test', array(), $root);

        $testMarkup->setStopper('bazzaa');

        $root->addChild($testMarkup);

        // now add the content for the test markup
        $testMarkup->addChild(new Token('booh', Token::TYPE_NONE, '', array(), $testMarkup));


        // render and test
        $this->assertEquals('baz fooboohbar', $this->_renderer->render($tokenList));
    }

    /**
     * Test filtering
     *
     * @return void
     */
    public function testFiltering()
    {
        // create a token list
        $tokenList = new TokenList();

        // first create a root token
        $root = new Token('', Token::TYPE_MARKUP, 'Zend_Markup_Root');

        $tokenList->addChild($root);

        // now add the actual markups to the root token
        $root->addChild(new Token('baz ', Token::TYPE_NONE, '', array(), $root));

        $testMarkup = new Token('foohooo', Token::TYPE_MARKUP, 'test', array(), $root);

        $testMarkup->setStopper('bazzaa');

        $root->addChild($testMarkup);

        // now add the content for the test markup
        $testMarkup->addChild(new Token('booh', Token::TYPE_NONE, '', array(), $testMarkup));

        // add a filter to the test markup
        $this->_renderer->getMarkup('test')->addFilter(new StringToUpperFilter(), -1);

        $this->assertEquals('baz fooBOOHbar', $this->_renderer->render($tokenList));
    }
}

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
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\View\Helper;

use Zend\Captcha\AdapterInterface as CaptchaAdapter;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Loader\Pluggable;
use Zend\View\Helper\AbstractHelper as BaseAbstractHelper;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormCaptcha extends AbstractHelper
{
    /**
     * Render a form captcha for an element
     * 
     * @param  ElementInterface $element 
     * @return string
     * @throws Exception\DomainException if the element does not compose a captcha, or the renderer is not Pluggable
     */
    public function render(ElementInterface $element)
    {
        $attributes = $element->getAttributes();
        if (!isset($attributes['captcha']) 
            || !$attributes['captcha'] instanceof CaptchaAdapter
        ) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has a "captcha" attribute implementing Zend\Captcha\AdapterInterface; none found',
                __METHOD__
            ));
        }

        $captcha = $attributes['captcha'];
        $helper  = $captcha->getHelperName();

        $renderer = $this->getView();
        if (!$renderer instanceof Pluggable) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the renderer implements Zend\Loader\Pluggable; it does not',
                __METHOD__
            ));
        }

        $helper = $renderer->plugin($helper);
        return $helper($element);
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     * 
     * @param  ElementInterface $element 
     * @return string
     */
    public function __invoke(ElementInterface $element)
    {
        return $this->render($element);
    }
}

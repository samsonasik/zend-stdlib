<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace Zend\Form\Annotation;

/**
 * ErrorMessage annotation
 *
 * Allows providing an error message to seed the Input specification for a
 * given element. The content should be a string.
 *
 * @Annotation
 * @package    Zend_Form
 * @subpackage Annotation
 */
class ErrorMessage extends AbstractStringAnnotation
{
    /**
     * Retrieve the message
     *
     * @return null|string
     */
    public function getMessage()
    {
        return $this->value;
    }
}

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
 * @subpackage View
 */

namespace Zend\Dojo\View\Helper;

/**
 * Dojo FilteringSelect dijit
 *
 * @package    Zend_Dojo
 * @subpackage View
  */
class FilteringSelect extends ComboBox
{
    /**
     * Dijit being used
     * @var string
     */
    protected $_dijit  = 'dijit.form.FilteringSelect';

    /**
     * Dojo module to use
     * @var string
     */
    protected $_module = 'dijit.form.FilteringSelect';

    /**
     * dijit.form.FilteringSelect
     *
     * @param  int $id
     * @param  mixed $value
     * @param  array $params  Parameters to use for dijit creation
     * @param  array $attribs HTML attributes
     * @param  array|null $options Select options
     * @return string
     */
    public function __invoke($id = null, $value = null, array $params = array(), array $attribs = array(), array $options = null)
    {
        return parent::__invoke($id, $value, $params, $attribs, $options);
    }
}

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
 * @package    Zend_Search_Lucene
 * @subpackage Index
 */

namespace Zend\Search\Lucene\Index;

/**
 * A Zend_Search_Lucene_Index_DocsFilter is used to filter documents while searching.
 *
 * It may or _may_not_ be used for actual filtering, so it's just a hint that upper query limits
 * search result by specified list.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Index
 */
class DocsFilter
{
    /**
     * Set of segment filters:
     *  array( <segmentName> => array(<docId> => <undefined_value>,
     *                                <docId> => <undefined_value>,
     *                                <docId> => <undefined_value>,
     *                                ...                          ),
     *         <segmentName> => array(<docId> => <undefined_value>,
     *                                <docId> => <undefined_value>,
     *                                <docId> => <undefined_value>,
     *                                ...                          ),
     *         <segmentName> => array(<docId> => <undefined_value>,
     *                                <docId> => <undefined_value>,
     *                                <docId> => <undefined_value>,
     *                                ...                          ),
     *         ...
     *       )
     *
     * @var array
     */
    public $segmentFilters = array();
}


<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */

namespace Zend\Test\PHPUnit\Db\DataSet;

/**
 * Use a Zend_Db_Table for assertions with other PHPUnit Database Extension table types.
 *
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 */
class DbTable extends \PHPUnit_Extensions_Database_DataSet_QueryTable
{
    /**
     * Zend_Db_Table object
     *
     * @var \Zend\Db\Table\AbstractTable
     */
    protected $_table = null;

    /**
     * @var array
     */
    protected $_columns = array();

    /**
     * @var string
     */
    protected $_where = null;

    /**
     * @var string
     */
    protected $_orderBy = null;

    /**
     * @var string
     */
    protected $_count = null;

    /**
     * @var int
     */
    protected $_offset = null;

    /**
     * Construct Dataset Table from Zend_Db_Table object
     *
     * @param \Zend\Db\Table\AbstractTable        $table
     * @param string|\Zend\Db\Select|null    $where
     * @param string|null                   $order
     * @param int                           $count
     * @param int                           $offset
     */
    public function __construct(\Zend\Db\Table\AbstractTable $table, $where=null, $order=null, $count=null, $offset=null)
    {
        $this->tableName = $table->info('name');
        $this->_columns = $table->info('cols');

        $this->_table = $table;
        $this->_where = $where;
        $this->_order = $order;
        $this->_count = $count;
        $this->_offset = $offset;
    }

    /**
     * Lazy load data via table fetchAll() method.
     *
     * @return void
     */
    protected function loadData()
    {
        if ($this->data === null) {
            $this->data = $this->_table->fetchAll(
                $this->_where, $this->_order, $this->_count, $this->_offset
            );
            if($this->data instanceof \Zend\Db\Table\AbstractRowset) {
                $this->data = $this->data->toArray();
            }
        }
    }

    /**
     * Create Table Metadata object
     */
    protected function createTableMetaData()
    {
        if ($this->tableMetaData === NULL) {
            $this->loadData();
            $this->tableMetaData = new \PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData($this->tableName, $this->_columns);
        }
    }
}

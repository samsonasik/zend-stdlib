<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Adapter\Driver\IbmDb2;

use Zend\Db\Adapter\Driver\ResultInterface;

class Result implements ResultInterface
{
    protected $resource;

    /**
     * @var int
     */
    protected $position = 0;

    protected $currentComplete = false;
    protected $currentData = null;

    protected $generatedValue = null;

    public function initialize($resource, $generatedValue = null)
    {
        $this->resource = $resource;
        $this->generatedValue = $generatedValue;
        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        if ($this->currentComplete) {
            return $this->currentData;
        }

        $this->currentData = db2_fetch_assoc($this->resource);
        return $this->currentData;
    }

    public function next()
    {
        $this->currentData = db2_fetch_assoc($this->resource);
        $this->currentComplete = true;
        $this->position++;
        return $this->currentData;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return ($this->currentData !== false);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        if ($this->position > 0) {
            throw new Exception\RuntimeException(
                'This result is a forward only result set, calling rewind() after moving forward is not supported'
            );
        }
        $this->currentData = db2_fetch_assoc($this->resource);
        $this->currentComplete = true;
        $this->position = 1;
    }

    /**
     * Force buffering
     *
     * @return void
     */
    public function buffer()
    {
        return null;
    }

    /**
     * Check if is buffered
     *
     * @return bool|null
     */
    public function isBuffered()
    {
        return false;
    }

    /**
     * Is query result?
     *
     * @return bool
     */
    public function isQueryResult()
    {
        return (db2_num_fields($this->resource) > 0);
    }

    /**
     * Get affected rows
     *
     * @return integer
     */
    public function getAffectedRows()
    {
        return db2_num_rows($this->resource);
    }

    /**
     * Get generated value
     *
     * @return mixed|null
     */
    public function getGeneratedValue()
    {
        return $this->generatedValue;
    }

    /**
     * Get the resource
     *
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Get field count
     *
     * @return integer
     */
    public function getFieldCount()
    {
        return db2_num_fields($this->resource);
    }

    public function count()
    {
        return null;
    }
}

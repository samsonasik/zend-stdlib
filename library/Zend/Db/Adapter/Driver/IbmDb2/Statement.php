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

use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\Adapter\ParameterContainer;
use Zend\Db\Adapter\Exception;

class Statement implements StatementInterface
{
    protected $db2 = null;

    /**
     * @var IbmDb2
     */
    protected $driver = null;

    protected $sql = '';

    protected $parameterContainer = null;

    protected $isPrepared = false;

    /**
     * @var resource
     */
    protected $resource = null;



    public function initialize($resource)
    {
        $this->db2 = $resource;
    }

    public function setDriver(IbmDb2 $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * Set sql
     *
     * @param $sql
     * @return mixed
     */
    public function setSql($sql)
    {
        $this->sql = $sql;
        return $this;
    }

    /**
     * Get sql
     *
     * @return mixed
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * Set parameter container
     *
     * @param ParameterContainer $parameterContainer
     * @return mixed
     */
    public function setParameterContainer(ParameterContainer $parameterContainer)
    {
        $this->parameterContainer = $parameterContainer;
        return $this;
    }

    /**
     * Get parameter container
     *
     * @return mixed
     */
    public function getParameterContainer()
    {
        return $this->parameterContainer;
    }

    public function setResource($resource)
    {
        if (get_resource_type($resource) !== 'DB2 Statement') {
            throw new Exception\InvalidArgumentException('Resource must be of type DB2 Statement');
        }
        $this->resource = $resource;
    }

    /**
     * Get resource
     *
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Prepare sql
     *
     * @param string $sql
     */
    public function prepare($sql = null)
    {
        if ($this->isPrepared) {
            throw new Exception\RuntimeException('This statement has been prepared already');
        }

        if ($sql == null) {
            $sql = $this->sql;
        }

        $this->resource = db2_prepare($this->db2, $sql);

        if ($this->resource === false) {
            throw new Exception\RuntimeException(db2_stmt_errormsg(), db2_stmt_error());
        }

        $this->isPrepared = true;
    }

    /**
     * Check if is prepared
     *
     * @return bool
     */
    public function isPrepared()
    {
        return $this->isPrepared;
    }

    /**
     * Execute
     *
     * @param null $parameters
     * @return Result
     */
    public function execute($parameters = null)
    {
        if (!$this->isPrepared) {
            $this->prepare();
        }

        /** START Standard ParameterContainer Merging Block */
        if (!$this->parameterContainer instanceof ParameterContainer) {
            if ($parameters instanceof ParameterContainer) {
                $this->parameterContainer = $parameters;
                $parameters = null;
            } else {
                $this->parameterContainer = new ParameterContainer();
            }
        }

        if (is_array($parameters)) {
            $this->parameterContainer->setFromArray($parameters);
        }
        /** END Standard ParameterContainer Merging Block */

        set_error_handler(function () {}, E_WARNING); // suppress warnings
        $response = db2_execute($this->resource, $this->parameterContainer->getPositionalArray());
        restore_error_handler();

        if ($response === false) {
            throw new Exception\RuntimeException(db2_stmt_errormsg($this->resource));
        }

        $result = $this->driver->createResult($this->resource);
        return $result;
    }
}

<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Paginator\Adapter\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Paginator\Adapter\DbSelect;
 
class DbSelectFactory implements
    FactoryInterface,
    MutableCreationOptionsInterface
{
    /**
     * Adapter options
     * @var array
     */
    protected $creationOptions;

    /**
     * Set creationOptions
     * @param array $creationOptions
     */
    public function setCreationOptions(array $creationOptions)
    {
        $this->creationOptions = $creationOptions;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Zend\Paginator\Adapter\DbSelect
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new DbSelect(
            $this->creationOptions[0],
            $this->creationOptions[1],
            isset($this->creationOptions[2]) ? : null
        );
    }
}

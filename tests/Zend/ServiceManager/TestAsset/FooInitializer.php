<?php

namespace ZendTest\ServiceManager\TestAsset;

use Zend\ServiceManager\InitializerInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

class FooInitializer implements InitializerInterface
{
    public $sm;

    protected $var;

    public function __construct($var = null)
    {
        if ($var) {
            $this->var = $var;
        }
    }

    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        $this->sm = $serviceLocator;
        if ($this->var) {
            list($key, $value) = each($this->var);
            $instance->{$key} = $value;
        }
    }
}

<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form;

use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var string Top-level configuration key indicating forms configuration
     */
    protected $configKey     = 'forms';

    /**
     * @var Factory Form factory used to create forms
     */
    protected $factory;

    /**
     * Can we create the requested service?
     *
     * @param  ServiceLocatorInterface $services
     * @param  string $name Service name (as resolved by ServiceManager)
     * @param  string $rName Name by which service was requested
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $services, $name, $rName)
    {
        $config = $this->getConfig($services);
        if (empty($config)) {
            return false;
        }

        return (isset($config[$rName]) && is_array($config[$rName]) && !empty($config[$rName]));
    }

    /**
     * Create a form
     *
     * @param  ServiceLocatorInterface $services
     * @param  string $name Service name (as resolved by ServiceManager)
     * @param  string $rName Name by which service was requested
     * @return Form
     */
    public function createServiceWithName(ServiceLocatorInterface $services, $name, $rName)
    {
        $config  = $this->getConfig($services);
        $config  = $config[$rName];
        $factory = $this->getFormFactory($services);

        $this->marshalInputFilter($config, $services, $factory);
        return $factory->createForm($config);
    }

    /**
     * Get forms configuration, if any
     * 
     * @param  ServiceLocatorInterface $services 
     * @return array
     */
    protected function getConfig(ServiceLocatorInterface $services)
    {
        if ($this->config !== null) {
            return $this->config;
        }

        if (!$services->has('Config')) {
            $this->config = array();
            return $this->config;
        }

        $config = $services->get('Config');
        if (!isset($config[$this->configKey])
            || !is_array($config[$this->configKey])
        ) {
            $this->config = array();
            return $this->config;
        }

        $this->config = $config[$this->configKey];
        return $this->config;
    }

    /**
     * Retrieve the form factory, creating it if necessary
     *
     * @param  ServiceLocatorInterface $services
     * @return Factory
     */
    protected function getFormFactory(ServiceLocatorInterface $services)
    {
        if ($this->factory instanceof Factory) {
            return $this->factory;
        }

        $elements = null;
        if ($services->has('FormElementManager')) {
            $elements = $services->get('FormElementManager');
        }

        $this->factory = new Factory($elements);
        return $this->factory;
    }

    /**
     * Marshal the input filter into the configuration
     *
     * If an input filter is specified:
     * - if the InputFilterManager is present, checks if it's there; if so,
     *   retrieves it and resets the specification to the instance.
     * - otherwise, pulls the input filter factory from the form factory, and
     *   attaches the FilterManager and ValidatorManager to it.
     *
     * @param array $config
     * @param ServiceLocatorInterface $services
     * @param Factory $formFactory
     */
    protected function marshalInputFilter(array &$config, ServiceLocatorInterface $services, Factory $formFactory)
    {
        if (!isset($config['input_filter'])) {
            return;
        }

        if ($config['input_filter'] instanceof InputFilterInterface) {
            return;
        }

        if (is_string($config['input_filter'])
            && $services->has('InputFilterManager')
        ) {
            $inputFilters = $services->get('InputFilterManager');
            if ($inputFilters->has($config['input_filter'])) {
                $config['input_filter'] = $inputFilters->get($config['input_filter']);
                return;
            }
        }

        $inputFilterFactory = $formFactory->getInputFilterFactory();
        $inputFilterFactory->getDefaultFilterChain()->setPluginManager($services->get('FilterManager'));
        $inputFilterFactory->getDefaultValidatorChain()->setPluginManager($services->get('ValidatorManager'));
    }
}

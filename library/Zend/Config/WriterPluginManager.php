<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Config;

use Zend\ServiceManager\AbstractPluginManager;

class WriterPluginManager extends AbstractPluginManager
{
    protected $invokableClasses = array(
        'php'  => 'Zend\Config\Writer\PhpArray',
        'ini'  => 'Zend\Config\Writer\Ini',
        'json' => 'Zend\Config\Writer\Json',
        'yaml' => 'Zend\Config\Writer\Yaml',
        'xml'  => 'Zend\Config\Writer\Xml',
    );

    public function validatePlugin($plugin)
    {
        if ($plugin instanceOf Writer\AbstractWriter) {
            return;
        }

        $type = is_object($plugin) ? get_class($plugin) : gettype($plugin);

        throw new Exception\InvalidArgumentException(
            "Plugin of type {$type} is invalid. Plugin must extend ".
                __NAMESPACE__.'\Writer\AbstractWriter'
        );
    }
}

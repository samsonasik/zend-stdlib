<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace Zend\Code\Generator;

use Zend\Code\Generator\Exception\RuntimeException;

class FileGeneratorRegistry
{
    /**
     * @var array[string]\Zend\Code\Generator\FileGenerator $fileCodeGenerators registry for Zend\Code\Generator\FileGenerator
     */
    private static $fileCodeGenerators = array();

    /**
     * Registry for the Zend_Code package. Zend_Tool uses this
     *
     * @param FileGenerator $fileCodeGenerator
     * @param string        $fileName
     * @throws RuntimeException
     */
    public static function registerFileCodeGenerator(FileGenerator $fileCodeGenerator, $fileName = null)
    {
        if ($fileName == null) {
            $fileName = $fileCodeGenerator->getFilename();
        }

        if ($fileName == '') {
            throw new RuntimeException('FileName does not exist.');
        }

        // cannot use realpath since the file might not exist, but we do need to have the index
        // in the same DIRECTORY_SEPARATOR that realpath would use:
        $fileName = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $fileName);

        static::$fileCodeGenerators[$fileName] = $fileCodeGenerator;

    }
}

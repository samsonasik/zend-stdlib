<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace Zend\Validator\File;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

/**
 * Validator which checks if the file already exists in the directory
 *
 * @category  Zend
 * @package   Zend_Validator
 */
class Exists extends AbstractValidator
{
    /**
     * @const string Error constants
     */
    const DOES_NOT_EXIST = 'fileExistsDoesNotExist';

    /**
     * @var array Error message templates
     */
    protected $messageTemplates = array(
        self::DOES_NOT_EXIST => "File does not exist",
    );

    /**
     * Options for this validator
     *
     * @var array
     */
    protected $options = array(
        'directory' => null,  // internal list of directories
    );

    /**
     * @var array Error message template variables
     */
    protected $messageVariables = array(
        'directory' => array('options' => 'directory'),
    );

    /**
     * Sets validator options
     *
     * @param  string|array|\Traversable $options
     */
    public function __construct($options = null)
    {
        if (is_string($options)) {
            $options = explode(',', $options);
        }

        if (is_array($options) && !array_key_exists('directory', $options)) {
            $options = array('directory' => $options);
        }

        parent::__construct($options);
    }

    /**
     * Returns the set file directories which are checked
     *
     * @param  boolean $asArray Returns the values as array, when false an concatenated string is returned
     * @return string|null
     */
    public function getDirectory($asArray = false)
    {
        $asArray   = (bool) $asArray;
        $directory = $this->options['directory'];
        if ($asArray && isset($directory)) {
            $directory = explode(',', (string)$directory);
        }

        return $directory;
    }

    /**
     * Sets the file directory which will be checked
     *
     * @param  string|array $directory The directories to validate
     * @return Extension Provides a fluent interface
     */
    public function setDirectory($directory)
    {
        $this->options['directory'] = null;
        $this->addDirectory($directory);
        return $this;
    }

    /**
     * Adds the file directory which will be checked
     *
     * @param  string|array $directory The directory to add for validation
     * @return Extension Provides a fluent interface
     * @throws Exception\InvalidArgumentException
     */
    public function addDirectory($directory)
    {
        $directories = $this->getDirectory(true);
        if (!isset($directories)) {
            $directories = array();
        }

        if (is_string($directory)) {
            $directory = explode(',', $directory);
        } elseif (!is_array($directory)) {
            throw new Exception\InvalidArgumentException('Invalid options to validator provided');
        }

        foreach ($directory as $content) {
            if (empty($content) || !is_string($content)) {
                continue;
            }

            $directories[] = trim($content);
        }
        $directories = array_unique($directories);

        // Sanity check to ensure no empty values
        foreach ($directories as $key => $dir) {
            if (empty($dir)) {
                unset($directories[$key]);
            }
        }

        $this->options['directory'] = (!empty($directory))
            ? implode(',', $directories) : null;

        return $this;
    }

    /**
     * Returns true if and only if the file already exists in the set directories
     *
     * @param  string|array $value Real file to check for existence
     * @return boolean
     */
    public function isValid($value)
    {
        if (is_array($value)) {
            if (!isset($value['tmp_name']) || !isset($value['name'])) {
                throw new Exception\InvalidArgumentException(
                    'Value array must be in $_FILES format'
                );
            }
            $file     = $value['tmp_name'];
            $filename = basename($file);
            $this->setValue($value['name']);
        } else {
            $file     = $value;
            $filename = basename($file);
            $this->setValue($filename);
        }

        $check = false;
        $directories = $this->getDirectory(true);
        if (!isset($directories)) {
            $check = true;
            if (!file_exists($file)) {
                $this->error(self::DOES_NOT_EXIST);
                return false;
            }
        } else {
            foreach ($directories as $directory) {
                if (!isset($directory) || '' === $directory) {
                    continue;
                }

                $check = true;
                if (!file_exists($directory . DIRECTORY_SEPARATOR . $filename)) {
                    $this->error(self::DOES_NOT_EXIST);
                    return false;
                }
            }
        }

        if (!$check) {
            $this->error(self::DOES_NOT_EXIST);
            return false;
        }

        return true;
    }
}

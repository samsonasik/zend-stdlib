<?php

namespace Zend\Config\Reader;

use Zend\Config\Reader\ReaderInterface;
use Zend\Config\Exception;

/**
 * Java-style properties config reader.
 */
class JavaProperties implements ReaderInterface
{
    /**
     * Directory of the Java-style properties file
     *
     * @var string
     */
    protected $directory;

    /**
     * fromFile(): defined by Reader interface.
     *
     * @see    ReaderInterface::fromFile()
     * @param  string $filename
     * @return array
     * @throws Exception\RuntimeException
     */
    public function fromFile($filename)
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new Exception\RuntimeException(sprintf(
                "File '%s' doesn't exist or not readable",
                $filename
            ));
        }

        $this->directory = dirname($filename);

        $config = $this->parse(file_get_contents($filename));
		
        return $this->process($config);
    }

    /**
     * fromString(): defined by Reader interface.
     *
     * @see    ReaderInterface::fromString()
     * @param  string $string
     * @return array|bool
     */
    public function fromString($string)
    {
        if (empty($string)) {
            return array();
        }

        $this->directory = null;

        $config = $this->parse($string);
		
        return $this->process($config);
    }

    /**
     * Process the array for @include
     *
     * @param  array $data
     * @return array
     * @throws Exception\RuntimeException
     */
    protected function process(array $data)
    {
        foreach ($data as $key => $value) {
            if (trim($key) === '@include') {
                if ($this->directory === null) {
                    throw new Exception\RuntimeException('Cannot process @include statement for a string');
                }
                $reader = clone $this;
                unset($data[$key]);
                $data = array_replace_recursive($data, $reader->fromFile($this->directory . '/' . $value));
            }
        }
        return $data;
    }

    /**
     * Parse Java-style properties string
     *
     * @todo Support use of the equals sign "key=value" as key-value delimiter
     * @todo Support use of the exclamation point "!" as comment mark
     * @todo Ignore whitespace that precedes text past the first line of multiline values
     *
     * @param  string $string
     * @return array
     */
    protected function parse($string) {
        $result = array();
        $lines = explode("\n", $string);
        $key = "";
        $isWaitingOtherLine = false;
        foreach ($lines as $i => $line) {
            if(empty($line) || (!$isWaitingOtherLine && strpos($line, "#") === 0)) {
                continue;
            }

            if(!$isWaitingOtherLine) {
                $key = substr($line, 0, strpos($line, ':'));
                $value = substr($line, strpos($line, ':') + 1, strlen($line));
            }
            else {
                $value .= $line;
            }

            /* Check if ends with single '\' */
            if(strrpos($value, "\\") === strlen($value)-strlen("\\")) {
                $value = substr($value,0,strlen($value)-1);
                $isWaitingOtherLine = true;
            }
            else {
                $isWaitingOtherLine = false;
            }

            $result[$key] = stripslashes($value);
            unset($lines[$i]);
        }

        return $result;
    }
}
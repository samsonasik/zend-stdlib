<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_XmlRpc
 */

namespace Zend\XmlRpc\Value;

use Zend\XmlRpc\Exception;

/**
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage Value
 */
class DateTime extends AbstractScalar
{
    /**
     * PHP compatible format string for XML/RPC datetime values
     *
     * @var string
     */
    protected $_phpFormatString = 'Ymd\\TH:i:s';

    /**
     * ISO compatible format string for XML/RPC datetime values
     *
     * @var string
     */
    protected $_isoFormatString = 'yyyyMMddTHH:mm:ss';

    /**
     * Set the value of a dateTime.iso8601 native type
     *
     * The value is in iso8601 format, minus any timezone information or dashes
     *
     * @param mixed $value Integer of the unix timestamp or any string that can be parsed
     *                     to a unix timestamp using the PHP strtotime() function
     */
    public function __construct($value)
    {
        $this->_type = self::XMLRPC_TYPE_DATETIME;

        if ($value instanceof \DateTime) {
            $this->_value = $value->format($this->_phpFormatString);
        } elseif (is_numeric($value)) { // The value is numeric, we make sure it is an integer
            $this->_value = date($this->_phpFormatString, (int)$value);
        } else {
            try {
                $dateTime = new \DateTime($value);
            } catch (\Exception $e) {
                throw new Exception\ValueException($e->getMessage(), $e->getCode(), $e);
            }

            $this->_value = $dateTime->format($this->_phpFormatString); // Convert the DateTime to iso8601 format
        }
    }

    /**
     * Return the value of this object as iso8601 dateTime value
     *
     * @return int As a Unix timestamp
     */
    public function getValue()
    {
        return $this->_value;
    }
}

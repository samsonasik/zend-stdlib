<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\WindowsAzure\Storage;

use Zend\Service\WindowsAzure\Exception\RuntimeException;
use Zend\Service\WindowsAzure\Exception\UnknownPropertyException;

/**
 * @category                                   Zend
 * @package                                    Zend_Service_WindowsAzure
 * @subpackage                                 Storage
 * @copyright                                  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license                                    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @property string  $Name                     Name of the queue
 * @property array   $Metadata                 Key/value pairs of meta data
 * @property integer $ApproximateMessageCount  The approximate number of messages in the queue
 */
class QueueInstance
{
    /**
     * Data
     *
     * @var array
     */
    protected $_data = null;

    /**
     * Constructor
     *
     * @param string $name          Name
     * @param array  $metadata      Key/value pairs of meta data
     */
    public function __construct($name, $metadata = array())
    {
        $this->_data = array(
            'name'                    => $name,
            'metadata'                => $metadata,
            'approximatemessagecount' => 0
        );
    }

    /**
     * Magic overload for setting properties
     *
     * @param string $name     Name of the property
     * @param string $value    Value to set
     * @throws UnknownPropertyException
     * @return
     */
    public function __set($name, $value)
    {
        if (array_key_exists(strtolower($name), $this->_data)) {
            $this->_data[strtolower($name)] = $value;
            return;
        }

        throw new UnknownPropertyException('Unknown property: ' . $name);
    }

    /**
     * Magic overload for getting properties
     *
     * @param string $name Name of the property
     * @throws UnknownPropertyException
     * @return
     */
    public function __get($name)
    {
        if (array_key_exists(strtolower($name), $this->_data)) {
            return $this->_data[strtolower($name)];
        }

        throw new UnknownPropertyException('Unknown property: ' . $name);
    }
}

<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_TimeSync
 */

namespace Zend\TimeSync;

/**
 * SNTP Protocol handling class
 *
 * @category  Zend
 * @package   Zend_TimeSync
 */
class Sntp extends AbstractProtocol
{
    /**
     * Port number for this timeserver
     *
     * @var integer
     */
    protected $_port = 37;

    /**
     * Socket delay
     *
     * @var integer
     */
    private $_delay;

    /**
     * Class constructor, sets the timeserver and port number
     *
     * @param string  $timeserver Timeserver to connect to
     * @param integer $port       Port of the timeserver when it differs from the default port
     */
    public function __construct($timeserver, $port)
    {
        $this->_timeserver = 'udp://' . $timeserver;
        if ($port !== null) {
            $this->_port = $port;
        }
    }

    /**
     * Prepares the data that will be send to the timeserver
     *
     * @return array
     */
    protected function _prepare()
    {
        return "\n";
    }

    /**
     * Reads the data returned from the timeserver
     *
     * @return string
     */
    protected function _read()
    {
        $result       = fread($this->_socket, 49);
        $this->_delay = (($this->_delay - time()) / 2);

        return $result;
    }

    /**
     * Writes data to to the timeserver
     *
     * @param  string $data Data to write to the timeserver
     * @return void
     */
    protected function _write($data)
    {
        $this->_connect();
        $this->_delay = time();
        fwrite($this->_socket, $data);
    }

    /**
     * Extracts the data returned from the timeserver
     *
     * @param  string $result Data to extract
     * @return integer
     */
    protected function _extract($result)
    {
        $dec   = hexdec('7fffffff');
        $time  = abs(($dec - hexdec(bin2hex($result))) - $dec);
        $time -= 2208988800;
        // Socket delay
        $time -= $this->_delay;

        $this->_info['offset'] = $this->_delay;

        return $time;
    }
}

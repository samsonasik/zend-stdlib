<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Akismet;

use Zend\Service\Akismet\Akismet;
use Zend\Http\Client\Adapter\Test as ClientTestAdapter;
use Zend\Http\Client as HttpClient;

/**
 * @category   Zend
 * @package    Zend_Service_Akismet
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Akismet
 */
class AkismetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Akismet
     */
    protected $akismet;

    /**
     * @var ClientTestAdapter
     */
    protected $adapter;

    /**
     * @var array
     */
    protected $comment;

    public function setUp()
    {
        $this->akismet = new Akismet('somebogusapikey', 'http://framework.zend.com/wiki/');
        $adapter = new ClientTestAdapter();
        $client = new HttpClient(null, array(
            'adapter' => $adapter
        ));
        $this->adapter = $adapter;
        $this->akismet->setHttpClient($client);

        $this->comment = array(
            'user_ip'         => '71.161.221.76',
            'user_agent'      => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1)',
            'comment_type'    => 'comment',
            'comment_content' => 'spam check'
        );
    }

    public function testBlogUrl()
    {
        $this->assertEquals('http://framework.zend.com/wiki/', $this->akismet->getBlogUrl());
        $this->akismet->setBlogUrl('http://framework.zend.com/');
        $this->assertEquals('http://framework.zend.com/', $this->akismet->getBlogUrl());
    }

    public function testApiKey()
    {
        $this->assertEquals('somebogusapikey', $this->akismet->getApiKey());
        $this->akismet->setApiKey('invalidapikey');
        $this->assertEquals('invalidapikey', $this->akismet->getApiKey());
    }

    public function testCharset()
    {
        $this->assertEquals('UTF-8', $this->akismet->getCharset());
        $this->akismet->setCharset('ISO-8859-1');
        $this->assertEquals('ISO-8859-1', $this->akismet->getCharset());
    }

    public function testPort()
    {
        $this->assertEquals(80, $this->akismet->getPort());
        $this->akismet->setPort(8080);
        $this->assertEquals(8080, $this->akismet->getPort());
    }

    public function testUserAgent()
    {
        $this->akismet->setUserAgent('MyUserAgent/1.0 | Akismet/1.11');
        $this->assertEquals('MyUserAgent/1.0 | Akismet/1.11', $this->akismet->getUserAgent());
    }

    public function testUserAgentDefaultMatchesFrameworkVersion()
    {
        $this->assertContains('Zend Framework/' . \Zend\Version::VERSION, $this->akismet->getUserAgent());
    }

    public function testVerifyKey()
    {
        $response = "HTTP/1.0 200 OK\r\n"
                  . "Content-type: text/plain; charset=utf-8\r\n"
                  . "Content-length: 5\r\n"
                  . "Server: LiteSpeed\r\n"
                  . "Date: Tue, 06 Feb 2007 14:41:24 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . "valid";
        $this->adapter->setResponse($response);
        $this->assertTrue($this->akismet->verifyKey());

        $response = "HTTP/1.0 200 OK\r\n"
                  . "Content-type: text/plain; charset=utf-8\r\n"
                  . "Content-length: 7\r\n"
                  . "Server: LiteSpeed\r\n"
                  . "Date: Tue, 06 Feb 2007 14:41:24 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . "invalid";
        $this->adapter->setResponse($response);
        $this->assertFalse($this->akismet->verifyKey());
    }

    public function testIsSpamThrowsExceptionOnInvalidKey()
    {
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/4.4.2\r\n"
                  . "Content-type: text/plain; charset=utf-8\r\n"
                  . "X-akismet-server: 72.21.44.242\r\n"
                  . "Content-length: 7\r\n"
                  . "Server: LiteSpeed\r\n"
                  . "Date: Tue, 06 Feb 2007 14:50:24 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . "invalid";
        $this->adapter->setResponse($response);

        $this->setExpectedException('Zend\Service\Akismet\Exception\InvalidArgumentException', 'Invalid API key');
        $this->akismet->isSpam($this->comment);
    }

    public function testIsSpam()
    {
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/4.4.2\r\n"
                  . "Content-type: text/plain; charset=utf-8\r\n"
                  . "X-akismet-server: 72.21.44.242\r\n"
                  . "Content-length: 4\r\n"
                  . "Server: LiteSpeed\r\n"
                  . "Date: Tue, 06 Feb 2007 14:50:24 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . "true";
        $this->adapter->setResponse($response);
        $this->assertTrue($this->akismet->isSpam($this->comment));

        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/4.4.2\r\n"
                  . "Content-type: text/plain; charset=utf-8\r\n"
                  . "X-akismet-server: 72.21.44.242\r\n"
                  . "Content-length: 5\r\n"
                  . "Server: LiteSpeed\r\n"
                  . "Date: Tue, 06 Feb 2007 14:50:24 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . "false";
        $this->adapter->setResponse($response);
        $this->assertFalse($this->akismet->isSpam($this->comment));
    }

    public function testSubmitSpamThrowsExceptionOnInvalidKey()
    {
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/4.4.2\r\n"
                  . "Content-type: text/plain; charset=utf-8\r\n"
                  . "X-akismet-server: 72.21.44.242\r\n"
                  . "Content-length: 7\r\n"
                  . "Server: LiteSpeed\r\n"
                  . "Date: Tue, 06 Feb 2007 14:50:24 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . "invalid";
        $this->adapter->setResponse($response);

        $this->setExpectedException('Zend\Service\Akismet\Exception\InvalidArgumentException', 'Invalid API key');
        $this->akismet->submitSpam($this->comment);
    }

    public function testSubmitSpam()
    {
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/4.4.2\r\n"
                  . "Content-type: text/html\r\n"
                  . "Content-length: 41\r\n"
                  . "Server: LiteSpeed\r\n"
                  . "Date: Tue, 06 Feb 2007 15:01:47 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . "Thanks for making the web a better place.";
        $this->adapter->setResponse($response);

        $this->akismet->submitSpam($this->comment);
    }

    public function testSubmitHam()
    {
        $response = "HTTP/1.0 200 OK\r\n"
                  . "X-powered-by: PHP/4.4.2\r\n"
                  . "Content-type: text/html\r\n"
                  . "Content-length: 41\r\n"
                  . "Server: LiteSpeed\r\n"
                  . "Date: Tue, 06 Feb 2007 15:01:47 GMT\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . "Thanks for making the web a better place.";
        $this->adapter->setResponse($response);

        $this->akismet->submitHam($this->comment);
    }
}

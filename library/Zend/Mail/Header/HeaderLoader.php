<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mail
 */

namespace Zend\Mail\Header;

use Zend\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for HTTP headers
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Header
 */
class HeaderLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased Header plugins
     */
    protected $plugins = array(
        'bcc'          => 'Zend\Mail\Header\Bcc',
        'cc'           => 'Zend\Mail\Header\Cc',
        'contenttype'  => 'Zend\Mail\Header\ContentType',
        'content_type' => 'Zend\Mail\Header\ContentType',
        'content-type' => 'Zend\Mail\Header\ContentType',
        'date'         => 'Zend\Mail\Header\Date',
        'from'         => 'Zend\Mail\Header\From',
        'mimeversion'  => 'Zend\Mail\Header\MimeVersion',
        'mime_version' => 'Zend\Mail\Header\MimeVersion',
        'mime-version' => 'Zend\Mail\Header\MimeVersion',
        'received'     => 'Zend\Mail\Header\Received',
        'replyto'      => 'Zend\Mail\Header\ReplyTo',
        'reply_to'     => 'Zend\Mail\Header\ReplyTo',
        'reply-to'     => 'Zend\Mail\Header\ReplyTo',
        'sender'       => 'Zend\Mail\Header\Sender',
        'subject'      => 'Zend\Mail\Header\Subject',
        'to'           => 'Zend\Mail\Header\To',
    );
}

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
 * @package    Zend_Validate
 * @subpackage Sitemap
 */

namespace Zend\Validator\Sitemap;

use Zend\Uri;
use Zend\Validator\AbstractValidator;

/**
 * Validates whether a given value is valid as a sitemap <loc> value
 *
 * @link       http://www.sitemaps.org/protocol.php Sitemaps XML format
 *
 * @see        Zend\Uri\Uri
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage Sitemap
 */
class Loc extends AbstractValidator
{
    /**
     * Validation key for not valid
     *
     */
    const NOT_VALID = 'sitemapLocNotValid';
    const INVALID   = 'sitemapLocInvalid';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_VALID => "The input is not a valid sitemap location",
        self::INVALID   => "Invalid type given. String expected",
    );

    /**
     * Validates if a string is valid as a sitemap location
     *
     * @link http://www.sitemaps.org/protocol.php#locdef <loc>
     *
     * @param  string  $value  value to validate
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);
        $uri = Uri\UriFactory::factory($value);
        if (!$uri->isValid()) {
            $this->error(self::NOT_VALID);
            return false;
        }

        return true;
    }
}

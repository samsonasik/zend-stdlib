<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace Zend\Stdlib\StringWrapper;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage StringWrapper
 */
class Iconv extends AbstractStringWrapper
{
    /**
     * List of supported character sets (upper case)
     *
     * @var string[]
     * @link http://www.gnu.org/software/libiconv/
     */
    protected $encodings = array(
        // European languages
        'ASCII',
        'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5', 'ISO-8859-7',
        'ISO-8859-9', 'ISO-8859-10', 'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16',
        'KOI8-R', 'KOI8-U', 'KOI8-RU',
        'CP1250', 'CP1251', 'CP1252', 'CP1253', 'CP1254' ,'CP1257',
        'CP850', 'CP866', 'CP1131',
        'MACROMAN', 'MACCENTRALEUROPE', 'MACICELAND', 'MACCROATIAN', 'MACROMANIA',
        'MACCYRILLIC', 'MACUKRAINE', 'MACGREEK', 'MACTURKISH',
        'MACINTOSH',

        // Semitic languages
        'ISO-8859-6', 'ISO-8859-8',
        'CP1255' , 'CP1256', 'CP862',
        'MACHEBREW', 'MACARABIC',

        // Japanese
        'EUC-JP', 'SHIFT_JIS', 'CP932', 'ISO-2022-JP', 'ISO-2022-JP-2', 'ISO-2022-JP-1',

        // Chinese
        'EUC-CN', 'HZ', 'GBK', 'CP936', 'GB18030', 'EUC-TW', 'BIG5', 'CP950',
        'BIG5-HKSCS', 'BIG5-HKSCS:2004', 'BIG5-HKSCS:2001', 'BIG5-HKSCS:1999',
        'ISO-2022-CN', 'ISO-2022-CN-EXT',

        // Korean
        'EUC-KR', 'CP949', 'ISO-2022-KR', 'JOHAB',

        // Armenian
        'ARMSCII-8',

        // Georgian
        'GEORGIAN-ACADEMY', 'GEORGIAN-PS',

        // Tajik
        'KOI8-T',

        // Kazakh
        'PT154', 'RK1048',

        // Thai
        'ISO-8859-11', 'TIS-620', 'CP874', 'MACTHAI',

        // Laotian
        'MULELAO-1', 'CP1133',

        // Vietnamese
        'VISCII', 'TCVN', 'CP1258',

        // Platform specifics
        'HP-ROMAN8', 'NEXTSTEP',

        // Full Unicode
        'UTF-8',
        'UCS-2', 'UCS-2BE', 'UCS-2LE',
        'UCS-4', 'UCS-4BE', 'UCS-4LE',
        'UTF-16', 'UTF-16BE', 'UTF-16LE',
        'UTF-32', 'UTF-32BE', 'UTF-32LE',
        'UTF-7',
        'C99', 'JAVA',

        // Full Unicode, in terms of uint16_t or uint32_t (with machine dependent endianness and alignment)
        // 'UCS-2-INTERNAL', 'UCS-4-INTERNAL',

        // Locale dependent, in terms of `char' or `wchar_t' (with machine dependent endianness and alignment,
        // and with OS and locale dependent semantics)
        // 'char', 'wchar_t',
        // '', // The empty encoding name is equivalent to "char": it denotes the locale dependent character encoding.

        // When configured with the option --enable-extra-encodings,
        // it also provides support for a few extra encodings:

        // European languages
        'CP437', 'CP737', 'CP775', 'CP852', 'CP853', 'CP855', 'CP857', 'CP858',
        'CP860', 'CP861', 'CP863', 'CP865', 'CP869', 'CP1125',

        // Semitic languages
        'CP864',

        // Japanese
        'EUC-JISX0213', 'Shift_JISX0213', 'ISO-2022-JP-3',

        // Chinese
        'BIG5-2003', // (experimental)

        // Turkmen
        'TDS565',

        // Platform specifics
        'ATARIST', 'RISCOS-LATIN1',
    );

    /**
     * Constructor
     *
     * @throws Exception\ExtensionNotLoadedException
     */
    public function __construct()
    {
        if (!extension_loaded('iconv')) {
            throw new Exception\ExtensionNotLoadedException(
                'PHP extension "iconv" is required for this wrapper'
            );
        }
    }

    /**
     * Returns the length of the given string
     *
     * @param string $str
     * @param string $encoding
     * @return int|false
     */
    public function strlen($str, $encoding = 'UTF-8')
    {
        return iconv_strlen($str, $encoding);
    }

    /**
     * Returns the portion of string specified by the start and length parameters
     *
     * @param string   $str
     * @param int      $offset
     * @param int|null $length
     * @param string   $encoding
     * @return string|false
     */
    public function substr($str, $offset = 0, $length = null, $encoding = 'UTF-8')
    {
        return iconv_substr($str, $offset, $length, $encoding);
    }

    /**
     * Find the position of the first occurrence of a substring in a string
     *
     * @param string $haystack
     * @param string $needle
     * @param int    $offset
     * @param string $encoding
     * @return int|false
     */
    public function strpos($haystack, $needle, $offset = 0, $encoding = 'UTF-8')
    {
        return iconv_strpos($haystack, $needle, $offset, $encoding);
    }

    /**
     * Convert a string from one character encoding to another
     *
     * @param string $str
     * @param string $toEncoding
     * @param string $fromEncoding
     * @return string|false
     */
    public function convert($str, $toEncoding, $fromEncoding = 'UTF-8')
    {
        return iconv($fromEncoding, $toEncoding, $str);
    }
}

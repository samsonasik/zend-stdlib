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
 * @package    Zend_Captcha
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Captcha;

use DirectoryIterator;
use Zend\Captcha\Exception;
use Zend\Loader\Pluggable;
use Zend\Stdlib\ErrorHandler;

/**
 * Image-based captcha element
 *
 * Generates image displaying random word
 *
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Image extends Word
{
    /**
     * Directory for generated images
     *
     * @var string
     */
    protected $imgDir = "public/images/captcha/";

    /**
     * URL for accessing images
     *
     * @var string
     */
    protected $imgUrl = "/images/captcha/";

    /**
     * Image's alt tag content
     *
     * @var string
     */
    protected $imgAlt = "";

    /**
     * Image suffix (including dot)
     *
     * @var string
     */
    protected $suffix = ".png";

    /**
     * Image width
     *
     * @var int
     */
    protected $width = 200;

    /**
     * Image height
     *
     * @var int
     */
    protected $height = 50;

    /**
     * Font size
     *
     * @var int
     */
    protected $fsize = 24;

    /**
     * Image font file
     *
     * @var string
     */
    protected $font;

    /**
     * Image to use as starting point
     * Default is blank image. If provided, should be PNG image.
     *
     * @var string
     */
    protected $startImage;

    /**
     * How frequently to execute garbage collection
     *
     * @var int
     */
    protected $gcFreq = 10;

    /**
     * How long to keep generated images
     *
     * @var int
     */
    protected $expiration = 600;

    /**
     * Number of noise dots on image
     * Used twice - before and after transform
     *
     * @var int
     */
    protected $dotNoiseLevel = 100;

    /**
     * Number of noise lines on image
     * Used twice - before and after transform
     *
     * @var int
     */
    protected $lineNoiseLevel = 5;

    /**
     * Constructor
     *
     * @param  array|\Traversable $options
     * @return void
     */
    public function __construct($options = null)
    {
        if (!extension_loaded("gd")) {
            throw new Exception\ExtensionNotLoadedException("Image CAPTCHA requires GD extension");
        }

        if (!function_exists("imagepng")) {
            throw new Exception\ExtensionNotLoadedException("Image CAPTCHA requires PNG support");
        }

        if (!function_exists("imageftbbox")) {
            throw new Exception\ExtensionNotLoadedException("Image CAPTCHA requires FT fonts support");
        }

        parent::__construct($options);
    }

    /**
     * @return string
     */
    public function getImgAlt()
    {
        return $this->imgAlt;
    }

    /**
     * @return string
     */
    public function getStartImage()
    {
        return $this->startImage;
    }

    /**
     * @return int
     */
    public function getDotNoiseLevel()
    {
        return $this->dotNoiseLevel;
    }

    /**
     * @return int
     */
    public function getLineNoiseLevel()
    {
        return $this->lineNoiseLevel;
    }

    /**
     * Get captcha expiration
     *
     * @return int
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * Get garbage collection frequency
     *
     * @return int
     */
    public function getGcFreq()
    {
        return $this->gcFreq;
    }

    /**
     * Get font to use when generating captcha
     *
     * @return string
     */
    public function getFont()
    {
        return $this->font;
    }

    /**
     * Get font size
     *
     * @return int
     */
    public function getFontSize()
    {
        return $this->fsize;
    }

    /**
     * Get captcha image height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Get captcha image directory
     *
     * @return string
     */
    public function getImgDir()
    {
        return $this->imgDir;
    }

    /**
     * Get captcha image base URL
     *
     * @return string
     */
    public function getImgUrl()
    {
        return $this->imgUrl;
    }

    /**
     * Get captcha image file suffix
     *
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * Get captcha image width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param string $startImage
     */
    public function setStartImage($startImage)
    {
        $this->startImage = $startImage;
        return $this;
    }

    /**
     * @param int $dotNoiseLevel
     */
    public function setDotNoiseLevel($dotNoiseLevel)
    {
        $this->dotNoiseLevel = $dotNoiseLevel;
        return $this;
    }

    /**
     * @param int $lineNoiseLevel
     */
    public function setLineNoiseLevel($lineNoiseLevel)
    {
        $this->lineNoiseLevel = $lineNoiseLevel;
        return $this;
    }

    /**
     * Set captcha expiration
     *
     * @param  int $expiration
     * @return Image
     */
    public function setExpiration($expiration)
    {
        $this->expiration = $expiration;
        return $this;
    }

    /**
     * Set garbage collection frequency
     *
     * @param  int $gcFreq
     * @return Image
     */
    public function setGcFreq($gcFreq)
    {
        $this->gcFreq = $gcFreq;
        return $this;
    }

    /**
     * Set captcha font
     *
     * @param  string $font
     * @return Image
     */
    public function setFont($font)
    {
        $this->font = $font;
        return $this;
    }

    /**
     * Set captcha font size
     *
     * @param  int $fsize
     * @return Image
     */
    public function setFontSize($fsize)
    {
        $this->fsize = $fsize;
        return $this;
    }

    /**
     * Set captcha image height
     *
     * @param  int $height
     * @return Image
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Set captcha image storage directory
     *
     * @param  string $imgDir
     * @return Image
     */
    public function setImgDir($imgDir)
    {
        $this->imgDir = rtrim($imgDir, "/\\") . '/';
        return $this;
    }

    /**
     * Set captcha image base URL
     *
     * @param  string $imgUrl
     * @return Image
     */
    public function setImgUrl($imgUrl)
    {
        $this->imgUrl = rtrim($imgUrl, "/\\") . '/';
        return $this;
    }

    /**
     * @param string $imgAlt
     */
    public function setImgAlt($imgAlt)
    {
        $this->imgAlt = $imgAlt;
        return $this;
    }

    /**
     * Set captch image filename suffix
     *
     * @param  string $suffix
     * @return Image
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
        return $this;
    }

    /**
     * Set captcha image width
     *
     * @param  int $width
     * @return Image
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Generate random frequency
     *
     * @return float
     */
    protected function randomFreq()
    {
        return mt_rand(700000, 1000000) / 15000000;
    }

    /**
     * Generate random phase
     *
     * @return float
     */
    protected function randomPhase()
    {
        // random phase from 0 to pi
        return mt_rand(0, 3141592) / 1000000;
    }

    /**
     * Generate random character size
     *
     * @return int
     */
    protected function randomSize()
    {
        return mt_rand(300, 700) / 100;
    }

    /**
     * Generate captcha
     *
     * @return string captcha ID
     */
    public function generate()
    {
        $id    = parent::generate();
        $tries = 5;

        // If there's already such file, try creating a new ID
        while ($tries-- && file_exists($this->getImgDir() . $id . $this->getSuffix())) {
            $id = $this->generateRandomId();
            $this->setId($id);
        }
        $this->generateImage($id, $this->getWord());

        if (mt_rand(1, $this->getGcFreq()) == 1) {
            $this->gc();
        }

        return $id;
    }

    /**
     * Generate image captcha
     *
     * Override this function if you want different image generator
     * Wave transform from http://www.captcha.ru/captchas/multiwave/
     *
     * @param string $id Captcha ID
     * @param string $word Captcha word
     */
    protected function generateImage($id, $word)
    {
        $font = $this->getFont();

        if (empty($font)) {
            throw new Exception\NoFontProvidedException('Image CAPTCHA requires font');
        }

        $w     = $this->getWidth();
        $h     = $this->getHeight();
        $fsize = $this->getFontSize();

        $img_file   = $this->getImgDir() . $id . $this->getSuffix();

        if (empty($this->startImage)) {
            $img = imagecreatetruecolor($w, $h);
        } else {
            // Potential error is change to exception
            ErrorHandler::start();
            $img   = imagecreatefrompng($this->startImage);
            $error = ErrorHandler::stop();
            if (!$img || $error) {
                throw new Exception\ImageNotLoadableException(
                    "Can not load start image '{$this->startImage}'", 0, $error
                );
            }
            $w = imagesx($img);
            $h = imagesy($img);
        }

        $text_color = imagecolorallocate($img, 0, 0, 0);
        $bg_color   = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 0, 0, $w-1, $h-1, $bg_color);
        $textbox = imageftbbox($fsize, 0, $font, $word);
        $x = ($w - ($textbox[2] - $textbox[0])) / 2;
        $y = ($h - ($textbox[7] - $textbox[1])) / 2;
        imagefttext($img, $fsize, 0, $x, $y, $text_color, $font, $word);

        // generate noise
        for ($i=0; $i < $this->dotNoiseLevel; $i++) {
           imagefilledellipse($img, mt_rand(0,$w), mt_rand(0,$h), 2, 2, $text_color);
        }
        for ($i=0; $i < $this->lineNoiseLevel; $i++) {
           imageline($img, mt_rand(0,$w), mt_rand(0,$h), mt_rand(0,$w), mt_rand(0,$h), $text_color);
        }

        // transformed image
        $img2     = imagecreatetruecolor($w, $h);
        $bg_color = imagecolorallocate($img2, 255, 255, 255);
        imagefilledrectangle($img2, 0, 0, $w-1, $h-1, $bg_color);

        // apply wave transforms
        $freq1 = $this->randomFreq();
        $freq2 = $this->randomFreq();
        $freq3 = $this->randomFreq();
        $freq4 = $this->randomFreq();

        $ph1 = $this->randomPhase();
        $ph2 = $this->randomPhase();
        $ph3 = $this->randomPhase();
        $ph4 = $this->randomPhase();

        $szx = $this->randomSize();
        $szy = $this->randomSize();

        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $sx = $x + (sin($x*$freq1 + $ph1) + sin($y*$freq3 + $ph3)) * $szx;
                $sy = $y + (sin($x*$freq2 + $ph2) + sin($y*$freq4 + $ph4)) * $szy;

                if ($sx < 0 || $sy < 0 || $sx >= $w - 1 || $sy >= $h - 1) {
                    continue;
                } else {
                    $color    = (imagecolorat($img, $sx, $sy) >> 16)         & 0xFF;
                    $color_x  = (imagecolorat($img, $sx + 1, $sy) >> 16)     & 0xFF;
                    $color_y  = (imagecolorat($img, $sx, $sy + 1) >> 16)     & 0xFF;
                    $color_xy = (imagecolorat($img, $sx + 1, $sy + 1) >> 16) & 0xFF;
                }

                if ($color == 255 && $color_x == 255 && $color_y == 255 && $color_xy == 255) {
                    // ignore background
                    continue;
                } elseif ($color == 0 && $color_x == 0 && $color_y == 0 && $color_xy == 0) {
                    // transfer inside of the image as-is
                    $newcolor = 0;
                } else {
                    // do antialiasing for border items
                    $frac_x  = $sx-floor($sx);
                    $frac_y  = $sy-floor($sy);
                    $frac_x1 = 1-$frac_x;
                    $frac_y1 = 1-$frac_y;

                    $newcolor = $color    * $frac_x1 * $frac_y1
                              + $color_x  * $frac_x  * $frac_y1
                              + $color_y  * $frac_x1 * $frac_y
                              + $color_xy * $frac_x  * $frac_y;
                }

                imagesetpixel($img2, $x, $y, imagecolorallocate($img2, $newcolor, $newcolor, $newcolor));
            }
        }

        // generate noise
        for ($i=0; $i<$this->dotNoiseLevel; $i++) {
            imagefilledellipse($img2, mt_rand(0,$w), mt_rand(0,$h), 2, 2, $text_color);
        }

        for ($i=0; $i<$this->lineNoiseLevel; $i++) {
           imageline($img2, mt_rand(0,$w), mt_rand(0,$h), mt_rand(0,$w), mt_rand(0,$h), $text_color);
        }

        imagepng($img2, $img_file);
        imagedestroy($img);
        imagedestroy($img2);
    }

    /**
     * Remove old files from image directory
     *
     */
    protected function gc()
    {
        $expire = time() - $this->getExpiration();
        $imgdir = $this->getImgDir();
        if (!$imgdir || strlen($imgdir) < 2) {
            // safety guard
            return;
        }

        $suffixLength = strlen($this->suffix);
        foreach (new DirectoryIterator($imgdir) as $file) {
            if (!$file->isDot() && !$file->isDir()) {
                if ($file->getMTime() < $expire) {
                    // only deletes files ending with $this->suffix
                    if (substr($file->getFilename(), -($suffixLength)) == $this->suffix) {
                        unlink($file->getPathname());
                    }
                }
            }
        }
    }

    /**
     * Get helper name used to render captcha
     *
     * @return string
     */
    public function getHelperName()
    {
        return 'captcha/image';
    }
}

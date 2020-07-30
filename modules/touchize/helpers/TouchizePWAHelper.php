<?php
/**
 * 2019 Touchize Sweden AB.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to prestashop@touchize.com so we can send you a copy immediately.
 *
 *  @author    Touchize Sweden AB <prestashop@touchize.com>
 *  @copyright 2018 Touchize Sweden AB
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Touchize Sweden AB
 */

/**
 * PWA helper.
 */

class TouchizePWAHelper extends TouchizeBaseHelper
{

    //iOS Device, width, height, aspectratio
    public $PWAsizes = array(
        array('iPhone Xs Max'                           ,1242, 2688, 3),
        array('iPhone Xr'                               ,828 , 1792, 2),
        array('iPhone X, Xs'                            ,1125, 2436, 3),
        array('iPhone 8 Plus, 7 Plus, 6s Plus, 6 Plus'  ,1242, 2208, 3),
        array('iPhone 8, 7, 6s, 6'                      ,750 , 1334, 2),
        array('12.9 iPad Pro'                           ,2048 ,2732, 2),
        array('11 iPad Pro'                             ,1668 ,2388, 2),
        array('10.5 iPad Pro'                           ,1668 ,2224, 2),
        array('iPad Mini, Air'                          ,1536 ,2048, 2),
    );

    public function getSplashName($width, $height)
    {
        return '/img/tz-apple-launch-'.$width.'x'.$height.'.png';
    }

    public function deleteSplashImages()
    {
        $this->createSplashImages(true);
    }

    public function createSplashImages($deleteOnly = false)
    {
        $pwaLogo = Configuration::get('TOUCHIZE_PWA_LOGO');
        if (empty($pwaLogo) || $pwaLogo === 0) {
            return;
        }
        $backgroundColor = Configuration::get('TOUCHIZE_PWA_BACKGROUND_COLOR');
        foreach ($this->PWAsizes as $PWAsize) {
            //Portrait
            $this->createSplashImage($pwaLogo, $deleteOnly, $PWAsize[1], $PWAsize[2], $backgroundColor);
            //Landscape
            $this->createSplashImage($pwaLogo, $deleteOnly, $PWAsize[2], $PWAsize[1], $backgroundColor);
        }
    }
    public function createSplashImage($pwaLogo, $deleteOnly, $width, $height, $backgroundColor)
    {
        $splashName = $this->getSplashName($width, $height);
        if (file_exists(_PS_ROOT_DIR_.$splashName)) {
            @unlink(_PS_ROOT_DIR_.$splashName);
        }
        if ($deleteOnly) {
            return;
        }
        if (file_exists(_PS_ROOT_DIR_.$pwaLogo) && filesize(_PS_ROOT_DIR_.$pwaLogo)) {
            $this->tzResize(
                _PS_ROOT_DIR_.$pwaLogo,
                _PS_ROOT_DIR_.$splashName,
                $width,
                $height,
                'png',
                true,
                $backgroundColor
            );
        }
    }

    public function getSplashes()
    {
        $splashes = array();
        foreach ($this->PWAsizes as $PWAsize) {
            $splashes[] = array(
                'device' => $PWAsize[0],
                'width'  => $PWAsize[1],
                'height' => $PWAsize[2],
                'ratio'  => $PWAsize[3],
            );
        }
        return $splashes;
    }

    private function hexColorAllocate($im, $hex)
    {
        $hex = ltrim($hex, '#');
        $a = hexdec(Tools::substr($hex, 0, 2));
        $b = hexdec(Tools::substr($hex, 2, 2));
        $c = hexdec(Tools::substr($hex, 4, 2));
        return imagecolorallocate($im, $a, $b, $c);
    }

    //Copy of ImageManager::resize but uses a background color instean
    public static function tzResize(
        $sourceFile,
        $destinationFile,
        $destinationWidth = null,
        $destinationHeight = null,
        $fileType = 'jpg',
        $forceType = false,
        $backgroundColor = '#FFFFFF',
        &$error = 0,
        &$targetWidth = null,
        &$targetHeight = null,
        $quality = 5,
        &$sourceWidth = null,
        &$sourceHeight = null
    ) {
        clearstatcache(true, $sourceFile);

        if (!file_exists($sourceFile) || !filesize($sourceFile)) {
            return !($error = ImageManager::ERROR_FILE_NOT_EXIST);
        }

        list($tmpWidth, $tmpHeight, $type) = getimagesize($sourceFile);
        $rotate = 0;
        if (function_exists('exif_read_data') && function_exists('mb_strtolower')) {
            $exif = @exif_read_data($sourceFile);

            if ($exif && isset($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $sourceWidth = $tmpWidth;
                        $sourceHeight = $tmpHeight;
                        $rotate = 180;
                        break;

                    case 6:
                        $sourceWidth = $tmpHeight;
                        $sourceHeight = $tmpWidth;
                        $rotate = -90;
                        break;

                    case 8:
                        $sourceWidth = $tmpHeight;
                        $sourceHeight = $tmpWidth;
                        $rotate = 90;
                        break;

                    default:
                        $sourceWidth = $tmpWidth;
                        $sourceHeight = $tmpHeight;
                }
            } else {
                $sourceWidth = $tmpWidth;
                $sourceHeight = $tmpHeight;
            }
        } else {
            $sourceWidth = $tmpWidth;
            $sourceHeight = $tmpHeight;
        }

        // If PS_IMAGE_QUALITY is activated, the generated image will be a PNG with .jpg as a file extension.
        // This allow for higher quality and for transparency. JPG source files will also benefit from a higher quality
        // because JPG reencoding by GD, even with max quality setting, degrades the image.
        if (Configuration::get('PS_IMAGE_QUALITY') == 'png_all'
            || (Configuration::get('PS_IMAGE_QUALITY') == 'png' && $type == IMAGETYPE_PNG) && !$forceType) {
            $fileType = 'png';
        }

        if (!$sourceWidth) {
            return !($error = ImageManager::ERROR_FILE_WIDTH);
        }
        if (!$destinationWidth) {
            $destinationWidth = $sourceWidth;
        }
        if (!$destinationHeight) {
            $destinationHeight = $sourceHeight;
        }

        $widthDiff = $destinationWidth / $sourceWidth;
        $heightDiff = $destinationHeight / $sourceHeight;

        $psImageGenerationMethod = Configuration::get('PS_IMAGE_GENERATION_METHOD');
        if ($widthDiff > 1 && $heightDiff > 1) {
            $nextWidth = $sourceWidth;
            $nextHeight = $sourceHeight;
        } else {
            if ($psImageGenerationMethod == 2 || (!$psImageGenerationMethod && $widthDiff > $heightDiff)) {
                $nextHeight = $destinationHeight;
                $nextWidth = round(($sourceWidth * $nextHeight) / $sourceHeight);
                $destinationWidth = (int) (!$psImageGenerationMethod ? $destinationWidth : $nextWidth);
            } else {
                $nextWidth = $destinationWidth;
                $nextHeight = round($sourceHeight * $destinationWidth / $sourceWidth);
                $destinationHeight = (int) (!$psImageGenerationMethod ? $destinationHeight : $nextHeight);
            }
        }

        if (!ImageManager::checkImageMemoryLimit($sourceFile)) {
            return !($error = ImageManager::ERROR_MEMORY_LIMIT);
        }

        $targetWidth  = $destinationWidth;
        $targetHeight = $destinationHeight;

        $destImage = imagecreatetruecolor($destinationWidth, $destinationHeight);

        // If image is a PNG and the output is PNG, fill with transparency. Else fill with white background.
        if ($fileType == 'png' && $type == IMAGETYPE_PNG) {
            imagealphablending($destImage, true);
            imagesavealpha($destImage, true);

            //TZ Change, hex and no transparency
            $hex = ltrim($backgroundColor, '#');
            $a = hexdec(Tools::substr($hex, 0, 2));
            $b = hexdec(Tools::substr($hex, 2, 2));
            $c = hexdec(Tools::substr($hex, 4, 2));
            $transparent = imagecolorallocatealpha($destImage, $a, $b, $c, 0);

//            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            imagefilledrectangle($destImage, 0, 0, $destinationWidth, $destinationHeight, $transparent);
        } else {
            $white = imagecolorallocate($destImage, 255, 255, 255);
            imagefilledrectangle($destImage, 0, 0, $destinationWidth, $destinationHeight, $white);
        }

        $srcImage = ImageManager::create($type, $sourceFile);
        if ($rotate) {
            $srcImage = imagerotate($srcImage, $rotate, 0);
        }

        //Touchize scale with factor to be more userfriendly
        $factor = min(($destinationWidth / 2) / $nextWidth, ($destinationHeight / 2) / $nextHeight);
        $nextWidth = $nextWidth * $factor;
        $nextHeight = $nextHeight * $factor;

        if ($destinationWidth >= $sourceWidth && $destinationHeight >= $sourceHeight) {
            imagecopyresized(
                $destImage,
                $srcImage,
                (int) (($destinationWidth - $nextWidth) / 2),
                (int) (($destinationHeight - $nextHeight) / 2),
                0,
                0,
                $nextWidth,
                $nextHeight,
                $sourceWidth,
                $sourceHeight
            );
        } else {
            ImageManager::imagecopyresampled(
                $destImage,
                $srcImage,
                (int) (($destinationWidth - $nextWidth) / 2),
                (int) (($destinationHeight - $nextHeight) / 2),
                0,
                0,
                $nextWidth,
                $nextHeight,
                $sourceWidth,
                $sourceHeight,
                $quality
            );
        }
        $writeFile = ImageManager::write($fileType, $destImage, $destinationFile);
        Hook::exec('actionOnImageResizeAfter', array('dst_file' => $destinationFile, 'file_type' => $fileType));
        @imagedestroy($srcImage);

        file_put_contents(
            dirname($destinationFile) . DIRECTORY_SEPARATOR . 'fileType',
            $fileType
        );

        return $writeFile;
    }
}

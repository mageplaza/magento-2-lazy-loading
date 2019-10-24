<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_LazyLoading
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\LazyLoading\Plugin\Model\Template;

use Magento\Cms\Model\Template\Filter as CmsFilter;
use Mageplaza\LazyLoading\Helper\Data as HelperData;
use Mageplaza\LazyLoading\Helper\Image as HelperImage;
use Magento\Framework\Filesystem\Io\File;

class Filter
{
    protected $helperData;
    protected $helperImage;
    protected $file;
    protected $moveImgTo = 'pub/media/mageplaza/lazyloading/';

    public function __construct(
        HelperData $helperData,
        HelperImage $helperImage,
        File $file
    ) {
        $this->helperData  = $helperData;
        $this->helperImage = $helperImage;
        $this->file        = $file;
    }

    public function afterFilter(CmsFilter $filter, $result)
    {
        if (!$this->helperData->isEnabled() || !$this->helperData->isLazyLoad()) {
            return $result;
        }

        $placeHolder = '';
        $holderType  = '';

        if ($this->helperData->getLoadingType() === 'icon') {
            $class       = 'mplazyload-icon mplazyload-cms';
            $placeHolder = $this->helperData->getIcon();
        } else {
            $holderType = $this->helperData->getPlaceholderType();
            $class      = 'mplazyload-' . $this->helperData->getPlaceholderType();
            if ($holderType === 'transparent') {
                $placeHolder = HelperData::DEFAULT_IMAGE;
            }
        }
        preg_match_all('/<img((.(?!class=))*)\/?>/', $result, $matches);

        $replaced = [];
        $search   = [];
        foreach ($matches[0] as $img) {
            if ($img) {
                if ($holderType !== 'transparent') {
                    $imgSrc  = $this->getImageSrc($img);
                    $imgPath = substr($imgSrc, strpos($imgSrc, 'pub'));
                    $imgInfo = $this->file->getPathInfo($imgPath);
                    $this->optimizeImage($this->filterSrc($imgPath), $imgInfo);
                    $placeHolder = $this->helperImage->getBaseMediaUrl()
                        . '/mageplaza/lazyloading/'
                        . $imgInfo['basename'];
                }
                $strProcess   = preg_replace('/src="/', 'data-src="', $img);
                $replaceClass = '<img class="mplazyload ' . $class . '" src="' . $placeHolder . '"';
                $strProcess   = preg_replace('/<img/', $replaceClass, $strProcess);
                $replaced[]   = $strProcess;
                $search[]     = $img;
            }
        }

        return str_replace($search, $replaced, $result);
    }

    public function filterSrc($path)
    {
        if (strpos($path, '/version') !== false) {
            $leftStr = substr($path, 0, strpos($path, '/version'));
            $rightStr = substr($path, strpos($path, '/frontend'));

            return $leftStr . $rightStr;
        }

        return $path;
    }

    public function getImageSrc($img)
    {
        preg_match('/src\s*=\s*"(.+?)"/', $img, $matches);

        return $matches[1];
    }

    public function optimizeImage($imgPath, $imgInfo)
    {
        $quality = 10;

        if ($dir = opendir($this->filterSrc($imgInfo['dirname']))) {
            $checkValidImage = getimagesize($imgPath);

            if ($checkValidImage) {
                $this->changeQuality($imgPath, $this->moveImgTo . $imgInfo['basename'], $quality);
            }
            closedir($dir);
        }
    }

    public function changeQuality($srcImage, $destImage, $imageQuality)
    {
        list($width, $height, $type) = getimagesize($srcImage);
        $newCanvas = imagecreatetruecolor($width, $height);
        switch (strtolower(image_type_to_mime_type($type))) {
            case 'image/jpeg':
                $newImage = imagecreatefromjpeg($srcImage);
                break;
            case 'image/JPEG':
                $newImage = imagecreatefromjpeg($srcImage);
                break;
            case 'image/png':
                $newImage = imagecreatefrompng($srcImage);
                break;
            case 'image/PNG':
                $newImage = imagecreatefrompng($srcImage);
                break;
            case 'image/gif':
                $newImage = imagecreatefromgif($srcImage);
                break;
            default:
                return false;
        }

        if (imagecopyresampled(
            $newCanvas,
            $newImage,
            0,
            0,
            0,
            0,
            $width,
            $height,
            $width,
            $height
        )) {
            if (imagejpeg($newCanvas, $destImage, $imageQuality)) {
                imagedestroy($newCanvas);

                return true;
            }
        }
    }
}

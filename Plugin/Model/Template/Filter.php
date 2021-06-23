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
 * @category  Mageplaza
 * @package   Mageplaza_LazyLoading
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\LazyLoading\Plugin\Model\Template;

use Magento\Cms\Model\Template\Filter as CmsFilter;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\DriverInterface;
use Mageplaza\LazyLoading\Helper\Data as HelperData;
use Mageplaza\LazyLoading\Helper\Image as HelperImage;
use Mageplaza\LazyLoading\Model\Config\Source\System\LoadingType;
use Mageplaza\LazyLoading\Model\Config\Source\System\PlaceholderType;

/**
 * Class Filter
 *
 * @package Mageplaza\LazyLoading\Plugin\Model\Template
 */
class Filter
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var HelperImage
     */
    protected $helperImage;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var string
     */
    protected $moveImgTo = 'mageplaza/lazyloading/';

    /**
     * Filter constructor.
     * @param HelperData $helperData
     * @param HelperImage $helperImage
     * @param File $file
     * @param Filesystem $filesystem
     * @param DriverInterface $driver
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        HelperData $helperData,
        HelperImage $helperImage,
        File $file,
        Filesystem $filesystem,
        DriverInterface $driver
    ) {
        $this->helperData  = $helperData;
        $this->helperImage = $helperImage;
        $this->file        = $file;
        $this->driver      = $driver;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    /**
     * @param CmsFilter $filter
     * @param string $result
     *
     * @return mixed
     * @throws NoSuchEntityException
     * @SuppressWarnings("Unused")
     */
    public function afterFilter(CmsFilter $filter, $result)
    {
        if (!$this->helperData->isEnabled() || !$this->helperData->isLazyLoad()) {
            return $result;
        }

        $placeHolder = '';
        $holderType  = '';
        $loadingType = $this->helperData->getLoadingType();

        if ($loadingType === LoadingType::ICON) {
            $class       = 'mplazyload mplazyload-icon mplazyload-cms';
            $placeHolder = $this->helperData->getIcon();
        } else {
            $holderType = $this->helperData->getPlaceholderType();
            $class      = 'mplazyload mplazyload-' . $this->helperData->getPlaceholderType();
            if ($holderType === PlaceholderType::TRANSPARENT) {
                $placeHolder = HelperData::DEFAULT_IMAGE;
            }
        }

        preg_match_all('/<img.*?src="(.*?)"[^\>]+>/', $result, $matches);
        $replaced = [];
        $search   = [];

        foreach ($matches[0] as $img) {
            if ($img && !$this->helperData->isExcludeText($this->getImageText($img))) {
                if ($holderType !== PlaceholderType::TRANSPARENT && $loadingType === LoadingType::PLACEHOLDER) {
                    $imgSrc  = $this->getImageSrc($img);
                    $imgPath = substr($imgSrc, strpos($imgSrc, 'pub'));
                    $imgInfo = $this->file->getPathInfo($imgPath);
                    $this->optimizeImage($this->filterSrc($imgPath), $imgInfo);
                    $placeHolder = $this->helperImage->getBaseMediaUrl()
                        . '/mageplaza/lazyloading/'
                        . $imgInfo['basename'];
                }

                // dont replace twice, in static block widget if they already have been processed and have data-src
                if (strpos($img, 'data-src="') !== false) {
                    continue;
                }

                if (strpos($img, 'class="') !== false) {
                    $newClass = str_replace('class="', 'class="' . $class . ' ', $img);
                } else {
                    $newClass = str_replace('<img', '<img class="' . $class . '"', $img);
                }

                $strProcess = str_replace('src="', 'src="' . $placeHolder . '" data-src="', $newClass);

                if (!$this->helperData->isExcludeClass($this->getImageClass($strProcess))) {
                    $replaced[] = $strProcess;
                    $search[]   = $img;
                }
            }
        }

        return str_replace($search, $replaced, $result);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function filterSrc($path)
    {
        if (strpos($path, '/version') !== false) {
            $leftStr  = substr($path, 0, strpos($path, '/version'));
            $rightStr = substr($path, strpos($path, '/frontend'));

            return $leftStr . $rightStr;
        }

        return $path;
    }

    /**
     * @param string $img
     *
     * @return array
     */
    public function getImageClass($img)
    {
        preg_match('/class\s*=\s*"(.+?)"/', $img, $matches);
        if ($matches) {
            return explode(' ', $matches[1]);
        }

        return [];
    }

    /**
     * @param string $img
     *
     * @return null
     */
    public function getImageText($img)
    {
        preg_match('/alt\s*=\s*"(.+?)"/', $img, $alt);
        preg_match('/title\s*=\s*"(.+?)"/', $img, $title);
        preg_match('/src\s*=\s*"(.+?)"/', $img, $src);

        $result = '';

        if ($alt && strpos($alt[1], 'title="') !== false) {
            $result .= $alt[1];
        } elseif ($this->helperData->getConfigValue('seo/general/enabled')
            && $this->helperData->getConfigValue('seo/seo_rule/enable_automate_alt_image')) {
            $imgName = substr($src[1], strrpos($src[1], '/'));
            $result  .= preg_replace('/.jpg|.png|.gif|.bmp|.svg|\/|-/', '', $imgName);
        }

        if ($title) {
            $result .= ' ' . $title[1];
        }

        return $result ?: null;
    }

    /**
     * @param string $img
     *
     * @return mixed
     */
    public function getImageSrc($img)
    {
        preg_match('/src\s*=\s*"(.+?)"/', $img, $matches);

        return $matches[1];
    }

    /**
     * @param string $imgPath
     * @param array $imgInfo
     */
    public function optimizeImage($imgPath, $imgInfo)
    {
        $quality = 10;
        $imgPath = str_replace('pub/media', "", $imgPath);

        if ($this->mediaDirectory->isFile($imgPath)) {
            $absolutePath = $this->mediaDirectory->getAbsolutePath().$imgPath;
            $checkValidImage = getimagesize($absolutePath);

            if ($checkValidImage) {
                $this->changeQuality($absolutePath, $this->mediaDirectory->getAbsolutePath().$this->moveImgTo . $imgInfo['basename'], $quality);
            }
        }
    }

    /**
     * @param string $srcImage
     * @param string $destImage
     * @param int $imageQuality
     *
     * @return bool
     */
    public function changeQuality($srcImage, $destImage, $imageQuality)
    {
        if (!$this->mediaDirectory->isFile($destImage)) {
            return false;
        }
        [$width, $height, $type] = getimagesize($srcImage);
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
        )
        ) {
            if (imagejpeg($newCanvas, $destImage, $imageQuality)) {
                imagedestroy($newCanvas);

                return true;
            }
        }

        return false;
    }
}

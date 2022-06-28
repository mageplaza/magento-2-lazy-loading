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

namespace Mageplaza\LazyLoading\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\LazyLoading\Helper\Image as HelperImage;
use Mageplaza\LazyLoading\Model\Config\Source\System\ApplyFor;

/**
 * Class Data
 *
 * @package Mageplaza\LazyLoading\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'mplazyload';
    const DEFAULT_IMAGE      = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

    /**
     * @var Image
     */
    protected $helperImage;

    /**
     * @var Repository
     */
    protected $assetRepo;

    /**
     * @var array
     */
    public $relatedBlock = [
        'related_products_list',
        'upsell_products_list',
        'cart_cross_sell_products'
    ];

    /**
     * @var File
     */
    protected $file;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var string
     */
    protected $moveImgTo = 'mageplaza/lazyloading/';

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param Image $helperImage
     * @param Repository $assetRepo
     * @param File $file
     * @param Filesystem $filesystem
     * @param DirectoryList $directoryList
     *
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        HelperImage $helperImage,
        Repository $assetRepo,
        File $file,
        Filesystem $filesystem,
        DirectoryList $directoryList
    ) {
        $this->helperImage    = $helperImage;
        $this->assetRepo      = $assetRepo;
        $this->file           = $file;
        $this->directoryList  = $directoryList;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        parent::__construct($context, $objectManager, $storeManager);
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
     * @param $imgPath
     * @param $imgInfo
     *
     * @return $this
     * @throws FileSystemException
     */
    public function optimizeImage($imgPath, $imgInfo)
    {
        $rootImage = $this->directoryList->getRoot() . '/' . $imgPath;

        if (strpos($rootImage, 'pub/static')) {
            return $this;
        }
        if ($this->file->fileExists($imgPath)) {
            $rootImage = $imgPath;
        }

        $moveTo = $this->mediaDirectory->getAbsolutePath() . $this->moveImgTo . $imgInfo['basename'];

        if ($this->file->fileExists($moveTo)) {
            return $this;
        }

        if ($this->file->fileExists($rootImage)) {
            $image = str_replace($this->mediaDirectory->getAbsolutePath(), '', $rootImage);
            $image = str_replace('pub/media/', '', $image);

            $this->mediaDirectory->copyFile(
                $image,
                $moveTo
            );
            $quality         = 9;
            $checkValidImage = getimagesize($rootImage);
            if ($checkValidImage) {
                $this->changeQuality(
                    $rootImage,
                    $moveTo,
                    $quality
                );
            }
        }

        return $this;
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

    /**
     * @return mixed
     */
    public function getApplyFor()
    {
        return $this->getConfigGeneral('apply_for');
    }

    /**
     * @return array
     */
    public function getExcludePage()
    {
        $list  = [];
        $pages = self::jsonDecode($this->getConfigGeneral('exclude_page'));

        if ($pages) {
            foreach ($pages as $value) {
                $list[] = $value['url_contains'];
            }
        }

        return $list;
    }

    /**
     * @return array
     */
    public function getExcludeCss()
    {
        $result = [];
        $class  = self::jsonDecode($this->getConfigGeneral('exclude_css'));
        foreach ($class as $item) {
            $result[] = $item['css_class'];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getExcludeText()
    {
        $result = [];
        $text   = self::jsonDecode($this->getConfigGeneral('exclude_text'));
        foreach ($text as $item) {
            $result[] = $item['text'];
        }

        return $result;
    }

    /**
     * @param array $class
     *
     * @return bool
     */
    public function isExcludeClass($class)
    {
        foreach ($this->getExcludeCss() as $item) {
            if (in_array($item, $class, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $text
     *
     * @return bool
     */
    public function isExcludeText($text)
    {
        foreach ($this->getExcludeText() as $item) {
            if (strpos($text, $item) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param null $imageId
     *
     * @return bool
     */
    public function isLazyLoad($imageId = null)
    {
        if (!$this->isEnabled()) {
            return false;
        }

        if ($this->checkApplyFor(ApplyFor::CATEGORY_PAGE, 'catalog_category_view')) {
            return true;
        }

        if (in_array($imageId, $this->relatedBlock, true)
            && strpos($this->getApplyFor(), ApplyFor::RELATED_BLOCK) !== false
        ) {
            return true;
        }

        if ($this->checkApplyFor(ApplyFor::PRODUCT_PAGE, 'catalog_product_view')) {
            if (in_array($imageId, $this->relatedBlock, true)) {
                if (strpos($this->getApplyFor(), ApplyFor::RELATED_BLOCK) === false) {
                    return false;
                }

                return true;
            }

            return true;
        }

        if ($this->checkApplyFor(ApplyFor::CMS_PAGE, 'cms')) {
            return true;
        }

        if ($this->checkApplyFor(ApplyFor::SEARCH_PAGE, 'catalogsearch_result_index')) {
            return true;
        }

        return false;
    }

    /**
     * @param string $page
     * @param string $fullActionName
     *
     * @return bool
     */
    public function checkApplyFor($page, $fullActionName)
    {
        $currentUrl = $this->_urlBuilder->getCurrentUrl();
        $pages      = true;

        foreach ($this->getExcludePage() as $value) {
            if (strpos($currentUrl, $value) !== false) {
                $pages = false;
            }
        }

        return $pages && strpos($this->getApplyFor(), $page) !== false
            && strpos($this->_getRequest()->getFullActionName(), $fullActionName) !== false;
    }

    /**
     * @return string
     */
    public function getDefaultIcon()
    {
        return $this->assetRepo->getUrl('Mageplaza_LazyLoading::mageplaza/lazyloading/loader.gif');
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getIcon()
    {
        if ($icon = $this->getConfigGeneral('icon')) {
            return $this->helperImage->getMediaUrl($this->helperImage->getMediaPath($icon));
        }

        return $this->getDefaultIcon();
    }

    /**
     * @return mixed
     */
    public function getLoadingType()
    {
        return $this->getConfigGeneral('loading_type');
    }

    /**
     * @return mixed
     */
    public function getPlaceholderType()
    {
        return $this->getConfigGeneral('placeholder_type');
    }

    /**
     * @return mixed
     */
    public function getThreshold()
    {
        return $this->getConfigGeneral('threshold');
    }
}

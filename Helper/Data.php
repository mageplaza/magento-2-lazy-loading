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
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\LazyLoading\Helper\Image as HelperImage;
use Magento\Framework\View\Asset\Repository;

/**
 * Class Data
 *
 * @package Mageplaza\ProductFinder\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'mplazyload';
    const DEFAULT_IMAGE      = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
    protected $helperImage;
    protected $assetRepo;
    public $relatedBlock = [
        'related_products_list',
        'upsell_products_list',
        'cart_cross_sell_products'
    ];

    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        HelperImage $helperImage,
        Repository $assetRepo
    ) {
        $this->helperImage = $helperImage;
        $this->assetRepo   = $assetRepo;
        parent::__construct($context, $objectManager, $storeManager);
    }

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
        //        return self::jsonEncode($result);
        //        return $this->getConfigGeneral('exclude_css');
    }

    public function getExcludeText()
    {
        $result = [];
        $text   = self::jsonDecode($this->getConfigGeneral('exclude_text'));
        foreach ($text as $item) {
            $result[] = $item['text'];
        }

        return $result;
    }

    public function isExcludeClass($class)
    {
        foreach ($this->getExcludeCss() as $item) {
            if (in_array($item, $class, true)) {
                return true;
            }
        }

        return false;
    }

    public function isExcludeText($text)
    {
        foreach ($this->getExcludeCss() as $item) {
            if (strpos($text, $item) !== false) {
                return true;
            }
        }

        return false;
    }

    public function isLazyLoad($imageId = null)
    {
        if (!$this->isEnabled()) {
            return false;
        }

        if ($this->checkApplyFor('category', 'catalog_category_view')) {
            return true;
        }

        if (in_array($imageId, $this->relatedBlock, true)
            && strpos($this->getApplyFor(), 'related') !== false
        ) {
            return true;
        }

        if ($this->checkApplyFor('product', 'catalog_product_view')) {
            return true;
        }

        if ($this->checkApplyFor('cms', 'cms')) {
            return true;
        }

        if ($this->checkApplyFor('search', 'catalogsearch_result_index')) {
            return true;
        }

        return false;
    }

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

    public function getDefaultIcon()
    {
        return $this->assetRepo->getUrl('Mageplaza_LazyLoading::mageplaza/lazyloading/loader.gif');
    }

    public function getIcon()
    {
        if ($icon = $this->getConfigGeneral('icon')) {
            return $this->helperImage->getMediaUrl($this->helperImage->getMediaPath($icon));
        }

        return $this->getDefaultIcon();
    }

    public function getLoadingType()
    {
        return $this->getConfigGeneral('loading_type');
    }

    public function getPlaceholderType()
    {
        return $this->getConfigGeneral('placeholder_type');
    }
}

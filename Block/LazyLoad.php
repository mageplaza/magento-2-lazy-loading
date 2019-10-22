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

namespace Mageplaza\LazyLoading\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\LazyLoading\Helper\Image as HelperImage;
use Mageplaza\LazyLoading\Helper\Data as HelperData;
use Magento\Framework\View\Asset\Repository;

/**
 * Class LazyLoad
 * @package Mageplaza\LazyLoading\Block
 */
class LazyLoad extends Template
{
    protected $helperImage;
    protected $helperData;
    protected $assetRepo;

    public function __construct(
        Context $context,
        HelperImage $helperImage,
        HelperData $helperData,
        Repository $assetRepo,
        array $data = []
    ) {
        $this->helperImage = $helperImage;
        $this->helperData  = $helperData;
        $this->assetRepo   = $assetRepo;
        parent::__construct($context, $data);
    }

    public function getDefaultIcon()
    {
        return $this->assetRepo->getUrl('Mageplaza_LazyLoading::mageplaza/lazyloading/loader.gif');
    }

//    public function resizeIcon()
//    {
//        $icon = $this->helperData->getConfigGeneral('icon');
//
//        return $this->helperImage->resizeIcon($icon, $this->getResizeWith(), $this->getResizeHeight());
//    }

    public function isLazyLoad()
    {
        return $this->helperData->isLazyLoad();
    }

    public function getLoadingType()
    {
        return $this->helperData->getLoadingType();
    }

    public function getThreshold()
    {
        return $this->helperData->getConfigGeneral('threshold');
    }

    public function getResizeWith()
    {
        return $this->helperData->getConfigGeneral('resize_width');
    }

    public function getResizeHeight()
    {
        return $this->helperData->getConfigGeneral('resize_height');
    }

    public function getExcludeCss()
    {
        return $this->helperData->getExcludeCss();
    }

    public function getExcludeText()
    {
        return $this->helperData->getExcludeText();
    }
}

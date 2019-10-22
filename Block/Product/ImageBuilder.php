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

namespace Mageplaza\LazyLoading\Block\Product;

use GuzzleHttp\Ring\Core;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Helper\ImageFactory as HelperFactory;
use Magento\Catalog\Model\Product\Image\NotLoadInfoImageException;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Product;
use Mageplaza\LazyLoading\Helper\Data as HelpData;
use Magento\Catalog\Helper\Image as CoreHelpImage;

/**
 * Class ImageBuilder
 * @package Mageplaza\LazyLoading\Block\Product
 */
class ImageBuilder extends \Magento\Catalog\Block\Product\ImageBuilder
{
    protected $helperData;
    protected $coreHelperImage;

    public function __construct(
        HelperFactory $helperFactory,
        ImageFactory $imageFactory,
        HelpData $helperData,
        CoreHelpImage $coreHelperImage
    ) {
        $this->helperData      = $helperData;
        $this->coreHelperImage = $coreHelperImage;
        parent::__construct($helperFactory, $imageFactory);
    }

    public function create(Product $product = null, string $imageId = null, array $attributes = null)
    {
        $product    = $product ?: $this->product;
        $imageId    = $imageId ?: $this->imageId;
        $attributes = $attributes ?: $this->attributes;

        if (!$this->helperData->isEnabled() || $this->helperData->isLazyLoad($imageId) === false) {
            return parent::create($product, $imageId, $attributes);
        }

        /** @var \Magento\Catalog\Helper\Image $helper */
        $imageFactory           = $this->helperFactory->create()->init($product, $imageId);
        $attributes['data-src'] = $imageFactory->getUrl();
        $this->setAttributes($attributes);
        $lowImage = HelpData::DEFAULT_IMAGE;
        $loadingType = $this->helperData->getLoadingType();
        $placeholderType = $this->helperData->getPlaceholderType();

        if ($loadingType === 'placeholder' && $placeholderType !== 'transparent') {
            $width    = $imageFactory->getWidth() - 1;
            $height   = $imageFactory->getHeight() - 1;
            $attrs    = compact('width', 'height');
            $lowImage = $this->coreHelperImage->init($product, $imageId, $attrs)->setQuality(10);
            $lowImage = $lowImage->getUrl();
        }

        $data = [
            'template'          => 'Mageplaza_LazyLoading::product/image_with_borders.phtml',
            'image_url'         => $lowImage,
            'width'             => $imageFactory->getWidth(),
            'height'            => $imageFactory->getHeight(),
            'label'             => $imageFactory->getLabel(),
            'ratio'             => $this->getRatio($imageFactory),
            'custom_attributes' => $this->getCustomAttributes(),
            'product_id'        => $product->getId(),
            'loading_type'      => $loadingType,
            'icon'              => $this->helperData->getIcon(),
            'placeholder_type'  => $placeholderType
        ];

        $result = $this->imageFactory->create($product, $imageId, $attributes);
        $result->setData($data);

        return $result;
    }
}
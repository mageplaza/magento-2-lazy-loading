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

namespace Mageplaza\LazyLoading\Block\Product;

use Magento\Catalog\Block\Product\Image;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Helper\Image as CoreHelpImage;
use Magento\Catalog\Helper\ImageFactory as HelperFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\LazyLoading\Helper\Data as HelpData;
use Mageplaza\LazyLoading\Model\Config\Source\System\LoadingType;
use Mageplaza\LazyLoading\Model\Config\Source\System\PlaceholderType;

/**
 * Class ImageBuilder
 *
 * @package Mageplaza\LazyLoading\Block\Product
 */
class ImageBuilder extends \Magento\Catalog\Block\Product\ImageBuilder
{
    /**
     * @var HelpData
     */
    protected $helperData;

    /**
     * @var CoreHelpImage
     */
    protected $coreHelperImage;

    /**
     * @var array
     */
    public $excludeClass = [
        'product-image-photo',
        'mplazyload',
        'mplazyload-icon',
        'mplazyload-blur',
        'mplazyload-low',
        'mplazyload-transparent'
    ];

    /**
     * ImageBuilder constructor.
     *
     * @param HelperFactory $helperFactory
     * @param ImageFactory $imageFactory
     * @param HelpData $helperData
     * @param CoreHelpImage $coreHelperImage
     */
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

    /**
     * @param Product|null $product
     * @param string|null $imageId
     * @param array|null $attributes
     *
     * @return Image
     * @throws NoSuchEntityException
     */
    public function create(Product $product = null, string $imageId = null, array $attributes = null)
    {
        $product    = $product ?: $this->product;
        $imageId    = $imageId ?: $this->imageId;
        $attributes = $attributes ?: $this->attributes;

        if (!$this->helperData->isEnabled()
            || $this->helperData->isLazyLoad($imageId) === false
            || !$this->helperData->isLazyLoad()) {
            return parent::create($product, $imageId, $attributes);
        }

        $imageFactory           = $this->helperFactory->create()->init($product, $imageId);
        $attributes['data-src'] = $imageFactory->getUrl();
        $this->setAttributes($attributes);
        $lazyImg         = HelpData::DEFAULT_IMAGE;
        $loadingType     = $this->helperData->getLoadingType();
        $placeholderType = $this->helperData->getPlaceholderType();

        if ($loadingType === LoadingType::PLACEHOLDER && $placeholderType !== PlaceholderType::TRANSPARENT) {
            $width   = $imageFactory->getWidth() - 1;
            $height  = $imageFactory->getHeight() - 1;
            $attrs   = compact('width', 'height');
            $lazyImg = $this->coreHelperImage->init($product, $imageId, $attrs)->setQuality(10);
            $lazyImg = $lazyImg->getUrl();
        }

        $isExclude = false;
        if ($this->checkExcludeClass() || $this->helperData->isExcludeText($imageFactory->getLabel())) {
            $lazyImg   = $imageFactory->getUrl();
            $isExclude = true;
        }

        $data = [
            'template'          => 'Mageplaza_LazyLoading::product/image_with_borders.phtml',
            'image_url'         => $lazyImg,
            'width'             => $imageFactory->getWidth(),
            'height'            => $imageFactory->getHeight(),
            'label'             => $imageFactory->getLabel(),
            'ratio'             => $this->getRatio($imageFactory),
            'custom_attributes' => $this->getCustomAttributes(),
            'product_id'        => $product->getId(),
            'loading_type'      => $loadingType,
            'icon'              => $this->helperData->getIcon(),
            'placeholder_type'  => $placeholderType,
            'is_exclude'        => $isExclude
        ];

        if ($this->helperData->versionCompare('2.3.0', '<=')) {
            return $this->imageFactory->create(['data' => $data]);
        }

        $result = $this->imageFactory->create($product, $imageId, $attributes);
        $result->setData($data);

        return $result;
    }

    /**
     * @return bool
     */
    public function checkExcludeClass()
    {
        foreach ($this->excludeClass as $item) {
            if (in_array($item, $this->helperData->getExcludeCss(), true)) {
                return true;
            }
        }

        return false;
    }
}

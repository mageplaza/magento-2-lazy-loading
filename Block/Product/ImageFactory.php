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

use Magento\Catalog\Block\Product\Image as ImageBlock;
use Magento\Catalog\Model\View\Asset\ImageFactory as AssetImageFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Image\ParamsBuilder;
use Magento\Catalog\Model\View\Asset\PlaceholderFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\ConfigInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Filesystem;
use Magento\Catalog\Helper\ImageFactory as HelperFactory;
use Mageplaza\LazyLoading\Helper\Data;
use Mageplaza\LazyLoading\Helper\Image;

/**
 * Class ImageFactory
 * @package Mageplaza\LazyLoading\Block\Product
 */
class ImageFactory extends \Magento\Catalog\Block\Product\ImageFactory
{
    /**
     * @var ConfigInterface
     */
    private $presentationConfig;

    /**
     * @var AssetImageFactory
     */
    private $viewAssetImageFactory;

    /**
     * @var ParamsBuilder
     */
    private $imageParamsBuilder;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var PlaceholderFactory
     */
    private $viewAssetPlaceholderFactory;

    /**
     * @var File
     */
    private $file;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var HelperFactory
     */
    private  $helperFactory;

    /**
     * @var Image
     */
    private $helperImage;

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
     * ImageFactory constructor.
     * @param ObjectManagerInterface $objectManager
     * @param ConfigInterface $presentationConfig
     * @param AssetImageFactory $viewAssetImageFactory
     * @param PlaceholderFactory $viewAssetPlaceholderFactory
     * @param ParamsBuilder $imageParamsBuilder
     * @param File $file
     * @param Data $data
     * @param Filesystem $filesystem
     * @param Image $helperImage
     * @param HelperFactory $helperFactory
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ConfigInterface $presentationConfig,
        AssetImageFactory $viewAssetImageFactory,
        PlaceholderFactory $viewAssetPlaceholderFactory,
        ParamsBuilder $imageParamsBuilder,
        File $file,
        Data $data,
        Filesystem $filesystem,
        Image $helperImage,
        HelperFactory $helperFactory
    ) {
        $this->objectManager = $objectManager;
        $this->presentationConfig = $presentationConfig;
        $this->viewAssetPlaceholderFactory = $viewAssetPlaceholderFactory;
        $this->viewAssetImageFactory = $viewAssetImageFactory;
        $this->imageParamsBuilder = $imageParamsBuilder;
        $this->file = $file;
        $this->helper = $data;
        $this->helperImage = $helperImage;
        $this->helperFactory = $helperFactory;
    }

    /**
     * Remove class from custom attributes
     *
     * @param array $attributes
     * @return array
     */
    private function filterCustomAttributes(array $attributes): array
    {
        if (isset($attributes['class'])) {
            unset($attributes['class']);
        }
        return $attributes;
    }

    /**
     * Retrieve image class for HTML element
     *
     * @param array $attributes
     * @return string
     */
    private function getClass(array $attributes): string
    {
        return $attributes['class'] ?? 'product-image-photo';
    }

    /**
     * Calculate image ratio
     *
     * @param int $width
     * @param int $height
     * @return float
     */
    private function getRatio(int $width, int $height): float
    {
        if ($width && $height) {
            return $height / $width;
        }
        return 1.0;
    }

    /**
     * Get image label
     *
     * @param Product $product
     * @param string $imageType
     * @return string
     */
    private function getLabel(Product $product, string $imageType): string
    {
        $label = $product->getData($imageType . '_' . 'label');
        if (empty($label)) {
            $label = $product->getName();
        }
        return (string)$label;
    }

    /**
     * @param Product $product
     * @param string $imageId
     * @param array|null $attributes
     * @return ImageBlock
     * @throws LocalizedException
     */
    public function create(Product $product, string $imageId, array $attributes = null): ImageBlock
    {
        $viewImageConfig = $this->presentationConfig->getViewConfig()->getMediaAttributes(
            'Magento_Catalog',
            ImageHelper::MEDIA_TYPE_CONFIG_NODE,
            $imageId
        );

        $imageMiscParams = $this->imageParamsBuilder->build($viewImageConfig);
        $originalFilePath = $product->getData($imageMiscParams['image_type']);

        if ($originalFilePath === null || $originalFilePath === 'no_selection') {
            $imageAsset = $this->viewAssetPlaceholderFactory->create(
                [
                    'type' => $imageMiscParams['image_type']
                ]
            );
        } else {
            $imageAsset = $this->viewAssetImageFactory->create(
                [
                    'miscParams' => $imageMiscParams,
                    'filePath' => $originalFilePath,
                ]
            );
        }

        $attributes = $attributes === null ? [] : $attributes;

        $imageFactory = $this->helperFactory->create()->init($product, $imageId);
        $isExclude = false;
        if ($this->checkExcludeClass() || $this->helper->isExcludeText($imageFactory->getLabel())) {
            $isExclude = true;
        }

        $data = [
            'data' => [
                'template' => 'Magento_Catalog::product/image_with_borders.phtml',
                'image_url' => $imageAsset->getUrl(),
                'width' => $imageMiscParams['image_width'],
                'height' => $imageMiscParams['image_height'],
                'label' => $this->getLabel($product, $imageMiscParams['image_type']),
                'ratio' => $this->getRatio($imageMiscParams['image_width'] ?? 0, $imageMiscParams['image_height'] ?? 0),
                'custom_attributes' => $this->filterCustomAttributes($attributes),
                'class' => $this->getClass($attributes),
                'product_id' => $product->getId(),
                'is_exclude' => $isExclude
            ],
        ];

        if (!$this->helper->isEnabled()) {
            return $this->objectManager->create(ImageBlock::class, $data);
        }

        $rootImage = $imageAsset->getUrl();
        $imgPath = substr($rootImage, strpos($rootImage, 'pub'));
        $imgInfo = $this->file->getPathInfo($imgPath);
        $this->helper->optimizeImage($this->helper->filterSrc($imgPath), $imgInfo);

        $lazyImage = $this->helperImage->getBaseMediaUrl() . '/mageplaza/lazyloading/' . $imgInfo['basename'];
        $data['data']['lazy_image'] = $lazyImage;
        $data['data']['template'] = 'Mageplaza_LazyLoading::product/image_with_borders.phtml';

        return $this->objectManager->create(ImageBlock::class, $data);
    }

    /**
     * @return bool
     */
    public function checkExcludeClass()
    {
        foreach ($this->excludeClass as $item) {
            if (in_array($item, $this->helper->getExcludeCss(), true)) {
                return true;
            }
        }

        return false;
    }
}

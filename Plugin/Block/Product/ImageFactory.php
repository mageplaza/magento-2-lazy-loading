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

namespace Mageplaza\LazyLoading\Plugin\Block\Product;

use Magento\Catalog\Block\Product\ImageFactory as CoreImageFactory;
use Mageplaza\LazyLoading\Helper\Data as HelperData;

/**
 * Class ImageFactory
 *
 * @package Mageplaza\LazyLoading\Plugin\Block\Product
 */
class ImageFactory
{
    protected $helperData;

    public function __construct(
        HelperData $helperData
    ) {
        $this->helperData    = $helperData;
    }

    public function afterCreate(CoreImageFactory $subject, $result)
    {
        if ($this->helperData->isEnabled()) {
            //            $lowRes = $this->coreHelperImage->setQuality(10);
            //            $data             = $result->getData();
            //            $data['template'] = 'Mageplaza_LazyLoading::product/image_with_borders.phtml';
            //            $data['low_image'] = $lowRes->getUrl();
            $result->setTemplate('Mageplaza_LazyLoading::product/image_with_borders.phtml');
            //            return $this->objectManager->create(\Mageplaza\LazyLoading\Block\Product\Image::class, ['data' => $data]);
        }

        return $result;
    }
}

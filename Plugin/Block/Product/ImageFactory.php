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
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * ImageFactory constructor.
     *
     * @param HelperData $helperData
     */
    public function __construct(
        HelperData $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param CoreImageFactory $subject
     * @param $result
     *
     * @return mixed
     * @SuppressWarnings("Unused")
     */
    public function afterCreate(CoreImageFactory $subject, $result)
    {
        if ($this->helperData->isEnabled()) {
            $result->setTemplate('Mageplaza_LazyLoading::product/image_with_borders.phtml');
        }

        return $result;
    }
}

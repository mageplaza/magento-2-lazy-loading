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
namespace Mageplaza\LazyLoading\Model\Config\Source\System;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ApplyFor
 *
 * @package Mageplaza\LazyLoading\Model\Config\Source\System
 */
class ApplyFor implements ArrayInterface
{
    const CATEGORY_PAGE = 'category';
    const PRODUCT_PAGE  = 'product';
    const CMS_PAGE      = 'cms';
    const SEARCH_PAGE   = 'search';
    const RELATED_BLOCK = 'related';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::CATEGORY_PAGE, 'label' => __('Category Page')],
            ['value' => self::PRODUCT_PAGE, 'label' => __('Product Detail Page')],
            ['value' => self::CMS_PAGE, 'label' => __('CMS Page')],
            ['value' => self::SEARCH_PAGE, 'label' => __('Search Result Page')],
            ['value' => self::RELATED_BLOCK, 'label' => __('Related, Cross-Sell, Up-Sell Products')]
        ];
    }
}

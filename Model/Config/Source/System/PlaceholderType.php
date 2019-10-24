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
 * Class PlaceholderType
 *
 * @package Mageplaza\LazyLoading\Model\Config\Source\System
 */
class PlaceholderType implements ArrayInterface
{
    const BLURRED        = 'blur';
    const LOW_RESOLUTION = 'low';
    const TRANSPARENT    = 'transparent';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::BLURRED, 'label' => __('Blurred (with transition)')],
            ['value' => self::LOW_RESOLUTION, 'label' => __('Low Resolution')],
            ['value' => self::TRANSPARENT, 'label' => __('Transparent')]
        ];
    }
}

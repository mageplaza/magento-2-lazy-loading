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
 * Class LoadingType
 *
 * @package Mageplaza\LazyLoading\Model\Config\Source\System
 */
class LoadingType implements ArrayInterface
{
    const ICON        = 'icon';
    const PLACEHOLDER = 'placeholder';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ICON, 'label' => __('Icon')],
            ['value' => self::PLACEHOLDER, 'label' => __('Placeholder')]
        ];
    }
}

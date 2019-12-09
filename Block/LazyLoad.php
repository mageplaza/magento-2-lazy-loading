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

namespace Mageplaza\LazyLoading\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\LazyLoading\Helper\Data as HelperData;

/**
 * Class LazyLoad
 *
 * @package Mageplaza\LazyLoading\Block
 */
class LazyLoad extends Template
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * LazyLoad constructor.
     *
     * @param Context $context
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        array $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isLazyLoad()
    {
        return $this->helperData->isLazyLoad();
    }

    /**
     * @return mixed
     */
    public function getLoadingType()
    {
        return $this->helperData->getLoadingType();
    }

    /**
     * @return mixed
     */
    public function getThreshold()
    {
        return $this->helperData->getConfigGeneral('threshold');
    }

    /**
     * @return mixed
     */
    public function getResizeWith()
    {
        return $this->helperData->getConfigGeneral('resize_width');
    }

    /**
     * @return mixed
     */
    public function getResizeHeight()
    {
        return $this->helperData->getConfigGeneral('resize_height');
    }
}

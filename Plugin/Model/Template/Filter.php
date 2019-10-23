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

namespace Mageplaza\LazyLoading\Plugin\Model\Template;

use Magento\Cms\Model\Template\Filter as CmsFilter;
use Mageplaza\LazyLoading\Helper\Data as HelperData;

class Filter
{
    protected $helperData;

    public function __construct(
        HelperData $helperData
    ) {
        $this->helperData = $helperData;
    }

    public function afterFilter(CmsFilter $filter, $result)
    {
        if (!$this->helperData->isEnabled() || !$this->helperData->isLazyLoad()) {
            return $result;
        }

        if ($this->helperData->getLoadingType() === 'icon') {
            $class       = 'mplazyload-icon';
            $placeHolder = $this->helperData->getIcon();
        } else {
            $holderType = $this->helperData->getPlaceholderType();
            $class      = 'mplazyload-' . $this->helperData->getPlaceholderType();
            if ($holderType !== 'transparent') {
                $placeHolder = 'test';
            } else {
                $placeHolder = HelperData::DEFAULT_IMAGE;
            }
        }
        preg_match_all('/<img((.(?!class=))*)\/?>/', $result, $matches);

        $replaced = [];
        $search   = [];
        foreach ($matches[0] as $img) {
            if ($img) {
                $strProcess   = preg_replace('/src="/', 'data-src="', $img);
                $replaceClass = '<img class="mplazyload mplazyload-cms ' . $class . '" src="' . $placeHolder . '"';
                $strProcess   = preg_replace('/<img/', $replaceClass, $strProcess);
                $replaced[]   = $strProcess;
                $search[]     = $img;
            }
        }

        return str_replace($search, $replaced, $result);
    }
}
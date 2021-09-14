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

var config = {
    paths: {
        'mageplaza/lazyloading/lib/lazyload': 'Mageplaza_LazyLoading/js/lib/jquery.lazy.min',
    },
    shim: {
        "mageplaza/lazyloading/lib/lazyload": ["jquery"]
    },
    config: {
		mixins: {
			'Magento_Swatches/js/swatch-renderer': {
				'Mageplaza_LazyLoading/js/swatch-renderer-mixin': true
			}
		}
	}
};

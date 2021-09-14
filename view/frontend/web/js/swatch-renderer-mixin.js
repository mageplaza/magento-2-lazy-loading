/* jslint browser */
/* global config */
define([
    'jquery',
    'jquery-ui-modules/widget'
], function ($) {
    'use strict';

    var mixin = {
        /**
         * Gets all product media and change current to the needed one
         *
         * @private
         */
         _LoadProductMedia: function () {
            var $widget = this,
                $this = $widget.element,
                productData = this._determineProductData(),
                mediaCallData,
                mediaCacheKey,

                /**
                 * Processes product media data
                 *
                 * @param {Object} data
                 * @returns void
                 */
                mediaSuccessCallback = function (data) {
                    if (!(mediaCacheKey in $widget.options.mediaCache)) {
                        $widget.options.mediaCache[mediaCacheKey] = data;
                    }
                    $widget._ProductMediaCallback($this, data, productData.isInProductView);
                    setTimeout(function () {
                        $widget._DisableProductMediaLoader($this);
                    }, 300);
                };

            if (!$widget.options.mediaCallback) {
                return;
            }

            mediaCallData = {
                'product_id': this.getProduct()
            };

            mediaCacheKey = JSON.stringify(mediaCallData);

            if (mediaCacheKey in $widget.options.mediaCache) {
                $widget._XhrKiller();
                if (!$($widget.productForm.context).parents('.product-item-info').find('img[class*=mplazyload-]').length) {
                    $widget._EnableProductMediaLoader($this);
                }
                mediaSuccessCallback($widget.options.mediaCache[mediaCacheKey]);
            } else {
                mediaCallData.isAjax = true;
                $widget._XhrKiller();
                if (!$($widget.productForm.context).parents('.product-item-info').find('img[class*=mplazyload-]').length) {
                    $widget._EnableProductMediaLoader($this);
                }
                $widget.xhr = $.ajax({
                    url: $widget.options.mediaCallback,
                    cache: true,
                    type: 'GET',
                    dataType: 'json',
                    data: mediaCallData,
                    success: mediaSuccessCallback
                }).done(function () {
                    $widget._XhrKiller();
                });
            }
        },

        /**
         * Update [gallery-placeholder] or [product-image-photo]
         * @param {Array} images
         * @param {jQuery} context
         * @param {Boolean} isInProductView
         */
         updateBaseImage: function (images, context, isInProductView) {
            var justAnImage = images[0],
                initialImages = this.options.mediaGalleryInitial,
                imagesToUpdate,
                gallery = context.find(this.options.mediaGallerySelector).data('gallery'),
                isInitial;

            if (isInProductView) {
                if (_.isUndefined(gallery)) {
                    context.find(this.options.mediaGallerySelector).on('gallery:loaded', function () {
                        this.updateBaseImage(images, context, isInProductView);
                    }.bind(this));

                    return;
                }

                imagesToUpdate = images.length ? this._setImageType($.extend(true, [], images)) : [];
                isInitial = _.isEqual(imagesToUpdate, initialImages);

                if (this.options.gallerySwitchStrategy === 'prepend' && !isInitial) {
                    imagesToUpdate = imagesToUpdate.concat(initialImages);
                }

                imagesToUpdate = this._setImageIndex(imagesToUpdate);

                gallery.updateData(imagesToUpdate);
                this._addFotoramaVideoEvents(isInitial);
            } else if (justAnImage && justAnImage.img) {
                if (context.find('img[class*=mplazyload-]').length) {
                    context.find('.product-image-photo').attr('data-src', justAnImage.img);
                } else {
                    context.find('.product-image-photo').attr('src', justAnImage.img);
                }
            }
        }
    };

    return function (swatchRenderer) {
        $.widget('mage.SwatchRenderer', swatchRenderer, mixin);
        return $.mage.SwatchRenderer;
    };
});

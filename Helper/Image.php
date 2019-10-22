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

namespace Mageplaza\LazyLoading\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\Core\Helper\Media;

/**
 * Class Image
 * @package Mageplaza\LazyLoading\Helper
 */
class Image extends Media
{
    const TEMPLATE_MEDIA_PATH = 'mageplaza/lazyloading';

    /**
     * @param $file
     * @param $width
     * @param string $height
     *
     * @return string
     * @throws NoSuchEntityException
     */
//    public function resizeIcon($file, $width, $height)
//    {
//        $image = $this->getMediaPath($file);
//        $resizeImage = $this->getMediaPath($file, 'resized/' . $width . 'x' . $height);
//        $mediaDirectory = $this->getMediaDirectory();
//
//        if ($mediaDirectory->isFile($resizeImage)) {
//            $image = $resizeImage;
//        } else {
//            $imageResize = $this->imageFactory->create();
//            $imageResize->open($mediaDirectory->getAbsolutePath($image));
//            $imageResize->constrainOnly(true);
//            $imageResize->keepTransparency(true);
//            $imageResize->keepFrame(false);
//            $imageResize->keepAspectRatio(false);
//            $imageResize->resize($width, $height);
//
//            try {
//                $imageResize->save($mediaDirectory->getAbsolutePath($resizeImage));
//
//                $image = $resizeImage;
//            } catch (Exception $e) {
//                $this->_logger->critical($e->getMessage());
//            }
//        }
//
//        return $this->getMediaUrl($image);
//    }
}

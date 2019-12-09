# Magento 2 Lazy Loading extension

Lazy Loading module allows speeding up the site load by showing the image only at the demanding time. Lazy Loading improves site performance and reduces bounce rate due to slow loading.

## 1. Documentation

- [Installation guide](https://www.mageplaza.com/install-magento-2-extension/)
- [User guide](https://docs.mageplaza.com/lazy-loading/index.html)
- [Introduction page](http://www.mageplaza.com/magento-2-lazy-loading/)
- [Contribute on Github](http://www.mageplaza.com/magento-2-lazy-loading/)
- [Get Support](https://github.com/mageplaza/magento-2-lazy-loading/issues)


## 2. FAQ

**Q: I got an error: Mageplaza_Core has been already defined**

A: Read solution [here](https://github.com/mageplaza/module-core/issues/3)

**Q: Which pages to apply Lazy Loading?**

A: Lazy Loading can be applied for images on many popular pages including Category Page, Product Detail Page, CMS Page, Checkout Page, Search Page, or Related/Cross-sell/  Up-sell product blocks.

**Q: How many types are there in loading transaction?**

A: Loading transaction has two main types: loading icon and lightweight placeholders. Loading icon enables you to upload any image to make the loading icon (recommended size 64x64px). And, you can select 3 types with placeholder which are transparent, blurred, and low-resolution. 

**Q: What does the loading threshold mean?**

A: Threshold is used to make the images load easier. You can use the threshold parameter (px). For example, if the threshold is 200px, the image load appears within 200px far from the viewport.

## 3. How to install Lazy Loading extension for Magento 2

Install via composer (recommend). Run the following command in Magento 2 root folder:

```
composer require mageplaza/module-lazy-loading
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## 4. Highlight Features
 

### Load images by demand

#### Hold up loading out-of-view images 

This feature makes the images loaded only in the parts which users are viewing in the entire page. Other images will not load until the users scroll to the next part of the page. Similarly, images will load as users are viewing. 

#### Optimize page loading 

Lazy Loading significantly speeds up the page loading, especially for long websites. You no longer have to wait for a long time to open a page as all images of the page will not load at the same time. This function makes it easier for visitors to view your page. Therefore, it reduces the bounce rate and increases conversions. 
 
![](https://i.imgur.com/LvBqqmt.gif)

### Applicable to the most popular pages

It is flexible and useful to apply Lazy Loading to the most used pages any online store site, which usually include almost images of products.

Some mostly-used pages are: 
- Category Page
- Product Page
- CMS Page
- Search Page
- Related, cross-sell, up-sell blocks


![](https://i.imgur.com/DEHgOO3.gif)


### Multiple Lazy Loading effects

The extension provides the store admin with several loading effects that can be adapted to the pages easily.  

#### Placeholders
- Transparent: the most effective one to enhance Lighthouse results. 
- Blur: this effect makes the transaction load smoothly.
- Low resolution: Blurred or pixelated loading 
#### Loading icon 
- Support any loading icon for image loading.
- Flexibly use your favourite icons such as gif, jpg, svg, and png.

![](https://i.imgur.com/DEHgOO3.gif)

### Adjust loading point time

In the Magento Default, once the user opens a page, images of the page will load immediately. With Lazy Loading, you can make specific images to load earlier by using the threshold parameter. 

For instance, if you set the threshold value is 200px, then the image load that appears within 200px will be far from the user’s visible area. 

This feature keeps the constancy of the image appearance as well as saves the bandwidth. 

![](https://i.imgur.com/eoahuH4.png)

### Remove Lazy Loading anytime

If you don’t want to use Lazy Loading anymore, you can exclude it in different ways:
 
- Exclude page with URL: Lazy Loading will not affect images of the Page(s) with the exclude URL(s). For example: /gear.html

- Exclude CSS class: Lazy Loading will not be applied for Images with excluding CSS class. For example: <img class="downloadable-product" src="lifelong.jpg"

- Exclude Text: The loading status of the images which have title or name with the exclude text will not be applied with Lazy Loading. For example: <img title = "lifelong" src = "download.jpg">

 ![](https://i.imgur.com/x6UyBcL.png)



## 5. Full Features List 

### For store admins

- Turn on/ Turn off the module 
- Choose pages to apply Lazy Loading: Category Page, Product Detail Page, CMS Page, Checkout Page, Search Page, Related/Cross-sell/  Up-sell product block.
- Remove Lazy Loading application via exclude URL, exclude CSS class, Exclude text in image title/ name 
- Set loading time by the loading threshold 
- Pick up the loading type: icon or placeholder 
- Upload and resize loading icon
- Choose the placeholder type: Transparent, Blurred, or Low resolution 

### For customers 

- Enjoy a page with a fast loading speed 
- Save time and have an excellent experience 

## 6. User Guide


### 6.1 How to use

#### Process Lazy Load with the icon

![](https://i.imgur.com/6pnOIvc.png)

#### Process Lazy Loading with placeholder

![](https://i.imgur.com/Gcj9Bp3.png)

### 6.2 How to configure

Login to the Magento Admin, go to `Stores > Settings > Configuration > Mageplaza Extensions > Lazy Loading`.


![](https://i.imgur.com/ENQJs3I.gif)

#### General configuration

![](https://i.imgur.com/0s7h7kA.png)

- **Enable = Yes/No**: To enable/disable the extension feature.
- **Apply For**: Select pages to apply lazy loading. You can select one or multiple pages at the same time. 

![](https://i.imgur.com/x6BuaNK.png)

- **Exclude Pages With URL Including**: Click **Add** button to enter the path of the page that you do not want to apply lazy loading. Pages with URLs entered in this field will not apply lazy loading. Click **Delete** icon to delete the path you have just entered.
- *Example*:

![](https://i.imgur.com/gc7Er3V.png)

- **Exclude Css Class**: Click **Add** button to enter the class name containing the image that you do not want to apply Lazy Loading. Click **Delete** icon to delete the class name just entered.
- *Example*:

![](https://i.imgur.com/uAEyJ7D.png)

`<img class = "downloadable-product" src = "lifelong.jpg">`. Images of the "downloadable-product" class will not apply lazy loading.

- **Exclude Text**: Click the **Add** button to enter the title or alt of the Image tag. Images with the tags that contain the text entered do not apply lazy loading.
- *Example*:

![](https://i.imgur.com/3cfqi4O.png)

`<img title = "lifelong" src = "download.jpg">` or `<img name = "lifelong" src = "product.jpg">`. When filling in "lifelong", this image will not apply lazy loading.
- **Loading Threshold**: Set the distance from the screen to the product image to process lazy loading. Products in the threshold range will still load without scrolling.
- **Loading Type**: Select the lazy loading processing effect.
![](https://i.imgur.com/v9SthIM.png)

  - **Icon**: Process lazy loading with Icon. Show more fields:
  ![](https://i.imgur.com/Q851Wsr.png)
  
    - **Upload Icon**: Click the **Choose File** button to select the image you want to display during lazy loading processing. If left blank, the default icon will be displayed. Click **Delete Image** button to delete the selected image.
    - **NOTE**: Only support files in the format: gif, png, jpg, svg.
    - **Resize Icon Width**: Enter the width of the image displayed when processing lazy loading. The default value is 64px.
    - **Resize Icon Height**: Enter the height of the displayed image when processing lazy loading. The default value is 64px.
    
  - **Placeholder**: Processing the lazy loading with the placeholder. Show additional **Placeholder Type** field with the following options:
    ![](https://i.imgur.com/ZmRJM79.png)

    - **Blurred**: Product image is blurred during processing of lazy loading.
    ![](https://i.imgur.com/MV5aHpP.gif)

    - **Low Resolution**: Product image quality is reduced during lazy loading processing.
    ![](https://i.imgur.com/Ighh5wH.gif)

    - **Transparent**: Display transparent images during lazy loading processing.
    ![](https://i.imgur.com/7QFFTBf.gif)




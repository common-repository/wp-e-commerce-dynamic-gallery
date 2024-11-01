<style>
<?php
global $wpsc_dgallery_fonts_face;
global $wpsc_dgallery_global_settings, $wpsc_dgallery_style_setting, $wpsc_dgallery_thumbnail_settings;
$width_type                 = $wpsc_dgallery_style_setting['width_type'];
if ($width_type == 'px') {
    $g_width                = $wpsc_dgallery_style_setting['product_gallery_width_fixed'].'px';
 } else {
    $g_width                = $wpsc_dgallery_style_setting['product_gallery_width_responsive'].'%';
 }

$g_thumb_width              = $wpsc_dgallery_thumbnail_settings['thumb_width'];
if ( $g_thumb_width <= 0 ) {
    $g_thumb_width          = 105;
}
$g_thumb_height             = $wpsc_dgallery_thumbnail_settings['thumb_height'];
if ( $g_thumb_height <= 0 ) {
    $g_thumb_height         = 75;
}
$g_thumb_spacing            = $wpsc_dgallery_thumbnail_settings['thumb_spacing'];

$navbar_font                =  $wpsc_dgallery_style_setting['navbar_font'];
$bg_nav_color               = $wpsc_dgallery_style_setting['bg_nav_color'];
$navbar_height              = $wpsc_dgallery_style_setting['navbar_height'];

$bg_image_wrapper           = $wpsc_dgallery_style_setting['bg_image_wrapper'];
$border_image_wrapper_color = $wpsc_dgallery_style_setting['border_image_wrapper_color'];
$popup_gallery              = $wpsc_dgallery_global_settings['popup_gallery'];

$product_gallery_bg_des     = $wpsc_dgallery_style_setting['product_gallery_bg_des'];
$des_background             = str_replace('#','',$product_gallery_bg_des);
$bg_des                     = WPSC_Dynamic_Gallery_Functions::html2rgb($product_gallery_bg_des,true);

if ( $wpsc_dgallery_thumbnail_settings['enable_gallery_thumb'] == 'yes' ) {
    $enable_gallery_thumb   = $wpsc_dgallery_thumbnail_settings['enable_gallery_thumb'];
} else {
    $enable_gallery_thumb   = 'no';
}

if( $wpsc_dgallery_style_setting['product_gallery_nav'] == 'yes') {
    $product_gallery_nav    = $wpsc_dgallery_style_setting['product_gallery_nav'];
} else {
    $product_gallery_nav    = 'no';
}

$caption_font               = $wpsc_dgallery_style_setting['caption_font'];
?>
.single_product_display .imagecol { 
    max-width:100%; 
    width: <?php
echo $g_width; ?>;  
}
.single_product_display .imagecol > a { display: none !important;position: absolute;z-index: -1;left:-1000em;}
#TB_window{width:auto !important;}
.ad-gallery {
        width: 100%;
        position:relative;
}
.ad-gallery .ad-image-wrapper {
    background: none repeat scroll 0 0 <?php
echo $bg_image_wrapper; ?>;
    border: 1px solid <?php
echo $border_image_wrapper_color; ?> !important;
    /*width: 99.3%;*/
    margin: 0px;
    position: relative;
    overflow: hidden !important;
    padding: 0;
    z-index: 8 !important;
}
@media only screen and (min-width : 768px)  and (max-width : 769px)  {
    .ad-gallery .ad-image-wrapper {
         /*width: 98.3%;*/
    }
}
@media only screen and (max-width : 321px) {
    .ad-gallery .ad-image-wrapper {
         /*width: 98.3%;*/
    }
}
.ad-gallery .ad-image-wrapper .ad-image {
    width: 100% !important;
    text-align: center;
}
.ad-gallery .ad-thumbs li {
    padding-right: <?php
echo $g_thumb_spacing; ?>px !important;
}
.ad-gallery .ad-thumbs li.last_item {
    padding-right: <?php
echo ($g_thumb_spacing + 13); ?>px !important;
}
.ad-gallery .ad-thumbs li div {
    height: <?php
echo $g_thumb_height; ?>px !important;
    width: <?php
echo $g_thumb_width; ?>px !important;
}
.ad-gallery .ad-thumbs li a {
    width: <?php
echo $g_thumb_width; ?>px !important;
    height: <?php
echo $g_thumb_height; ?>px !important;
}
* html .ad-gallery .ad-forward, .ad-gallery .ad-back {
    height: <?php
echo $g_thumb_height; ?>px !important;
}
/*Gallery*/

.ad-image-wrapper {
    overflow: inherit !important;
}
.ad-gallery .ad-controls {
    background: <?php
echo $bg_nav_color; ?> !important;
    border:1px solid <?php
echo $bg_nav_color; ?>;
    color: #FFFFFF;
    font-size: 12px;
    height: 22px;
    margin-top: 20px !important;
    padding: 8px 2% !important;
    position: relative;
    width: 95.8%;
    -khtml-border-radius:5px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    display: none;
}
.ad-gallery .ad-info {
    float: right;
    font-size: 14px;
    position: relative;
    right: 8px;
    text-shadow: 1px 1px 1px #000000 !important;
    top: 1px !important;
}
.ad-gallery .ad-nav .ad-thumbs {
    margin: 7px 4% 0 !important;
}
.ad-gallery .ad-thumbs .ad-thumb-list {
    margin-top: 0px !important;
}
.ad-thumb-list li {
    background: none !important;
    padding-bottom: 0 !important;
    padding-left: 0 !important;
    padding-top: 0 !important;
}
.ad-image-wrapper .ad-image-description {
    background: rgba(<?php
echo $bg_des; ?>, 0.5) !important;
    filter:progid:DXImageTransform.Microsoft.Gradient(GradientType=1, StartColorStr="#88<?php echo $des_background; ?>", EndColorStr="#88<?php echo $des_background; ?>");
    left: 0;
    line-height: 1.4em;
    padding: 2% 2% 2% !important;
    position: absolute;
    text-align: left;
    width: 96.1% !important;
    z-index: 10;
    font-weight: normal;
    <?php
echo $wpsc_dgallery_fonts_face->generate_font_css($caption_font); ?>;
}
.product_gallery .slide-ctrl, .product_gallery .icon_zoom {
    height: <?php
echo ($navbar_height - 16); ?>px !important;
    line-height: <?php
echo ($navbar_height - 16); ?>px !important;
    <?php
echo $wpsc_dgallery_fonts_face->generate_font_css($navbar_font); ?>
}

.product_gallery .icon_zoom {
    background: <?php
echo $bg_nav_color; ?>;
    border-right: 1px solid <?php
echo $bg_nav_color; ?>;
    border-top: 1px solid <?php
echo $border_image_wrapper_color; ?>;
}
.product_gallery .slide-ctrl {
    background: <?php
echo $bg_nav_color; ?>;
    border-left: 1px solid <?php
echo $border_image_wrapper_color; ?>;
    border-top: 1px solid <?php
echo $border_image_wrapper_color; ?>;
}
.product_gallery .slide-ctrl .ad-slideshow-stop-slide, .product_gallery .slide-ctrl .ad-slideshow-start-slide, .product_gallery .icon_zoom {
    line-height: <?php
echo ($navbar_height - 16); ?>px !important;
    <?php
echo $wpsc_dgallery_fonts_face->generate_font_css($navbar_font); ?>
}
.product_gallery .ad-gallery .ad-thumbs li a {
    border: 1px solid <?php
echo $border_image_wrapper_color; ?> !important;
}
.ad-gallery .ad-thumbs li a.ad-active {
    border: 1px solid <?php
echo $bg_nav_color; ?> !important;
}
<?php
if ($enable_gallery_thumb == 'no') { ?>
.ad-nav{display:none; height:1px;}
.woocommerce .images { margin-bottom: 15px;}
<?php
} ?>
<?php
if ($product_gallery_nav == 'no') { ?>
.ad-image-wrapper:hover .slide-ctrl {
        display: block !important;
 }
 .product_gallery .slide-ctrl {
   background: none repeat scroll 0 0 transparent;
   border: medium none;
   height: 50px !important;
   left: 41.5% !important;
   top: 38% !important;
   width: 50px !important;
}
.product_gallery .slide-ctrl .ad-slideshow-start-slide {background: url(<?php
    echo WPSC_DYNAMIC_GALLERY_JS_URL; ?>/mygallery/play.png) !important;height: 50px !important;text-indent: -999em !important; width: 50px !important;}
.product_gallery .slide-ctrl .ad-slideshow-stop-slide {background: url(<?php
    echo WPSC_DYNAMIC_GALLERY_JS_URL; ?>/mygallery/pause.png) !important;height: 50px !important;text-indent: -999em !important; width: 50px !important;}
<?php
} ?>
<?php
if ($popup_gallery == 'deactivate') { ?>
.ad-image-wrapper .ad-image img{cursor: default !important;}
.icon_zoom{cursor: default !important;}
<?php
} ?>
</style>
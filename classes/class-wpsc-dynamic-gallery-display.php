<?php
/**
 * WP e-Commerce Dynamic Gallery Display Class
 *
 * Class Function into WP e-Commerce plugin
 *
 * Table Of Contents
 *
 * wpsc_dynamic_gallery_display()
 * get_gallery_variations()
 */
class WPSC_Dynamic_Gallery_Display_Class
{
	public static function wpsc_dynamic_gallery_display( $product_id = 0, $get_again=false ) {
		/**
		 * Single Product Image
		 */
		global $post;
		global $wpsc_dgallery_global_settings, $wpsc_dgallery_style_setting, $wpsc_dgallery_thumbnail_settings;

		$global_stop_scroll_1image = $wpsc_dgallery_style_setting['stop_scroll_1image'];
		$enable_scroll = 'true';

		if ($product_id <= 0) {
			$product_id = $post->ID;
		}
		$ogrinal_product_id = $product_id;

		// Get all attached images to this product

		$featured_img = get_post_meta( $product_id, '_thumbnail_id' );
		$attached_images = (array) get_posts( array(
			'post_type'   => 'attachment',
			'post_mime_type' => 'image',
			'numberposts' => -1,
			'post_status' => null,
			'post_parent' => $product_id ,
			'orderby'     => 'menu_order',
			'order'       => 'ASC',
		) );
		$attached_thumb = array();
		$all_image_thumb = array();
		if ( count($attached_images) > 0 ) {
			$i = 0;
			foreach ( $attached_images as $key => $object ) {
				$i++;
				if ( in_array( $object->ID, $featured_img ) ) {
					$attached_thumb[0] = $object;
				} else {
					$attached_thumb[$i] = $object;
				}
				$chosen = get_post_meta( $object->ID, '_wpsc_dgallery_in_variations', true );

				if ( $chosen || get_post_meta( $object->ID, '_wpsc_exclude_image', true ) != 1) {
					$all_image_thumb[$i] = $object;
				}
			}
		}
		ksort($attached_thumb);
		$product_id .= '_'.rand(100,10000);
		$have_image = false;
		$lightbox_class = '';
		$attached_images = array();

		$max_height = 0;
		$width_of_max_height = 0;
		if ( is_array($attached_thumb) && count( $attached_thumb ) > 0 ) {
			foreach ( $attached_thumb as $item_thumb ) {
				if ( get_post_meta( $item_thumb->ID, '_wpsc_exclude_image', true ) == 1 ) continue;

				$image_attribute = wp_get_attachment_image_src( $item_thumb->ID, 'full');
				$image_lager_default_url = $image_attribute[0];
				$height_current = $image_attribute[2];
				if ( $height_current > $max_height ) {
					$max_height = $height_current;
					$width_of_max_height = $image_attribute[1];
				}

				$item_thumb->image_lager_default_url = $image_lager_default_url;
				$attached_images[] = $item_thumb;
				$have_image = true;
			}
		}

		if ($have_image) $lightbox_class = 'lightbox';

		//Gallery settings
		$width_type = $wpsc_dgallery_style_setting['width_type'];
		if ( $width_type == 'px' ) {
			$g_width = $wpsc_dgallery_style_setting['product_gallery_width_fixed'].'px';
			$g_height = $wpsc_dgallery_style_setting['product_gallery_height'];
		} else {
			$g_width = $wpsc_dgallery_style_setting['product_gallery_width_responsive'].'%';
			if ( $max_height > 0 ) {
				$g_height = false;
			?>
            <script type="text/javascript">
			(function($){
				$(function(){
					a3revWPSCDynamicGallery_<?php echo $product_id; ?> = {

						setHeightProportional: function () {
							var image_wrapper_width = parseInt( $( '#gallery_<?php echo $product_id; ?>' ).find('.ad-image-wrapper').width() );
							var width_of_max_height = parseInt(<?php echo $width_of_max_height; ?>);
							var image_wrapper_height = parseInt(<?php echo $max_height; ?>);
							//alert(width_of_max_height);
							//alert(image_wrapper_width);
							//alert(image_wrapper_height);
							if( width_of_max_height > image_wrapper_width ) {
								var ratio = width_of_max_height / image_wrapper_width;
								image_wrapper_height = parseInt(<?php echo $max_height; ?>) / ratio;
								//alert(ratio);
								//alert(image_wrapper_height);
							}
							$( '#gallery_<?php echo $product_id; ?>' ).find('.ad-image-wrapper').css({ height: image_wrapper_height });
						}
					}

					a3revWPSCDynamicGallery_<?php echo $product_id; ?>.setHeightProportional();

					$( window ).resize(function() {
						a3revWPSCDynamicGallery_<?php echo $product_id; ?>.setHeightProportional();
					});

				});
	  		})(jQuery);
			</script>
            <?php
			} else {
				$g_height = 138;
			}
		}


		$g_auto = $wpsc_dgallery_style_setting['product_gallery_auto_start'];
        $g_speed = $wpsc_dgallery_style_setting['product_gallery_speed'];
        $g_effect = $wpsc_dgallery_style_setting['product_gallery_effect'];
        $g_animation_speed = $wpsc_dgallery_style_setting['product_gallery_animation_speed'];
		$popup_gallery = $wpsc_dgallery_global_settings['popup_gallery'];

		//Nav bar settings
		if( $wpsc_dgallery_style_setting['product_gallery_nav'] == 'yes') {
			$product_gallery_nav = $wpsc_dgallery_style_setting['product_gallery_nav'];
		} else {
			$product_gallery_nav = 'no';
		}
		$navbar_height = $wpsc_dgallery_style_setting['navbar_height'];
		if ( $product_gallery_nav == 'yes' ) {
			$display_ctrl = 'display:block !important;';
			$mg = $navbar_height;
			$ldm = $navbar_height;
		} else {
			$display_ctrl = 'display:none !important;';
			$mg = '0';
			$ldm = '0';
		}

		//Lazy-load scroll settings
		$transition_scroll_bar = $wpsc_dgallery_style_setting['transition_scroll_bar'];
		$lazy_load_scroll = $wpsc_dgallery_style_setting['lazy_load_scroll'];

        $g_thumb_width = $wpsc_dgallery_thumbnail_settings['thumb_width'];
		if ( $g_thumb_width <= 0 ) $g_thumb_width = 105;
        $g_thumb_height = $wpsc_dgallery_thumbnail_settings['thumb_height'];
		if ( $g_thumb_height <= 0 ) $g_thumb_height = 75;

		$hide_thumb_1image = $wpsc_dgallery_thumbnail_settings['hide_thumb_1image'];

		$start_label = __('START SLIDESHOW', 'wp-e-commerce-dynamic-gallery' );
		$stop_label = __('STOP SLIDESHOW', 'wp-e-commerce-dynamic-gallery' );
		if($global_stop_scroll_1image == 'yes' && count($attached_images) <= 1) {
			$enable_scroll = 'false';
			$start_label = '';
			$stop_label = '';
		}

		$zoom_label = __('ZOOM +', 'wp-e-commerce-dynamic-gallery' );
		if ($popup_gallery == 'deactivate') {
			$zoom_label = '';
			$lightbox_class = '';
		}

		if ($lightbox_class == '' && $enable_scroll == 'false') {
			$display_ctrl = 'display:none !important;';
			$mg = '0';
			$ldm = '0';
		}

		$html = '';
		if (!$get_again) {
			$_upload_dir = wp_upload_dir();
			if ( file_exists( $_upload_dir['basedir'] . '/sass/wpsc_dynamic_gallery_pro.min.css' ) ) {
				$html .= '<link media="screen" type="text/css" href="' . str_replace(array('http:','https:'), '', $_upload_dir['baseurl'] ) . '/sass/wpsc_dynamic_gallery_pro.min.css" rel="stylesheet" />' . "\n";
			} else {
				ob_start();
				include( WPSC_DYNAMIC_GALLERY_DIR . '/templates/customized_style.php' );
				$html .= ob_get_clean();
			}
        	$html .= '<div class="images gallery_container" style="'. ( ( $width_type == 'px' ) ? 'width:'.$g_width.';' : 'width:100%;' ) .'">';
		}
			$html .= '<div class="product_gallery">';

            $html .=  '<style>
                .ad-gallery .ad-image-wrapper {
				'
					. ( ( $g_height != false ) ? 'height: '.($g_height-2).'px;' : '' ) .
                    '
                }
				#gallery_'.$product_id.' .ad-image-wrapper .ad-image-description {
					margin: 0 0 '.$mg.'px !important;
				}
				.product_gallery #gallery_'.$product_id.' .ad-image-wrapper {
					padding-bottom:'.$mg.'px;
				}
				.product_gallery #gallery_'.$product_id.' .slide-ctrl, .product_gallery #gallery_'.$product_id.' .icon_zoom {
					'.$display_ctrl.';
				}';
				if ($lazy_load_scroll == 'yes') {
					$html .=  '#gallery_'.$product_id.' .lazy-load{
						background:'.$transition_scroll_bar.' !important;'
						 . ( ( $g_height != false ) ? 'top:'.($g_height + 9).'px !important;' : '' ) .
						'opacity:1 !important;
						margin-top:'.$ldm.'px !important;
					}';
				} else {
					$html .=  '.ad-gallery .lazy-load{display:none!important;}';
				}
			if ($hide_thumb_1image == 'yes' && count($attached_images) <= 1) {
				$html .= '#gallery_'.$product_id.' .ad-nav{visibility:hidden !important; height: 0 !important;} .woocommerce #gallery_'.$product_id.' .images { margin-bottom: 15px;}';
			}

			if ($global_stop_scroll_1image == 'yes' && count($attached_images) <= 1) $html .= '#gallery_'.$product_id.' .slide-ctrl{cursor: default;}';

			$html .=  '
            </style>';

            $html .=  '<script type="text/javascript">
                jQuery(function() {
                    var settings_defaults_'.$product_id.' = { loader_image: "'.WPSC_DYNAMIC_GALLERY_JS_URL.'/mygallery/loader.gif",
                        start_at_index: 0,
                        gallery_ID: "'.$product_id.'",
						lightbox_class: "'.$lightbox_class.'",
                        description_wrapper: false,
                        thumb_opacity: 0.5,
                        animate_first_image: false,
                        animation_speed: '.$g_animation_speed.'000,
                        width: false,
                        height: false,
                        display_next_and_prev: '.$enable_scroll.',
                        display_back_and_forward: '.$enable_scroll.',
                        scroll_jump: 0,
                        slideshow: {
                            enable: '.$enable_scroll.',
                            autostart: '.$g_auto.',
                            speed: '.$g_speed.'000,
                            start_label: "'.$start_label.'",
                            stop_label: "'.$stop_label.'",
							zoom_label: "'.$zoom_label.'",
                            stop_on_scroll: true,
                            countdown_prefix: "(",
                            countdown_sufix: ")",
                            onStart: false,
                            onStop: false
                        },
                        effect: "'.$g_effect.'",
                        enable_keyboard_move: true,
                        cycle: true,
                        callbacks: {
                        init: false,
                        afterImageVisible: false,
                        beforeImageVisible: false
                    }
                };
                jQuery("#gallery_'.$product_id.'").adGallery(settings_defaults_'.$product_id.');
            });
            </script>';
            $html .=  '<div id="gallery_'.$product_id.'" class="ad-gallery" style="width: 100%;">
                <div class="ad-image-wrapper"></div>
                <div class="ad-controls"> </div>
                  <div class="ad-nav">
                    <div class="ad-thumbs">
                      <ul class="ad-thumb-list">';

                        $script_colorbox = '';
						$script_fancybox = '';
                        if ( is_array($attached_images) && count( $attached_images ) > 0 ){
                            $i = 0;
                            $display = '';

                                $script_colorbox .= '<script type="text/javascript">';
								$script_fancybox .= '<script type="text/javascript">';
                                $script_colorbox .= '(function($){';
								$script_fancybox .= '(function($){';
                                $script_colorbox .= '$(function(){';
								$script_fancybox .= '$(function(){';
                                $script_colorbox .= '$(document).on("click", ".ad-gallery .lightbox", function(ev) { if( $(this).attr("rel") == "gallery_'.$product_id.'") {
									var idx = $("#gallery_'.$product_id.' .ad-image img").attr("idx");';
								$script_fancybox .= '$(document).on("click", ".ad-gallery .lightbox", function(ev) { if( $(this).attr("rel") == "gallery_'.$product_id.'") {
									var idx = $("#gallery_'.$product_id.' .ad-image img").attr("idx");';
                                if ( count($attached_images) <= 1 ) {
									$script_colorbox .= '$(".gallery_product_'.$product_id.'").colorbox({open:true, maxWidth:"100%", title: function() { return "&nbsp;";} });';
									$script_fancybox .= '$.fancybox(';
                                } else {
                                    $script_colorbox .= '$(".gallery_product_'.$product_id.'").colorbox({rel:"gallery_product_'.$product_id.'", maxWidth:"100%", title: function() { return "&nbsp;";} }); $(".gallery_product_'.$product_id.'_"+idx).colorbox({open:true, maxWidth:"100%", title: function() { return "&nbsp;";} });';
									$script_fancybox .= '$.fancybox([';
                                }
                                $common = '';

								$idx = 0;

                                foreach ( $attached_images as $item_thumb ) {
                                    $li_class = '';
                                    if ( $i == 0 ) { $li_class = 'first_item'; } elseif ( $i == count($attached_images)-1 ) { $li_class = 'last_item'; }
                                    $image_lager_default_url = $item_thumb->image_lager_default_url;

									$image_thumb_attribute = wp_get_attachment_image_src( $item_thumb->ID, 'wpsc-dynamic-gallery-thumb');
                                    $image_thumb_default_url = $image_thumb_attribute[0];

                                    $thumb_height = $g_thumb_height;
                                    $thumb_width = $g_thumb_width;
                                    $width_old = $image_thumb_attribute[1];
                                    $height_old = $image_thumb_attribute[2];
									if ($width_old > $g_thumb_width || $height_old > $g_thumb_height) {
                                        if ( $height_old > $g_thumb_height && $g_thumb_height > 0 ) {
                                            $factor = ($height_old / $g_thumb_height);
                                            $thumb_height = $g_thumb_height;
                                            $thumb_width = $width_old / $factor;
                                        }
                                        if ( $thumb_width > $g_thumb_width && $g_thumb_width > 0 ) {
                                            $factor = ($width_old / $g_thumb_width);
                                            $thumb_height = $height_old / $factor;
                                            $thumb_width = $g_thumb_width;
                                        } elseif ( $thumb_width == $g_thumb_width && $width_old > $g_thumb_width && $g_thumb_width > 0 ) {
                                            $factor = ($width_old / $g_thumb_width);
                                            $thumb_height = $height_old / $factor;
                                            $thumb_width = $g_thumb_width;
                                        }
									} else {
										$thumb_height = $height_old;
                                        $thumb_width = $width_old;
									}
									$alt = get_post_meta($item_thumb->ID, '_wp_attachment_image_alt', true);
									$img_description = $item_thumb->post_excerpt;

                                    $html .=  '<li class="'.$li_class.'"><a alt="'.esc_attr( $alt ).'" class="gallery_product_'.$product_id.' gallery_product_'.$product_id.'_'.$idx.'" title="'. esc_attr( $img_description ) .'" rel="gallery_product_'.$product_id.'" href="'.$image_lager_default_url.'"><div><img idx="'.$idx.'" style="width:'.$thumb_width.'px !important;height:'.$thumb_height.'px !important" src="'.$image_thumb_default_url.'" alt="'. esc_attr( $alt ) .'" data-caption="'.esc_attr( $img_description ).'" class="image'.$i.'" width="'.$thumb_width.'" height="'.$thumb_height.'"></div></a></li>';
                                    $img_description = esc_js( $img_description );
                                    if ($img_description != '') {
										$script_fancybox .= $common.'{href:"'.$image_lager_default_url.'",title:"'.$img_description.'"}';
                                    } else {
										$script_fancybox .= $common.'{href:"'.$image_lager_default_url.'",title:""}';
                                    }
                                    $common = ',';
                                    $i++;
									$idx++;
								}

                                if (count($attached_images) <= 1 ) {
									$script_fancybox .= ');';
                                } else {
									$script_fancybox .= '],{
										 \'index\': idx
      								});';
                                }
                                $script_colorbox .= 'ev.preventDefault();';
                                $script_colorbox .= '} });';
								$script_fancybox .= '} });';
                                $script_colorbox .= '});';
								$script_fancybox .= '});';
                                $script_colorbox .= '})(jQuery);';
								$script_fancybox .= '})(jQuery);';
                                $script_colorbox .= '</script>';
								$script_fancybox .= '</script>';

								if (!$have_image) {
									$script_colorbox = '';
									$script_fancybox = '';
									echo '<li style="width:'.$g_thumb_width.'px;height:'.$g_thumb_height.'px;"> <a style="width:'.($g_thumb_width-2).'px !important;height:'.($g_thumb_height - 2).'px !important;overflow:hidden;float:left !important" class="" rel="gallery_product_'.$product_id.'" href="'.WPSC_DYNAMIC_GALLERY_JS_URL . '/mygallery/no-image.png"> <div><img style="width:'.$g_thumb_width.'px;height:'.$g_thumb_height.'px;" src="'.WPSC_DYNAMIC_GALLERY_JS_URL . '/mygallery/no-image.png" class="image" alt=""> </div></a> </li>';
								}
                        } else {
                            $html .=  '<li style="width:'.$g_thumb_width.'px;height:'.$g_thumb_height.'px;"> <a style="width:'.($g_thumb_width-2).'px !important;height:'.($g_thumb_height - 2).'px !important;overflow:hidden;float:left !important" class="" rel="gallery_product_'.$product_id.'" href="'.WPSC_DYNAMIC_GALLERY_JS_URL . '/mygallery/no-image.png"> <div><img style="width:'.$g_thumb_width.'px;height:'.$g_thumb_height.'px;" src="'.WPSC_DYNAMIC_GALLERY_JS_URL . '/mygallery/no-image.png" class="image" alt=""> </div></a> </li>';
                        }
						if ($popup_gallery == 'deactivate') {
							$script_colorbox = '';
							$script_fancybox = '';
						} else if($popup_gallery == 'colorbox') {
                        	$html .=  $script_colorbox;
						} else {
							$html .=  $script_fancybox;
						}
                        $html .=  '</ul>
                        </div>
                      </div>
                    </div>
          		</div>';
		if (!$get_again) {
        $html .= '</div>';
		$html .= '<div style="display:none !important"><img style="width:0px;height:0px;" src="'.WPSC_DYNAMIC_GALLERY_JS_URL.'/mygallery/no-image.png" alt="" />';
				$global_show_variation = $wpsc_dgallery_global_settings['dgallery_show_variation'];
				$show_variation = get_post_meta($ogrinal_product_id, '_wpsc_dgallery_show_variation',true);

				$show_vr = false;

				if ($show_variation == '' && $global_show_variation == 'yes') {
					$show_vr = true;
				} elseif ($show_variation == 1) {
					$show_vr = true;
				}

				if ($show_vr) {

					if ( count($all_image_thumb) > 0) {
						foreach ($all_image_thumb as $image_hide) {
							$image_hide_attribute = wp_get_attachment_image_src( $image_hide->ID, 'full');
							$image_hide_lager_default_url = $image_hide_attribute[0];

							$image_hide_thumb_attribute = wp_get_attachment_image_src( $image_hide->ID, 'wpsc-dynamic-gallery-thumb');
							$image_hide_thumb_default_url = $image_hide_thumb_attribute[0];

							$html .= '<img style="width:0px !important;height:0px !important" src="'.$image_hide_thumb_default_url.'" width="0" height="0" />';
							$html .= '<img style="width:0px !important;height:0px !important" src="'.$image_hide_lager_default_url.'" width="0" height="0" />';
						}
					}
				}
        $html .= '</div>';
		}

		return $html;
	}

	public static function get_gallery_variations( $product_id, $variations ) {
		global $wpsc_dgallery_global_settings, $wpsc_dgallery_style_setting, $wpsc_dgallery_thumbnail_settings;
		$global_stop_scroll_1image = $wpsc_dgallery_style_setting['stop_scroll_1image'];
		$enable_scroll = 'true';

		// Get all attached images to this product

		$attached_images = (array) get_posts( array(
			'post_type'   => 'attachment',
			'post_mime_type' => 'image',
			'numberposts' => -1,
			'post_status' => null,
			'post_parent' => $product_id ,
			'orderby'     => 'menu_order',
			'order'       => 'ASC',
		) );
		$imgs = array();
		$product_id .= '_'.rand(100,10000);

		$i = 0;
		$lightbox_class = '';
		$max_height = 0;
		$width_of_max_height = 0;
		if ( $attached_images ) {
			foreach ( $attached_images as $attached_image ) {
				$chosen = get_post_meta( $attached_image->ID, '_wpsc_dgallery_in_variations', true );

				if ($chosen) {

					$showimg = array(); // Create blank array

					foreach ($variations as $key => $val) {
						if ( isset($chosen[$key]) ) {
							if ( in_array($val, $chosen[$key]) ) {
								$showimg[] = 1;
							} else {
								$showimg[] = 0;
							}
						} else {
							$showimg[] = 0;
						}
					}

					if ( !in_array(0, $showimg) ) { // if the new array contains a false, don't show the img

						$image_attribute = wp_get_attachment_image_src( $attached_image->ID, 'full');
						$image_lager_default_url = $image_attribute[0];
						$height_current = $image_attribute[2];
						if ( $height_current > $max_height ) {
							$max_height = $height_current;
							$width_of_max_height = $image_attribute[1];
						}

						$attached_image->image_lager_default_url = $image_lager_default_url;
						$imgs[$i] = $attached_image;
					}

				}
				$i++;
			}
		}

		if ( count($imgs) > 0 ) {
			$lightbox_class = 'lightbox';
		}

		//Gallery settings
		$width_type = $wpsc_dgallery_style_setting['width_type'];
		if ( $width_type == 'px' ) {
			$g_height = $wpsc_dgallery_style_setting['product_gallery_height'];
		} else {
			if ( $max_height > 0 ) {
				$g_height = false;
			?>
            <script type="text/javascript">
			(function($){
				$(function(){
					a3revWPSCDynamicGallery_<?php echo $product_id; ?> = {

						setHeightProportional: function () {
							var image_wrapper_width = parseInt( $( '#gallery_<?php echo $product_id; ?>' ).find('.ad-image-wrapper').width() );
							var width_of_max_height = parseInt(<?php echo $width_of_max_height; ?>);
							var image_wrapper_height = parseInt(<?php echo $max_height; ?>);
							if( width_of_max_height > image_wrapper_width ) {
								var ratio = width_of_max_height / image_wrapper_width;
								image_wrapper_height = parseInt(<?php echo $max_height; ?>) / ratio;
							}
							$( '#gallery_<?php echo $product_id; ?>' ).find('.ad-image-wrapper').css({ height: image_wrapper_height });
						}
					}

					a3revWPSCDynamicGallery_<?php echo $product_id; ?>.setHeightProportional();

					$( window ).resize(function() {
						a3revWPSCDynamicGallery_<?php echo $product_id; ?>.setHeightProportional();
					});
				});
	  		})(jQuery);
			</script>
            <?php
			} else {
				$g_height = 138;
			}
		}

		$g_auto = $wpsc_dgallery_style_setting['product_gallery_auto_start'];
        $g_speed = $wpsc_dgallery_style_setting['product_gallery_speed'];
        $g_effect = $wpsc_dgallery_style_setting['product_gallery_effect'];
        $g_animation_speed = $wpsc_dgallery_style_setting['product_gallery_animation_speed'];
		$popup_gallery = $wpsc_dgallery_global_settings['popup_gallery'];

		//Nav bar settings
		if ( $wpsc_dgallery_style_setting['product_gallery_nav'] == 'yes') {
			$product_gallery_nav = $wpsc_dgallery_style_setting['product_gallery_nav'];
		} else {
			$product_gallery_nav = 'no';
		}
		$navbar_height = $wpsc_dgallery_style_setting['navbar_height'];
		if ( $product_gallery_nav == 'yes' ) {
			$display_ctrl = 'display:block !important;';
			$mg = $navbar_height;
			$ldm = $navbar_height;
		} else {
			$display_ctrl = 'display:none !important;';
			$mg = '0';
			$ldm = '0';
		}

		//Lazy-load scroll settings
		$transition_scroll_bar = $wpsc_dgallery_style_setting['transition_scroll_bar'];
		$lazy_load_scroll = $wpsc_dgallery_style_setting['lazy_load_scroll'];

        $g_thumb_width = $wpsc_dgallery_thumbnail_settings['thumb_width'];
		if ( $g_thumb_width <= 0 ) $g_thumb_width = 105;
        $g_thumb_height = $wpsc_dgallery_thumbnail_settings['thumb_height'];
		if ( $g_thumb_height <= 0 ) $g_thumb_height = 75;

		$hide_thumb_1image = $wpsc_dgallery_thumbnail_settings['hide_thumb_1image'];

		$start_label = __('START SLIDESHOW', 'wp-e-commerce-dynamic-gallery' );
		$stop_label = __('STOP SLIDESHOW', 'wp-e-commerce-dynamic-gallery' );
		if ( $global_stop_scroll_1image == 'yes' && count($imgs) <= 1 ) {
			$enable_scroll = 'false';
			$start_label = '';
			$stop_label = '';
		}

		$zoom_label = __('ZOOM +', 'wp-e-commerce-dynamic-gallery' );
		if ( $popup_gallery == 'deactivate' ) {
			$zoom_label = '';
			$lightbox_class = '';
		}

		if ( $lightbox_class == '' && $enable_scroll == 'false' ) {
			$display_ctrl = 'display:none !important;';
			$mg = '0';
			$ldm = '0';
		}

		$html = '';
        $html .= '<div class="product_gallery">';

            $html .=  '<style>
                .ad-gallery .ad-image-wrapper {
                '
					. ( ( $g_height != false ) ? 'height: '.($g_height-2).'px;' : '' ) .
                    '
                }
				#gallery_'.$product_id.' .ad-image-wrapper .ad-image-description {
					margin: 0 0 '.$mg.'px !important;
				}
				.product_gallery #gallery_'.$product_id.' .ad-image-wrapper {
					padding-bottom:'.$mg.'px;
				}
				.product_gallery #gallery_'.$product_id.' .slide-ctrl, .product_gallery #gallery_'.$product_id.' .icon_zoom {
					'.$display_ctrl.';
				}';
				if ( $lazy_load_scroll == 'yes' ) {
					$html .=  '#gallery_'.$product_id.' .lazy-load{
						background:'.$transition_scroll_bar.' !important;'
						 . ( ( $g_height != false ) ? 'top:'.($g_height + 9).'px !important;' : '' ) .
						'opacity:1 !important;
						margin-top:'.$ldm.'px !important;
					}';
				} else {
					$html .=  '.ad-gallery .lazy-load{display:none!important;}';
				}
			if ( $hide_thumb_1image == 'yes' && count($imgs) <= 1 ) {
				$html .= '#gallery_'.$product_id.' .ad-nav{display:none !important;}.woocommerce #gallery_'.$product_id.' .images { margin-bottom: 15px;}';
			}

			if ( $global_stop_scroll_1image == 'yes' && count($imgs) <= 1 ) $html .=  '#gallery_'.$product_id.' .slide-ctrl{cursor: default;}';

			$html .=  '
            </style>';

            $html .=  '<script type="text/javascript">
                jQuery(function() {
                    var settings_defaults_'.$product_id.' = { loader_image: "'.WPSC_DYNAMIC_GALLERY_JS_URL.'/mygallery/loader.gif",
                        start_at_index: 0,
                        gallery_ID: "'.$product_id.'",
						lightbox_class: "'.$lightbox_class.'",
                        description_wrapper: false,
                        thumb_opacity: 0.5,
                        animate_first_image: false,
                        animation_speed: '.$g_animation_speed.'000,
                        width: false,
                        height: false,
                        display_next_and_prev: '.$enable_scroll.',
                        display_back_and_forward: '.$enable_scroll.',
                        scroll_jump: 0,
                        slideshow: {
                            enable: '.$enable_scroll.',
                            autostart: '.$g_auto.',
                            speed: '.$g_speed.'000,
                            start_label: "'.$start_label.'",
                            stop_label: "'.$stop_label.'",
							zoom_label: "'.$zoom_label.'",
                            stop_on_scroll: true,
                            countdown_prefix: "(",
                            countdown_sufix: ")",
                            onStart: false,
                            onStop: false
                        },
                        effect: "'.$g_effect.'",
                        enable_keyboard_move: true,
                        cycle: true,
                        callbacks: {
                        init: false,
                        afterImageVisible: false,
                        beforeImageVisible: false
                    }
                };
                jQuery("#gallery_'.$product_id.'").adGallery(settings_defaults_'.$product_id.');
            });
            </script>';
            $html .=  '<div id="gallery_'.$product_id.'" class="ad-gallery" style="width: 100%;">
                <div class="ad-image-wrapper"></div>
                <div class="ad-controls"> </div>
                  <div class="ad-nav">
                    <div class="ad-thumbs">
                      <ul class="ad-thumb-list">';

                        $script_colorbox = '';
						$script_fancybox = '';
                        if ( is_array($imgs) && count( $imgs ) > 0 ){
                            $i = 0;

                                $script_colorbox .= '<script type="text/javascript">';
								$script_fancybox .= '<script type="text/javascript">';
                                $script_colorbox .= '(function($){';
								$script_fancybox .= '(function($){';
                                $script_colorbox .= '$(function(){';
								$script_fancybox .= '$(function(){';
                                $script_colorbox .= '$(document).on("click", ".ad-gallery .lightbox", function(ev) { if( $(this).attr("rel") == "gallery_'.$product_id.'") {
									var idx = $("#gallery_'.$product_id.' .ad-image img").attr("idx");';
								$script_fancybox .= '$(document).on("click", ".ad-gallery .lightbox", function(ev) { if( $(this).attr("rel") == "gallery_'.$product_id.'") {
									var idx = $("#gallery_'.$product_id.' .ad-image img").attr("idx");';
                                if ( count($imgs) <= 1 ) {
                                    $script_colorbox .= '$(".gallery_product_'.$product_id.'").colorbox({open:true, maxWidth:"100%", title: function() { return "&nbsp;";} });';
									$script_fancybox .= '$.fancybox(';
                                } else {
                                    $script_colorbox .= '$(".gallery_product_'.$product_id.'").colorbox({rel:"gallery_product_'.$product_id.'", maxWidth:"100%", title: function() { return "&nbsp;";} }); $(".gallery_product_'.$product_id.'_"+idx).colorbox({open:true, maxWidth:"100%", title: function() { return "&nbsp;";} });';
									$script_fancybox .= '$.fancybox([';
                                }
                                $common = '';

								$idx = 0;

                                foreach ( $imgs as $item_thumb ) {
                                    $li_class = '';
                                    if  ($i == 0 ){ $li_class = 'first_item'; } elseif ( $i == count($imgs)-1 ) { $li_class = 'last_item'; }
                                    $image_lager_default_url = $item_thumb->image_lager_default_url;

									$image_thumb_attribute = wp_get_attachment_image_src( $item_thumb->ID, 'wpsc-dynamic-gallery-thumb');
                                    $image_thumb_default_url = $image_thumb_attribute[0];

                                    $thumb_height = $g_thumb_height;
                                    $thumb_width = $g_thumb_width;
                                    $width_old = $image_thumb_attribute[1];
                                    $height_old = $image_thumb_attribute[2];
									if ( $width_old > $g_thumb_width || $height_old > $g_thumb_height ) {
                                        if ( $height_old > $g_thumb_height && $g_thumb_height > 0 ) {
                                            $factor = ($height_old / $g_thumb_height);
                                            $thumb_height = $g_thumb_height;
                                            $thumb_width = $width_old / $factor;
                                        }
                                        if ( $thumb_width > $g_thumb_width && $g_thumb_width > 0 ) {
                                            $factor = ($width_old / $g_thumb_width);
                                            $thumb_height = $height_old / $factor;
                                            $thumb_width = $g_thumb_width;
                                        } elseif( $thumb_width == $g_thumb_width && $width_old > $g_thumb_width && $g_thumb_width > 0 ) {
                                            $factor = ($width_old / $g_thumb_width);
                                            $thumb_height = $height_old / $factor;
                                            $thumb_width = $g_thumb_width;
                                        }
									} else {
										$thumb_height = $height_old;
                                        $thumb_width = $width_old;
									}
									$alt = get_post_meta($item_thumb->ID, '_wp_attachment_image_alt', true);
									$img_description = $item_thumb->post_excerpt;

                                    $html .=  '<li class="'.$li_class.'"><a alt="'.esc_attr( $alt ).'" class="gallery_product_'.$product_id.' gallery_product_'.$product_id.'_'.$idx.'" title="'. esc_attr( $img_description ) .'" rel="gallery_product_'.$product_id.'" href="'.$image_lager_default_url.'"><div><img idx="'.$idx.'" style="width:'.$thumb_width.'px !important;height:'.$thumb_height.'px !important" src="'.$image_thumb_default_url.'" alt="'. esc_attr( $alt ) .'" data-caption="'. esc_attr( $img_description ) .'" class="image'.$i.'" width="'.$thumb_width.'" height="'.$thumb_height.'"></div></a></li>';
                                    $img_description = esc_js( $img_description );
                                    if ( $img_description != '' ) {
										$script_fancybox .= $common.'{href:"'.$image_lager_default_url.'",title:"'.$img_description.'"}';
                                    } else {
										$script_fancybox .= $common.'{href:"'.$image_lager_default_url.'",title:""}';
                                    }
                                    $common = ',';
                                    $i++;
									$idx++;
								}

                                if ( count($imgs) <= 1 ) {
									$script_fancybox .= ');';
                                } else {
									$script_fancybox .= '],{
										 \'index\': idx
      								});';
                                }
                                $script_colorbox .= 'ev.preventDefault();';
                                $script_colorbox .= '} });';
								$script_fancybox .= '} });';
                                $script_colorbox .= '});';
								$script_fancybox .= '});';
                                $script_colorbox .= '})(jQuery);';
								$script_fancybox .= '})(jQuery);';
                                $script_colorbox .= '</script>';
								$script_fancybox .= '</script>';

                        } else {
                            $html .=  '<li style="width:'.$g_thumb_width.'px;height:'.$g_thumb_height.'px;"> <a style="width:'.($g_thumb_width-2).'px !important;height:'.($g_thumb_height - 2).'px !important;overflow:hidden;float:left !important" class="" rel="gallery_product_'.$product_id.'" href="'.WPSC_DYNAMIC_GALLERY_JS_URL . '/mygallery/no-image.png"> <div><img style="width:'.$g_thumb_width.'px;height:'.$g_thumb_height.'px;" src="'.WPSC_DYNAMIC_GALLERY_JS_URL . '/mygallery/no-image.png" class="image" alt=""> </div></a> </li>';
                        }
						if ( $popup_gallery == 'deactivate') {
							$script_colorbox = '';
							$script_fancybox = '';
						} elseif ($popup_gallery == 'colorbox' ){
                        	$html .=  $script_colorbox;
						} else {
							$html .=  $script_fancybox;
						}
                        $html .=  '</ul>
                        </div>
                      </div>
                    </div>
          		</div>';

		return $html;
	}
}
?>
<?php
/**
 * WPEC Dynamic Gallery Functions
 *
 * Table Of Contents
 *
 * reset_products_galleries_activate()
 * reset_variations_galleries_activate()
 * html2rgb()
 * upgrade_1_2_1()
 */
class WPSC_Dynamic_Gallery_Functions 
{
	
	public static function reset_products_galleries_activate() {
		global $wpdb;
		$wpdb->query( "DELETE FROM ".$wpdb->postmeta." WHERE meta_key='_actived_d_gallery' " );
	}
	
	public static function reset_variations_galleries_activate() {
		global $wpdb;
		$wpdb->query( "DELETE FROM ".$wpdb->postmeta." WHERE meta_key='_wpsc_dgallery_show_variation' " );
	}
	
	public static function add_google_fonts() {
		global $wpsc_dgallery_style_setting, $wpsc_dgallery_fonts_face;
		
		$caption_font = $wpsc_dgallery_style_setting['caption_font'];
		$google_fonts = array( $caption_font['face'] );
		
		$navbar_font = $wpsc_dgallery_style_setting['navbar_font'];
		if ( $wpsc_dgallery_style_setting['product_gallery_nav'] == 'yes' )
			$google_fonts[] = $navbar_font['face'];
		$wpsc_dgallery_fonts_face->generate_google_webfonts( $google_fonts );
	}
	
	public static function html2rgb($color,$text = false){
		if ($color[0] == '#')
			$color = substr($color, 1);
	
		if (strlen($color) == 6)
			list($r, $g, $b) = array($color[0].$color[1],
									 $color[2].$color[3],
									 $color[4].$color[5]);
		elseif (strlen($color) == 3)
			list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		else
			return false;
	
		$r = hexdec($r); $g = hexdec($g); $b = hexdec($b);
		if($text){
			return $r.','.$g.','.$b;
		}else{
			return array($r, $g, $b);
		}
	}
	
	public static function upgrade_1_1_4() {
		$default_settings = array(
			'product_gallery_width'					=> get_option('product_gallery_width', 100),
			'width_type'							=> get_option('width_type', '%'),
			'product_gallery_height'				=> get_option('product_gallery_height', 75),
			'product_gallery_auto_start'			=> get_option('product_gallery_auto_start', 'true'),
			'product_gallery_speed'					=> get_option('product_gallery_speed', 5),
			'product_gallery_effect'				=> get_option('product_gallery_effect', 'slide-vert'),
			'product_gallery_animation_speed'		=> get_option('product_gallery_animation_speed', 2),
			'stop_scroll_1image'					=> get_option('dynamic_gallery_stop_scroll_1image', 'no'),
			'bg_image_wrapper'						=> get_option('bg_image_wrapper', '#FFFFFF'),
			'border_image_wrapper_color'			=> get_option('border_image_wrapper_color', '#CCCCCC'),
		);
		update_option( 'wpsc_dgallery_container_settings', $default_settings );
		
		$default_settings = array(
			'popup_gallery'							=> get_option('popup_gallery', 'fb'),
			'dgallery_activate'						=> 'yes',
			'dgallery_show_variation'				=> get_option('dynamic_gallery_show_variation', 'yes'),
		);
		update_option( 'wpsc_dgallery_global_settings', $default_settings );
		
		$default_settings = array(
			'caption_font'							=> get_option('caption_font', 'Arial, sans-serif'),
			'caption_font_size'						=> get_option('caption_font_size', '12px'),
			'caption_font_style'					=> get_option('caption_font_style', 'normal'),
			'product_gallery_text_color'			=> get_option('product_gallery_text_color', '#FFFFFF'),
			'product_gallery_bg_des'				=> get_option('product_gallery_bg_des', '#000000'),
		);
		update_option( 'wpsc_dgallery_caption_settings', $default_settings );
		
		$default_settings = array(
			'product_gallery_nav'					=> get_option('product_gallery_nav', 'yes'),
			'navbar_font'							=> get_option('navbar_font', 'Arial, sans-serif'),
			'navbar_font_size'						=> get_option('navbar_font_size', '12px'),
			'navbar_font_style'						=> get_option('navbar_font_style', 'bold'),
			'bg_nav_text_color'						=> get_option('bg_nav_text_color', '#000000'),
			'bg_nav_color'							=> get_option('bg_nav_color', '#FFFFFF'),
			'navbar_height'							=> get_option('navbar_height', 25),
		);
		update_option( 'wpsc_dgallery_navbar_settings', $default_settings );
		
		$default_settings = array(
			'lazy_load_scroll'						=> get_option('lazy_load_scroll', 'yes'),
			'transition_scroll_bar'					=> get_option('transition_scroll_bar', '#000000'),
		);
		update_option( 'wpsc_dgallery_lazyload_settings', $default_settings );
		
		$default_settings = array(
			'enable_gallery_thumb'					=> get_option('enable_gallery_thumb', 'yes'),
			'hide_thumb_1image'						=> get_option('dynamic_gallery_hide_thumb_1image', 'no'),
			'thumb_width'							=> get_option('thumb_width', 105),
			'thumb_height'							=> get_option('thumb_height', 75),
			'thumb_spacing'							=> get_option('thumb_spacing', 2),
		);
		update_option( 'wpsc_dgallery_thumbnail_settings', $default_settings );
		
		
		global $wpdb;
		$wpdb->query( "UPDATE ".$wpdb->postmeta." SET meta_key='_wpsc_dgallery_show_variation' WHERE meta_key='_show_variation' " );
		$wpdb->query( "UPDATE ".$wpdb->postmeta." SET meta_key='_wpsc_dgallery_in_variations' WHERE meta_key='_in_variations' " );
	}
	
	public static function upgrade_1_1_5() {
		$wpsc_dgallery_global_settings = get_option('wpsc_dgallery_global_settings', array() );
		if ( isset( $wpsc_dgallery_global_settings['popup_gallery'] ) && $wpsc_dgallery_global_settings['popup_gallery'] == 'lb' ) {
			$wpsc_dgallery_global_settings['popup_gallery'] = 'colorbox';
			update_option('wpsc_dgallery_global_settings', $wpsc_dgallery_global_settings );	
		}
	}
	
	public static function upgrade_1_1_9() {
		$wpsc_dgallery_container_settings = get_option( 'wpsc_dgallery_container_settings' );
		
		$wpsc_dgallery_style_setting = array();
		$wpsc_dgallery_style_setting = array_merge( $wpsc_dgallery_style_setting, $wpsc_dgallery_container_settings );
		$wpsc_dgallery_style_setting['product_gallery_width_responsive'] = trim( $wpsc_dgallery_container_settings['product_gallery_width'] );
		$wpsc_dgallery_style_setting['product_gallery_width_fixed'] = trim( $wpsc_dgallery_container_settings['product_gallery_width'] );
		
		$wpsc_dgallery_caption_settings = get_option( 'wpsc_dgallery_caption_settings', array() );
		$wpsc_dgallery_style_setting['caption_font'] = array(
			'size' 		=> $wpsc_dgallery_caption_settings['caption_font_size'],
			'face'		=> $wpsc_dgallery_caption_settings['caption_font'],
			'style'		=> $wpsc_dgallery_caption_settings['caption_font_style'],
			'color' 	=> $wpsc_dgallery_caption_settings['product_gallery_text_color'],
		);
		$wpsc_dgallery_style_setting['product_gallery_bg_des'] = $wpsc_dgallery_caption_settings['product_gallery_bg_des'];
		
		$wpsc_dgallery_navbar_settings = get_option( 'wpsc_dgallery_navbar_settings', array() );
		
		$wpsc_dgallery_style_setting['product_gallery_nav'] = $wpsc_dgallery_navbar_settings['product_gallery_nav'];
		$wpsc_dgallery_style_setting['bg_nav_color'] = $wpsc_dgallery_navbar_settings['bg_nav_color'];
		$wpsc_dgallery_style_setting['navbar_height'] = $wpsc_dgallery_navbar_settings['navbar_height'];
		
		$wpsc_dgallery_style_setting['navbar_font'] = array(
			'size' 		=> $wpsc_dgallery_navbar_settings['caption_font_size'],
			'face'		=> $wpsc_dgallery_navbar_settings['caption_font'],
			'style'		=> $wpsc_dgallery_navbar_settings['caption_font_style'],
			'color' 	=> $wpsc_dgallery_navbar_settings['product_gallery_text_color'],
		);
		
		$wpsc_dgallery_lazyload_settings = get_option( 'wpsc_dgallery_lazyload_settings', array() );
		
		$wpsc_dgallery_style_setting = array_merge( $wpsc_dgallery_style_setting, $wpsc_dgallery_lazyload_settings );
		
		update_option( 'wpsc_dgallery_style_setting', $wpsc_dgallery_style_setting );
		
	}
}
?>

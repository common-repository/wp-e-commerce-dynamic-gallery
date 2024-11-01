<?php
function wpsc_dynamic_gallery_install(){
	update_option('a3rev_wpsc_dgallery_version', '2.0.0');
	
	// Set Settings Default from Admin Init
	global $wpsc_dgallery_admin_init;
	$wpsc_dgallery_admin_init->set_default_settings();

	// Build sass
	global $wpsc_dynamic_gallery_less;
	$wpsc_dynamic_gallery_less->plugin_build_sass();
		
	update_option('a3rev_wpsc_dgallery_just_installed', true);
}

update_option('a3rev_wpsc_dgallery_plugin', 'wpsc_dynamic_gallery');

/**
 * Load languages file
 */
function wpsc_dynamic_gallery_init() {
	if ( get_option('a3rev_wpsc_dgallery_just_installed') ) {
		delete_option('a3rev_wpsc_dgallery_just_installed');
		wp_redirect( admin_url( 'edit.php?post_type=wpsc-product&page=wpsc-dynamic-gallery', 'relative' ) );
		exit;
	}

	wpsc_dynamic_gallery_plugin_textdomain();

	load_plugin_textdomain( 'wpsc_dgallery', false, WPSC_DYNAMIC_GALLERY_FOLDER.'/languages' );

	global $wpsc_dgallery_thumbnail_settings;

	$thumb_width = $wpsc_dgallery_thumbnail_settings['thumb_width'];
	if ( $thumb_width <= 0 ) $thumb_width = 105;
	$thumb_height = $wpsc_dgallery_thumbnail_settings['thumb_height'];
	if ( $thumb_height <= 0 ) $thumb_height = 75;
	add_image_size( 'wpsc-dynamic-gallery-thumb', $thumb_width, $thumb_height, false  );
}

// Add language
add_action('init', 'wpsc_dynamic_gallery_init');

// Add custom style to dashboard
add_action( 'admin_enqueue_scripts', array( 'WPSC_Dynamic_Gallery_Hook_Filter', 'a3_wp_admin' ) );

// Add text on right of Visit the plugin on Plugin manager page
add_filter( 'plugin_row_meta', array('WPSC_Dynamic_Gallery_Hook_Filter', 'plugin_extra_links'), 10, 2 );
	
// Need to call Admin Init to show Admin UI
global $wpsc_dgallery_admin_init;
$wpsc_dgallery_admin_init->init();

// Add extra link on left of Deactivate link on Plugin manager page
add_action('plugin_action_links_' . WPSC_DYNAMIC_GALLERY_NAME, array( 'WPSC_Dynamic_Gallery_Hook_Filter', 'settings_plugin_links' ) );

// Add extra fields for image in Product Edit Page
add_filter( 'attachment_fields_to_edit', array('WPSC_Dynamic_Gallery_Hook_Filter', 'wpsc_attachment_fields_filter'), 12, 2 );
add_filter( 'attachment_fields_to_save', array('WPSC_Dynamic_Gallery_Hook_Filter', 'wpsc_exclude_image_from_product_page_field_save'), 1, 2 );
add_action( 'add_attachment', array('WPSC_Dynamic_Gallery_Hook_Filter', 'wpsc_exclude_image_from_product_page_field_add') );

// Version 1.0.5
add_action('wp_ajax_wpsc_dgallery_change_variation', array('WPSC_Dynamic_Gallery_Variations', 'wpsc_dgallery_change_variation') );
add_action('wp_ajax_nopriv_wpsc_dgallery_change_variation', array('WPSC_Dynamic_Gallery_Variations', 'wpsc_dgallery_change_variation') );

add_filter( 'attachment_fields_to_edit', array('WPSC_Dynamic_Gallery_Variations', 'media_fields'), 13, 2 );
add_filter( 'attachment_fields_to_save', array('WPSC_Dynamic_Gallery_Variations', 'save_media_fields'), 13, 2 );

add_action('admin_footer', array('WPSC_Dynamic_Gallery_Hook_Filter', 'wp_admin_footer_scripts') );

//Ajax do dynamic gallery frontend
add_action('wp_ajax_wpsc_dynamic_gallery_frontend', array('WPSC_Dynamic_Gallery_Hook_Filter', 'wpsc_dynamic_gallery_frontend') );
add_action('wp_ajax_nopriv_wpsc_dynamic_gallery_frontend', array('WPSC_Dynamic_Gallery_Hook_Filter', 'wpsc_dynamic_gallery_frontend') );

// Check upgrade functions
add_action('init', 'wpsc_dgallery_pro_upgrade_plugin');
function wpsc_dgallery_pro_upgrade_plugin () {

	// Upgrade to 2.0.0
	if( version_compare(get_option('a3rev_wpsc_dgallery_version'), '2.0.0') === -1 ){
		update_option('a3rev_wpsc_dgallery_version', '2.0.0');

		global $wpsc_dgallery_admin_init;
		$wpsc_dgallery_admin_init->set_default_settings();

		// Build sass
		global $wpsc_dynamic_gallery_less;
		$wpsc_dynamic_gallery_less->plugin_build_sass();
	}

	update_option('a3rev_wpsc_dgallery_version', '2.0.0');
}

//Frontend do dynamic gallery
if (!is_admin()) 
	add_action('init', array('WPSC_Dynamic_Gallery_Hook_Filter', 'dynamic_gallery_frontend_script') );
	
add_action('wp_head', 'wpsc_show_dynamic_gallery_with_goldcart' );

function wpsc_show_dynamic_gallery_with_goldcart() {
	global $post;
		
	if ( !function_exists( 'gold_shpcrt_display_gallery' ) ) {
		function gold_shpcrt_display_gallery($product_id) {
			global $wpsc_dgallery_global_settings;
			
			$global_wpsc_dgallery_activate = $wpsc_dgallery_global_settings['dgallery_activate'];
			
			if ($product_id <= 0) {
				$product_id = $post->ID;
			}
			$actived_d_gallery = get_post_meta($product_id, '_actived_d_gallery',true);
			if ($actived_d_gallery == '' && $global_wpsc_dgallery_activate != 'no') {
				$actived_d_gallery = 1;
			}
			if ( is_singular('wpsc-product') && $actived_d_gallery == 1 ) {
				wp_enqueue_script( 'filter-gallery-script', WPSC_DYNAMIC_GALLERY_JS_URL . '/filter_gallery.js', array(), false, true );
				WPSC_Dynamic_Gallery_Hook_Filter::dynamic_gallery_frontend_script();
				
				$global_show_variation = $wpsc_dgallery_global_settings['dgallery_show_variation'];
				$show_variation = get_post_meta($product_id, '_wpsc_dgallery_show_variation',true);
							
				$show_vr = false;
							
				if($show_variation == '' && $global_show_variation == 'yes'){
					$show_vr = true;
				}elseif($show_variation == 1){
					$show_vr = true;
				}
							
				if($show_vr){
					global $wpsc_dgallery_style_setting;
					if ( isset( $wpsc_dgallery_style_setting['variation_gallery_effect'] ) )
						$variation_gallery_effect = $wpsc_dgallery_style_setting['variation_gallery_effect'];
					else
						$variation_gallery_effect = 'fade';
					
					if ( isset( $wpsc_dgallery_style_setting['variation_gallery_effect_speed'] ) )
						$variation_gallery_effect_speed = $wpsc_dgallery_style_setting['variation_gallery_effect_speed'] * 500;
					else
						$variation_gallery_effect_speed = 2 * 500;
				
					wp_enqueue_script( 'wpsc-gallery-variations-script', WPSC_DYNAMIC_GALLERY_JS_URL . '/select_variations.js', array(), false, true );
					$wpsc_dgallery_change_variation = wp_create_nonce("wpsc_dgallery_change_variation");
					wp_localize_script('wpsc-gallery-variations-script', 'vars', array( 'product_id' => $product_id, 'security' => $wpsc_dgallery_change_variation, 'ajax_url' => admin_url( 'admin-ajax.php', 'relative' ), 'pluginsurl' => WPSC_DYNAMIC_GALLERY_URL, 'siteurl' => get_bloginfo('url'), 'imgurl' => WPSC_DYNAMIC_GALLERY_IMAGES_URL, 'variation_gallery_effect' => $variation_gallery_effect, 'variation_gallery_effect_speed' => $variation_gallery_effect_speed ) );
				}
				
				echo WPSC_Dynamic_Gallery_Display_Class::wpsc_dynamic_gallery_display($product_id);
			}
		}
	} else {
		global $wpsc_dgallery_global_settings;
		
		$global_wpsc_dgallery_activate = $wpsc_dgallery_global_settings['dgallery_activate'];
		$actived_d_gallery = get_post_meta($post->ID, '_actived_d_gallery',true);
		if ($actived_d_gallery == '' && $global_wpsc_dgallery_activate != 'no') {
			$actived_d_gallery = 1;
		}
		
		if ( is_singular('wpsc-product') && $actived_d_gallery == 1 ) {
			wp_enqueue_script( 'filter-gallery-script', WPSC_DYNAMIC_GALLERY_JS_URL . '/filter_gallery.js', array(), false, true );
			WPSC_Dynamic_Gallery_Hook_Filter::dynamic_gallery_frontend_script();
			
			$global_show_variation = $wpsc_dgallery_global_settings['dgallery_show_variation'];
			$show_variation = get_post_meta($post->ID, '_wpsc_dgallery_show_variation',true);
							
			$show_vr = false;
							
			if($show_variation == '' && $global_show_variation == 'yes'){
					$show_vr = true;
			}elseif($show_variation == 1){
					$show_vr = true;
			}
							
			if($show_vr){
				global $wpsc_dgallery_style_setting;
				if ( isset( $wpsc_dgallery_style_setting['variation_gallery_effect'] ) )
					$variation_gallery_effect = $wpsc_dgallery_style_setting['variation_gallery_effect'];
				else
					$variation_gallery_effect = 'fade';
					
				if ( isset( $wpsc_dgallery_style_setting['variation_gallery_effect_speed'] ) )
					$variation_gallery_effect_speed = $wpsc_dgallery_style_setting['variation_gallery_effect_speed'] * 500;
				else
					$variation_gallery_effect_speed = 2 * 500;
				wp_enqueue_script( 'wpsc-gallery-variations-script', WPSC_DYNAMIC_GALLERY_JS_URL . '/select_variations_goldcart.js', array(), false, true );
				$wpsc_dgallery_change_variation = wp_create_nonce("wpsc_dgallery_change_variation");
				wp_localize_script('wpsc-gallery-variations-script', 'vars', array( 'product_id' => $post->ID, 'security' => $wpsc_dgallery_change_variation, 'ajax_url' => admin_url( 'admin-ajax.php', 'relative' ), 'pluginsurl' => WPSC_DYNAMIC_GALLERY_URL, 'siteurl' => get_bloginfo('url'), 'imgurl' => WPSC_DYNAMIC_GALLERY_IMAGES_URL, 'variation_gallery_effect' => $variation_gallery_effect, 'variation_gallery_effect_speed' => $variation_gallery_effect_speed ) );
			}
				
			add_action('get_footer', array('WPSC_Dynamic_Gallery_Hook_Filter', 'do_dynamic_gallery'), 8 );
		}
	}	
}
?>
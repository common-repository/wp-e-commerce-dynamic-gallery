<?php
/**
 * WP e-Commerce Dynamic Gallery Hook Filter Class
 *
 * Class Function into WP e-Commerce plugin
 *
 * Table Of Contents
 *
 * dynamic_gallery_frontend_script()
 * wp_admin_footer_scripts()
 * wpsc_dynamic_gallery_display()
 * wpsc_dynamic_gallery_preview()
 * wpsc_hide_featured_image_single_product()
 * a3_wp_admin()
 * plugin_extra_links()
 */
class WPSC_Dynamic_Gallery_Hook_Filter
{
	
	public static function dynamic_gallery_frontend_script() {
		global $wpsc_dgallery_global_settings;
		// Include google fonts into header
		add_action( 'wp_head', array( 'WPSC_Dynamic_Gallery_Functions', 'add_google_fonts'), 10 );
				
			$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script('jquery');
			wp_enqueue_style( 'ad-gallery-style', WPSC_DYNAMIC_GALLERY_JS_URL . '/mygallery/jquery.ad-gallery.css' );
			wp_enqueue_script( 'ad-gallery-script', WPSC_DYNAMIC_GALLERY_JS_URL . '/mygallery/jquery.ad-gallery.js', array(), false );
			
			$popup_gallery = $wpsc_dgallery_global_settings['popup_gallery'];
			if ($popup_gallery == 'colorbox') {
				wp_enqueue_style( 'a3_colorbox_style', WPSC_DYNAMIC_GALLERY_JS_URL . '/colorbox/colorbox.css' );
				wp_enqueue_script( 'colorbox_script', WPSC_DYNAMIC_GALLERY_JS_URL . '/colorbox/jquery.colorbox'.$suffix.'.js', array(), false, true );
			} elseif($popup_gallery != 'deactivate') {
				wp_enqueue_style( 'woocommerce_fancybox_styles', WPSC_DYNAMIC_GALLERY_JS_URL . '/fancybox/fancybox.css' );
				wp_enqueue_script( 'fancybox', WPSC_DYNAMIC_GALLERY_JS_URL . '/fancybox/fancybox'.$suffix.'.js', array(), false, true );
			}
	}
	
	public static function wp_admin_footer_scripts() {
	?>
    <script type="text/javascript">
		(function($){		
			$(function(){	
				$("a.nav-tab").click(function(){
					if($(this).attr('data-tab-id') == 'gallery_settings'){
						window.location.href=$(this).attr('href');
						return false;
					}
				});
			});		  
		})(jQuery);
	</script>
    <?php
	}
	
	public static function wpsc_attachment_fields_filter( $form_fields, $post ) {
		if (!$post->post_parent) return $form_fields;
		$parent = get_post( $post->post_parent );
		if ($parent->post_type!=='wpsc-product') return $form_fields;
		
		$edit_post = sanitize_post($post, 'edit');
		if (is_array($form_fields) && count($form_fields) > 0) {
			$number_field = 0;
			$field_post_excerpt = array(
										'label' => __('Caption', 'wp-e-commerce-dynamic-gallery' ),
										'input' => 'html',
										'html' => wp_caption_input_textarea($edit_post));
			foreach ($form_fields as $form_key => $form_values) {
				if ($number_field == 1 && is_array($field_post_excerpt)) {
					$new_form_fields['post_excerpt'] = $field_post_excerpt;
					$field_post_excerpt = '';
				}
				$new_form_fields[$form_key] = $form_values;
				
				$number_field++;
			}
			$form_fields = $new_form_fields;
		}
		
		$exclude_image = (int) get_post_meta($post->ID, '_wpsc_exclude_image', true);
		
		$label = __('Exclude image', 'wp-e-commerce-dynamic-gallery' );
		
		$html = '<input type="checkbox" '.checked($exclude_image, 1, false).' name="attachments['.$post->ID.'][wpsc_exclude_image]" id="attachments['.$post->ID.'][wpsc_exclude_image]" />';
		
		$form_fields['wpsc_exclude_image'] = array(
				'label' => $label,
				'input' => 'html',
				'html' =>  $html,
				'value' => '',
				'helps' => __('Enabling this option will hide it from the product page image gallery. If assigned to variations below the image will show when option is selected.', 'wp-e-commerce-dynamic-gallery' )
		);
		
		return $form_fields;
	}
	
	public static function wpsc_exclude_image_from_product_page_field_save( $post, $attachment ) {
	
		if (isset($_REQUEST['attachments'][$post['ID']]['wpsc_exclude_image'])) :
			delete_post_meta( (int) $post['ID'], '_wpsc_exclude_image' );
			update_post_meta( (int) $post['ID'], '_wpsc_exclude_image', 1);
		else :
			delete_post_meta( (int) $post['ID'], '_wpsc_exclude_image' );
			update_post_meta( (int) $post['ID'], '_wpsc_exclude_image', 0);
		endif;
			
		return $post;
					
	}
	
	public static function wpsc_exclude_image_from_product_page_field_add( $post_id ) {
		add_post_meta( $post_id, '_wpsc_exclude_image', 0, true );
	}
	
	public static function wpsc_dynamic_gallery_preview(){
		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) die();
		WPSC_Dynamic_Gallery_Preview_Display::wpsc_dynamic_gallery_preview($_REQUEST);
		die();
	}
	
	public static function wpsc_dynamic_gallery_frontend(){
		ob_start();
		echo WPSC_Dynamic_Gallery_Display_Class::wpsc_dynamic_gallery_display($_REQUEST['product_id']);
		$result = ob_get_clean();
		echo json_encode($result);
		die();
	}
	
	public static function do_dynamic_gallery(){
		global $post;
		global $wpsc_dgallery_style_setting;
		if ( is_singular('wpsc-product') ) {
			$wpsc_dynamic_gallery_frontend = wp_create_nonce("wpsc_dynamic_gallery_frontend");
			$width_type = $wpsc_dgallery_style_setting['width_type'];
			if ( $width_type == 'px' ) {
				$g_width = $wpsc_dgallery_style_setting['product_gallery_width_fixed'].'px';
				$g_height = $wpsc_dgallery_style_setting['product_gallery_height'];
			} else { 
				$g_width = $wpsc_dgallery_style_setting['product_gallery_width_responsive'].'%';
				$g_height = 138;
			}
			
	
			echo "<script type=\"text/javascript\">
			jQuery(document).ready(function($) {
				var container_image = $('.single_product_display .imagecol');
				container_image.html('<div style=\'display: block; width: 100%; height: 16px; text-align: center; position: absolute; top: 48%;\'><img src=\'".WPSC_DYNAMIC_GALLERY_JS_URL."/mygallery/ajax-loader.gif\' style=\'width:16px !important;height:16px !important;margin:auto !important;padding:0 !important;border:0 !important;\' /></div>').css('position','relative').css('width','".$g_width."').css('min-height','".$g_height."px');
				$.get( '". admin_url( 'admin-ajax.php', 'relative' )."?security=".$wpsc_dynamic_gallery_frontend."&action=wpsc_dynamic_gallery_frontend&product_id=".$post->ID."', function(response) {
					response = $.parseJSON( response );
					container_image.html(response);
				});
			});
			</script>";
		}
	}
	
	public static function a3_wp_admin() {
		wp_enqueue_style( 'a3rev-wp-admin-style', WPSC_DYNAMIC_GALLERY_CSS_URL . '/a3_wp_admin.css' );
	}
	
	public static function plugin_extra_links( $links, $plugin_name ) {
		if ( $plugin_name != WPSC_DYNAMIC_GALLERY_NAME ) {
			return $links;
		}
		$links[] = '<a href="http://docs.a3rev.com/user-guides/wp-e-commerce/wpec-dynamic-gallery/" target="_blank">'.__('Documentation', 'wp-e-commerce-dynamic-gallery' ).'</a>';
		$links[] = '<a href="http://wordpress.org/support/plugin/wp-e-commerce-dynamic-gallery/" target="_blank">'.__('Support', 'wp-e-commerce-dynamic-gallery').'</a>';
		return $links;
	}

	public static function settings_plugin_links($actions) {
		$actions = array_merge( array( 'settings' => '<a href="edit.php?post_type=wpsc-product&page=wpsc-dynamic-gallery">' . __( 'Settings', 'wp-e-commerce-dynamic-gallery' ) . '</a>' ), $actions );

		return $actions;
	}
}
?>
<?php
/**
 * WP e-Commerce Dynamic Gallery Metaboxes Class
 *
 * Class Function into WP e-Commerce plugin
 *
 * Table Of Contents
 *
 *
 * remove_wpsc_metaboxes()
 * wpsc_meta_boxes_image()
 * wpsc_product_image_box()
 * save_actived_d_gallery()
 */
class WPSC_Dynamic_Gallery_Metaboxes_Class
{
	public static function remove_wpsc_metaboxes(){
		global $post;
		if ( is_admin() ) :
			remove_meta_box('wpsc_product_image_forms', 'wpsc-product', 'normal');
		endif;
	}
	
	
	public static function wpsc_meta_boxes_image() {
		global $post;
		global $wpsc_dgallery_global_settings;
		
		$global_wpsc_dgallery_activate = $wpsc_dgallery_global_settings['dgallery_activate'];
		$actived_d_gallery = get_post_meta($post->ID, '_actived_d_gallery', true);
		if ( $actived_d_gallery == '' && $global_wpsc_dgallery_activate != 'no' ) {
			$actived_d_gallery = 1;
		}
		
		$default_show_variation = $wpsc_dgallery_global_settings['dgallery_show_variation'];
		$show_variation = get_post_meta($post->ID, '_wpsc_dgallery_show_variation',true);
		$show = '';
		if ( $show_variation == '' ) $show_variation = $default_show_variation;
		if ( $show_variation == 1 || $show_variation == 'yes' ) { $show = 'checked="checked"'; }
		
		add_meta_box( 'wpsc_product_gallery_image_forms', '<label class="a3_actived_d_gallery" style="margin-right: 50px;"><input type="checkbox" '.checked( $actived_d_gallery, 1, false).' value="1" name="_actived_d_gallery" class="actived_d_gallery" /> '.__('A3 Dynamic Image Gallery activated', 'wp-e-commerce-dynamic-gallery' ).'</label> <label class="a3_wpsc_dgallery_show_variation"><input type="checkbox" '.$show.' value="1" name="_wpsc_dgallery_show_variation" /> '.__('Product Variation Images activated', 'wp-e-commerce-dynamic-gallery' ).'</label>', array('WPSC_Dynamic_Gallery_Metaboxes_Class','wpsc_product_image_box'), 'wpsc-product', 'normal', 'high' );
	}
	
	public static function wpsc_product_image_box() {
		global $post, $thepostid;
		global $wpsc_dgallery_global_settings;
		
		$hide_wpec_gallery = $wpsc_dgallery_global_settings['hide_wpec_gallery'];
		$global_wpec_dgallery_activate = $wpsc_dgallery_global_settings['dgallery_activate'];
		$actived_d_gallery = get_post_meta($post->ID, '_actived_d_gallery', true);
		
		if ($actived_d_gallery == '' && $global_wpec_dgallery_activate != 'no') {
			$actived_d_gallery = 1;
		}
	?>
    	<script>
		<?php if ( $actived_d_gallery == 1 && $hide_wpec_gallery == 'yes' ) { ?>
		jQuery(document).ready(function() {
			jQuery('#wpsc_product_details_tabs li').first().removeClass('tabs').hide();
			jQuery('#wpsc_product_details_tabs li:nth-child(2)' ).addClass('tabs');
			jQuery('#wpsc_product_details-image').hide();
			jQuery('#wpsc_product_details-desc').show();
		});
		<?php } ?>
		jQuery(document).on('click', '#wpsc_product_gallery_image_forms h3', function(){
			if( jQuery('input.actived_d_gallery').is(":checked") ) {
				jQuery('#wpsc_product_gallery_image_forms').removeClass("closed");
			} else {
				jQuery('#wpsc_product_gallery_image_forms').addClass("closed");
			}
		});
		jQuery(document).ready(function() {
			if( jQuery('input.actived_d_gallery').is(":checked") ) {
				jQuery('#wpsc_product_gallery_image_forms').removeClass("closed");
			} else {
				jQuery('#wpsc_product_gallery_image_forms').addClass("closed");
			}
			jQuery('input.actived_d_gallery').change(function() {
				if( jQuery(this).is(":checked") ) {
					jQuery('#wpsc_product_gallery_image_forms').removeClass("closed");
					<?php if ( $hide_wpec_gallery == 'yes' ) { ?>
					jQuery('#wpsc_product_details_tabs li').first().removeClass('tabs').hide();
					jQuery('#wpsc_product_details_tabs li:nth-child(2)' ).addClass('tabs');
					jQuery('#wpsc_product_details-image').slideUp();
					jQuery('#wpsc_product_details-desc').show();
					<?php } ?>
				} else {
					jQuery('#wpsc_product_gallery_image_forms').addClass("closed");
					<?php if ( $hide_wpec_gallery == 'yes' ) { ?>
					jQuery('#wpsc_product_details_tabs li').first().css('display', 'inline');
					<?php } ?>
				}
			});
		});
		</script>
    	<style>
		<?php if ( $actived_d_gallery == 1 && $hide_wpec_gallery == 'yes' ) { ?>
		#wpsc_product_details_tabs li:first-child {
			display: none;
		}
		#wpsc_product_details-image {
			display: none;
		}
		<?php } ?>
		@media screen and ( max-width: 782px ) {
			.a3_actived_d_gallery {
				padding-bottom:5px;	
				display:inline-block;
			}
			.a3_wpsc_dgallery_show_variation {
				white-space:nowrap;
				padding-bottom:5px;
				display:inline-block;
			}
		}
		@media screen and ( max-width: 480px ) {
			.a3_wpsc_dgallery_show_variation {
				white-space:inherit;
			}
		}
        </style>
    <?php
		echo '<script type="text/javascript">
		jQuery(document).on("click", "#wpsc_product_gallery_image_forms h3", function(){
			jQuery("#wpsc_product_gallery_image_forms").removeClass("closed");
		});
		jQuery(document).on("click", ".upload_image_button", function(){
			var post_id = '.$post->ID.';
			//window.send_to_editor = window.send_to_termmeta;
			tb_show("", "media-upload.php?parent_page=wpsc-edit-products&post_id=" + post_id + "&type=image&tab=gallery&TB_iframe=true");
			return false;
		});
		
		</script>';
		echo '<div class="wpsc_options_panel">';
		$attached_images = (array) get_posts( array(
			'post_type'   => 'attachment',
			'post_mime_type' => 'image',
			'numberposts' => -1,
			'post_status' => null,
			'post_parent' => $post->ID ,
			'orderby'     => 'menu_order',
			'order'       => 'ASC',
		) );
		
		$featured_img = get_post_meta($post->ID, '_thumbnail_id');
		$attached_thumb = array();
		if ( count($attached_images) > 0 ) {
			$i = 0;
			foreach ( $attached_images as $key => $object ) {
				$i++;
				if ( in_array( $object->ID, $featured_img ) ) {
					$attached_thumb[0] = $object;
				} else {
					$attached_thumb[$i] = $object;
				}
			}			
		}
		ksort($attached_thumb);
		
		if ( is_array($attached_thumb) && count($attached_thumb) > 0 ) {
	
			echo '<a href="#" onclick="tb_show(\'\', \'media-upload.php?parent_page=wpsc-edit-products&post_id='.$post->ID.'&type=image&TB_iframe=true\');return false;" style="margin-right:10px;margin-bottom:10px;" class="upload_image_button1" rel="'.$post->ID.'"><img src="'.WPSC_DYNAMIC_GALLERY_JS_URL.'/mygallery/no-image.jpg" style="width:69px;height:69px;border:2px solid #CCC" /><input type="hidden" name="upload_image_id[1]" class="upload_image_id" value="0" /></a>';
			
			$i = 0 ;
			foreach ( $attached_thumb as $item_thumb ) {
				$i++;
				$in_variations = get_post_meta( $item_thumb->ID, '_wpsc_dgallery_in_variations', true );
				if ( get_post_meta( $item_thumb->ID, '_wpsc_exclude_image', true ) == 1 && (!is_array($in_variations) || count($in_variations) < 1 ) ) continue;
				$image_attribute = wp_get_attachment_image_src( $item_thumb->ID, array(70,70) );
				echo '<a href="#" style="margin-right:10px;margin-bottom:10px;" class="upload_image_button" rel="'.$post->ID.'"><img src="'.$image_attribute[0].'" style="width:69px;height:69px;border:2px solid #CCC" /><input type="hidden" name="upload_image_id['.$i.']" class="upload_image_id" value="'.$item_thumb->ID.'" /></a>';
			}
		} else {
			echo '<a href="#" class="upload_image_button" rel="'.$post->ID.'"><img src="'.WPSC_DYNAMIC_GALLERY_JS_URL.'/mygallery/no-image.jpg" style="width:69px;height:69px;border:2px solid #CCC" /><input type="hidden" name="upload_image_id[1]" class="upload_image_id" value="0" /></a>';
		}
		
		echo '</div>';			
	}
	
	public static function save_actived_d_gallery( $post_id ) {
		global $post;
		$post_status = get_post_status($post_id);
		$post_type = get_post_type($post_id);
		if ( empty($post_id) || empty($post) || empty($_POST) ) return;
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
		if ( is_int( wp_is_post_revision( $post ) ) ) return;
		if ( is_int( wp_is_post_autosave( $post ) ) ) return;
		if ( !current_user_can( 'edit_post', $post_id )) return;
		if ( $post->post_type != 'wpsc-product' || $post_status == false  || $post_status == 'inherit' ) return;
		
		if ( isset($_REQUEST['_actived_d_gallery']) ) {
			update_post_meta($post_id, '_actived_d_gallery', 1); 
		} else {
			update_post_meta($post_id, '_actived_d_gallery', 0); 
		}
		
		if ( isset($_REQUEST['_wpsc_dgallery_show_variation']) ) {
			update_post_meta($post_id, '_wpsc_dgallery_show_variation', 1); 
		} else {
			update_post_meta($post_id, '_wpsc_dgallery_show_variation', 0); 
		}
	}
}

add_action( 'admin_head', array('WPSC_Dynamic_Gallery_Metaboxes_Class','remove_wpsc_metaboxes') );
add_action( 'add_meta_boxes', array('WPSC_Dynamic_Gallery_Metaboxes_Class','wpsc_meta_boxes_image'), 9);
if (in_array( basename( $_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php') ) ) {
	add_action( 'save_post', array('WPSC_Dynamic_Gallery_Metaboxes_Class','save_actived_d_gallery') );
}
?>
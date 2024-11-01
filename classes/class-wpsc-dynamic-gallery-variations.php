<?php
/**
 * WPSC Dynamic Gallery Variations Class
 *
 *
 * Table Of Contents
 *
 * media_fields()
 * save_media_fields()
 * wpsc_dgallery_change_variation()
 */
class WPSC_Dynamic_Gallery_Variations
{
	
	public static function media_fields( $form_fields, $attachment ) {
		
		if ( !isset( $_GET['post_id'] ) ) return $form_fields;
		$product_id = $_GET['post_id'];
	
		if (!$attachment->post_parent) return $form_fields;
		$parent = get_post( $attachment->post_parent );
		if ($parent->post_type!=='wpsc-product') return $form_fields;
				
		$product_id = $parent->ID;
		
		if ( isset($_GET['tab']) && $_GET['tab'] == 'gallery' && get_post_type($product_id) == 'wpsc-product' && class_exists('wpsc_variations') && wp_attachment_is_image($attachment->ID) ) {
			global $wpsc_variations;
			$wpsc_variations = new wpsc_variations( $product_id );
						
			$chosen = get_post_meta( $attachment->ID, '_wpsc_dgallery_in_variations', true );
						
			if ( wpsc_have_variation_groups() ) {
				$html_start = '<script>jQuery(function() { jQuery(".assign_image_all_variations").click(function () { jQuery("."+jQuery(this).attr("id")).attr("checked", this.checked); }); });</script><hr /><input type="hidden" name="attachments['.$attachment->ID.'][wpsc_dgallery_save_variations]" value="1" />';
				$form_fields['start_variation'] = array(
						'label' => __('Variations', 'wp-e-commerce-dynamic-gallery' ),
						'input' => 'html',
						'html' => $html_start,
						'value' => '',
						'helps'	=> __('Checked on options to show this image in gallery when options is selected on frontend.', 'wp-e-commerce-dynamic-gallery' ),
					);
				while ( wpsc_have_variation_groups() ) : wpsc_the_variation_group();
					
					$html = "<input type='checkbox' class='assign_image_all_variations' id='".$attachment->ID."_".wpsc_vargrp_id()."' name='".$attachment->ID."_".wpsc_vargrp_id()."' value=''> <label for='".$attachment->ID."_".wpsc_vargrp_id()."'><strong>".__('Apply to All', 'wp-e-commerce-dynamic-gallery' )."</strong></label><br />";
					
					while ( wpsc_have_variations() ): wpsc_the_variation();
						if (wpsc_the_variation_id() > 0 ) {
							$checked = ( isset($chosen[wpsc_vargrp_id()]) && $chosen && is_array($chosen[wpsc_vargrp_id()]) && in_array(wpsc_the_variation_id(), $chosen[wpsc_vargrp_id()] ) ) ? 'checked="checked"' : '';
							$html .= "&nbsp;- &nbsp; <input class='".$attachment->ID."_".wpsc_vargrp_id()."' type='checkbox' id='".$attachment->ID."_".wpsc_vargrp_id()."_".wpsc_the_variation_id()."' name='attachments[".$attachment->ID."][wpsc_dgallery_in_variations][".wpsc_vargrp_id()."][]' value='".wpsc_the_variation_id()."' ".$checked."> <label for='".$attachment->ID."_".wpsc_vargrp_id()."_".wpsc_the_variation_id()."'>".esc_html( wpsc_the_variation_name() )."</label><br />";
						}
					endwhile;
											
					$form_fields['in_variations_'.wpsc_vargrp_id()] = array(
						'label' => esc_html( wpsc_the_vargrp_name() ),
						'input' => 'html',
						'html' => $html,
						'value' => ''
					);
					
				endwhile;
				$form_fields['end_variation'] = array(
						'label' => '<hr />',
						'input' => 'html',
						'html' => '<hr />',
						'value' => ''
					);
			}
			
		}
	
		return $form_fields;
	}
	
	public static function save_media_fields( $post, $attachment ) {
		if ( substr($post['post_mime_type'], 0, 5) == 'image' && isset($attachment['wpsc_dgallery_save_variations']) ) {
			if ( isset($attachment['wpsc_dgallery_in_variations']) ) {
				update_post_meta( (int) $post['ID'], '_wpsc_dgallery_in_variations', $attachment['wpsc_dgallery_in_variations'] );
			} else {
				delete_post_meta( (int) $post['ID'], '_wpsc_dgallery_in_variations' );
			}
		}
		return $post;
	}
	
	public static function wpsc_dgallery_change_variation() {
		$product_id   = (int) $_REQUEST['product_id'];
		$variations = array();
		if (is_array($_REQUEST['variations']) && count($_REQUEST['variations']) > 0) {
			foreach ($_REQUEST['variations'] as $variation) {
				if ($variation['value'] > 0) {
					$group = str_replace('variation[', '', $variation['name']);
					$group = str_replace(']', '', $group);
					$variations[$group] = $variation['value'];
				}
			}
		}
		ob_start();
		if ( count($variations) < 1 ) {
			echo WPSC_Dynamic_Gallery_Display_Class::wpsc_dynamic_gallery_display($product_id, true);
		} else {
			echo WPSC_Dynamic_Gallery_Display_Class::get_gallery_variations($product_id, $variations);
		}
		$result = ob_get_clean();
		echo json_encode($result);
		die();
	}
}
?>
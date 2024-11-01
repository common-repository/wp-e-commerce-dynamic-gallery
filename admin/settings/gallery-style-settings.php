<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
/*-----------------------------------------------------------------------------------
WPSC Dynamic Gallery Style Settings

TABLE OF CONTENTS

- var parent_tab
- var subtab_data
- var option_name
- var form_key
- var position
- var form_fields
- var form_messages

- __construct()
- subtab_init()
- set_default_settings()
- get_settings()
- subtab_data()
- add_subtab()
- settings_form()
- init_form_fields()

-----------------------------------------------------------------------------------*/

class WPSC_Dynamic_Gallery_Style_Settings extends WPSC_Dynamic_Gallery_Admin_UI
{
	
	/**
	 * @var string
	 */
	private $parent_tab = 'gallery-style';
	
	/**
	 * @var array
	 */
	private $subtab_data;
	
	/**
	 * @var string
	 * You must change to correct option name that you are working
	 */
	public $option_name = 'wpsc_dgallery_style_setting';
	
	/**
	 * @var string
	 * You must change to correct form key that you are working
	 */
	public $form_key = 'wpsc_dgallery_style_setting';
	
	/**
	 * @var string
	 * You can change the order show of this sub tab in list sub tabs
	 */
	private $position = 1;
	
	/**
	 * @var array
	 */
	public $form_fields = array();
	
	/**
	 * @var array
	 */
	public $form_messages = array();
	
	/*-----------------------------------------------------------------------------------*/
	/* __construct() */
	/* Settings Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {
		$this->init_form_fields();
		$this->subtab_init();
		
		$this->form_messages = array(
				'success_message'	=> __( 'Dynamic Gallery Style successfully saved.', 'wp-e-commerce-dynamic-gallery' ),
				'error_message'		=> __( 'Error: Dynamic Gallery Style can not save.', 'wp-e-commerce-dynamic-gallery' ),
				'reset_message'		=> __( 'Dynamic Gallery Style successfully reseted.', 'wp-e-commerce-dynamic-gallery' ),
			);
		
		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_end', array( $this, 'include_script' ) );
			
		add_action( $this->plugin_name . '_set_default_settings' , array( $this, 'set_default_settings' ) );
		add_action( $this->plugin_name . '_get_all_settings' , array( $this, 'get_settings' ) );
		
		//Ajax Preview gallery
		add_action('wp_ajax_wpsc_dynamic_gallery', array('WPSC_Dynamic_Gallery_Hook_Filter','wpsc_dynamic_gallery_preview'));
		add_action('wp_ajax_nopriv_wpsc_dynamic_gallery', array('WPSC_Dynamic_Gallery_Hook_Filter','wpsc_dynamic_gallery_preview'));
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* subtab_init() */
	/* Sub Tab Init */
	/*-----------------------------------------------------------------------------------*/
	public function subtab_init() {
		
		add_filter( $this->plugin_name . '-' . $this->parent_tab . '_settings_subtabs_array', array( $this, 'add_subtab' ), $this->position );
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* set_default_settings()
	/* Set default settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function set_default_settings() {
		global $wpsc_dgallery_admin_interface;
		
		$wpsc_dgallery_admin_interface->reset_settings( $this->form_fields, $this->option_name, false );
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* get_settings()
	/* Get settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function get_settings() {
		global $wpsc_dgallery_admin_interface;
		
		$wpsc_dgallery_admin_interface->get_settings( $this->form_fields, $this->option_name );
	}
	
	/**
	 * subtab_data()
	 * Get SubTab Data
	 * =============================================
	 * array ( 
	 *		'name'				=> 'my_subtab_name'				: (required) Enter your subtab name that you want to set for this subtab
	 *		'label'				=> 'My SubTab Name'				: (required) Enter the subtab label
	 * 		'callback_function'	=> 'my_callback_function'		: (required) The callback function is called to show content of this subtab
	 * )
	 *
	 */
	public function subtab_data() {
		
		$subtab_data = array( 
			'name'				=> 'gallery-style',
			'label'				=> __( 'Gallery Style', 'wp-e-commerce-dynamic-gallery' ),
			'callback_function'	=> 'wpsc_dgallery_style_settings_form',
		);
		
		if ( $this->subtab_data ) return $this->subtab_data;
		return $this->subtab_data = $subtab_data;
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* add_subtab() */
	/* Add Subtab to Admin Init
	/*-----------------------------------------------------------------------------------*/
	public function add_subtab( $subtabs_array ) {
	
		if ( ! is_array( $subtabs_array ) ) $subtabs_array = array();
		$subtabs_array[] = $this->subtab_data();
		
		return $subtabs_array;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* settings_form() */
	/* Call the form from Admin Interface
	/*-----------------------------------------------------------------------------------*/
	public function settings_form() {
		global $wpsc_dgallery_admin_interface;
		
		$output = '';
		$output .= $wpsc_dgallery_admin_interface->admin_forms( $this->form_fields, $this->form_key, $this->option_name, $this->form_messages );
		
		return $output;
	}
	
	// fix conflict with mandrill plugin
	public function remove_mandrill_notice() {
		remove_action( 'admin_notices', array( 'wpMandrill', 'adminNotices' ) );
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* init_form_fields() */
	/* Init all fields of this form */
	/*-----------------------------------------------------------------------------------*/
	public function init_form_fields() {
		
  		// Define settings			
     	$this->form_fields = apply_filters( $this->option_name . '_settings_fields', array(
		
			array(
            	'name' 		=> __( 'Preview', 'wp-e-commerce-dynamic-gallery' ),
				'desc'		=> '<a href="'.  admin_url( 'admin-ajax.php', 'relative') .'?act=preview-dgallery" class="preview_gallery">' . __( 'Click here to preview gallery', 'wp-e-commerce-dynamic-gallery' ) . '</a>',
                'type' 		=> 'heading',
           	),
			
			array(	'name' => __('Gallery Dimensions', 'wp-e-commerce-dynamic-gallery' ), 'type' => 'heading'),
			
			array(  
				'name' 		=> __( 'Gallery type', 'wp-e-commerce-dynamic-gallery' ),
				'id' 		=> 'width_type',
				'class'		=> 'gallery_width_type',
				'type' 		=> 'switcher_checkbox',
				'default'	=> '%',
				'checked_value'		=> '%',
				'unchecked_value' 	=> 'px',
				'checked_label'		=> __( 'Responsive', 'wp-e-commerce-dynamic-gallery' ),
				'unchecked_label' 	=> __( 'Fixed Wide', 'wp-e-commerce-dynamic-gallery' ),
			),
			array(
            	'class' 	=> 'gallery_width_type_percent',
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'Gallery width', 'wp-e-commerce-dynamic-gallery' ),
				'id' 		=> 'product_gallery_width_responsive',
				'desc'		=> '%',
				'type' 		=> 'slider',
				'default'	=> 100,
				'min'		=> 20,
				'max'		=> 100,
				'increment'	=> 1,
			),
			array(
            	'class' 	=> 'gallery_width_type_percent',
                'type' 		=> 'heading',
				'desc'		=> '<table class="form-table"><tbody><tr valign="top"><th class="titledesc" scope="row"><label>' . __( 'Gallery height', 'wp-e-commerce-dynamic-gallery' ) . '</label></th><td class="forminp">' . __( "Show tall in proportion to wide", 'wp-e-commerce-dynamic-gallery' ) . '</td></tr></tbody></table>',
           	),
			
			array(
            	'class' 	=> 'gallery_width_type_fixed',
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'Gallery width', 'wp-e-commerce-dynamic-gallery' ),
				'id' 		=> 'product_gallery_width_fixed',
				'desc'		=> 'px',
				'type' 		=> 'text',
				'default'	=> 320,
				'css' 		=> 'width:40px;',
			),
			array(  
				'name' 		=> __( 'Gallery height', 'wp-e-commerce-dynamic-gallery' ),
				'desc'		=> 'px',
				'class'		=> 'wpsc_product_gallery_height',
				'id' 		=> 'product_gallery_height',
				'type' 		=> 'text',
				'default'	=> 215,
				'css' 		=> 'width:40px;',
			),
			
			array(	
				'name' => __('Gallery Special Effects', 'wp-e-commerce-dynamic-gallery' ), 
				'type' => 'heading'
			),
			array(  
				'name' => __( 'Auto start', 'wp-e-commerce-dynamic-gallery' ),
				'desc' 		=> '',
				'id' 		=> 'product_gallery_auto_start',
				'default'	=> 'true',
				'type' 		=> 'onoff_checkbox',
				'checked_value'		=> 'true',
				'unchecked_value'	=> 'false',
				'checked_label'		=> __( 'YES', 'wp-e-commerce-dynamic-gallery' ),
				'unchecked_label' 	=> __( 'NO', 'wp-e-commerce-dynamic-gallery' ),
			),
			array(  
				'name' => __( 'Slide transition effect', 'wp-e-commerce-dynamic-gallery' ),
				'desc' 		=> '',
				'id' 		=> 'product_gallery_effect',
				'css' 		=> 'width:120px;',
				'default'	=> 'slide-vert',
				'type' 		=> 'select',
				'options' => array( 
					'none'  			=> __( 'None', 'wp-e-commerce-dynamic-gallery' ),
					'fade'				=> __( 'Fade', 'wp-e-commerce-dynamic-gallery' ),
					'slide-hori'		=> __( 'Slide Hori', 'wp-e-commerce-dynamic-gallery' ),
					'slide-vert'		=> __( 'Slide Vert', 'wp-e-commerce-dynamic-gallery' ),
					'resize'			=> __( 'Resize', 'wp-e-commerce-dynamic-gallery' ),
				),
			),
			array(  
				'name' => __( 'Time between transitions', 'wp-e-commerce-dynamic-gallery' ),
				'desc' 		=> 'seconds',
				'id' 		=> 'product_gallery_speed',
				'type' 		=> 'slider',
				'default'	=> 5,
				'min'		=> 1,
				'max'		=> 10,
				'increment'	=> 1,
			),
			array(  
				'name' => __( 'Transition effect speed', 'wp-e-commerce-dynamic-gallery' ),
				'desc' 		=> 'seconds',
				'id' 		=> 'product_gallery_animation_speed',
				'type' 		=> 'slider',
				'default'	=> 2,
				'min'		=> 1,
				'max'		=> 10,
				'increment'	=> 1,
			),
			
			array(  
				'name' 		=> __( 'Single Image Transition', 'wp-e-commerce-dynamic-gallery' ),
				'desc' 		=> __( 'YES to auto deactivate image transition effect when only 1 image is loaded to gallery.', 'wp-e-commerce-dynamic-gallery' ),
				'id' 		=> 'stop_scroll_1image',
				'default'	=> 'no',
				'type' 		=> 'onoff_checkbox',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'YES', 'wp-e-commerce-dynamic-gallery' ),
				'unchecked_label' 	=> __( 'NO', 'wp-e-commerce-dynamic-gallery' ),
			),
			
			array(	'name' => __('Gallery Style', 'wp-e-commerce-dynamic-gallery' ), 'type' => 'heading'),
			array(  
				'name' => __( 'Image background colour', 'wp-e-commerce-dynamic-gallery' ),
				'desc' 		=> __( 'Gallery image background colour. Default <code>#FFFFFF</code>.', 'wp-e-commerce-dynamic-gallery' ),
				'id' 		=> 'bg_image_wrapper',
				'type' 		=> 'color',
				'default'	=> '#FFFFFF'
			),
			array(  
				'name' => __( 'Border colour', 'wp-e-commerce-dynamic-gallery' ),
				'desc' 		=> __( 'Gallery border colour. Default <code>#CCCCCC</code>.', 'wp-e-commerce-dynamic-gallery' ),
				'id' 		=> 'border_image_wrapper_color',
				'type' 		=> 'color',
				'default'	=> '#CCCCCC'
			),
			
			array(	'name' => __('Caption text', 'wp-e-commerce-dynamic-gallery' ), 'type' => 'heading'),
			array(  
				'name' 		=> __( 'Font', 'wp-e-commerce-dynamic-gallery' ),
				'id' 		=> 'caption_font',
				'type' 		=> 'typography',
				'default'	=> array( 'size' => '12px', 'face' => 'Arial, sans-serif', 'style' => 'normal', 'color' => '#FFFFFF' )
			),
			array(  
				'name' => __( 'Background', 'wp-e-commerce-dynamic-gallery' ),
				'desc' 		=> __( 'Caption text background colour. Default [default_value]', 'wp-e-commerce-dynamic-gallery' ),
				'id' 		=> 'product_gallery_bg_des',
				'type' 		=> 'color',
				'default'	=> '#000000'
			),
			
			array(	'name' => __('Nav Bar', 'wp-e-commerce-dynamic-gallery' ), 'type' => 'heading'),
			array(  
				'name' 		=> __( 'Control', 'wp-e-commerce-dynamic-gallery' ),
				'desc' 		=> __( 'YES to enable Nav bar Control', 'wp-e-commerce-dynamic-gallery' ),
				'class'		=> 'gallery_nav_control',
				'id' 		=> 'product_gallery_nav',
				'default'	=> 'yes',
				'type' 		=> 'onoff_checkbox',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'YES', 'wp-e-commerce-dynamic-gallery' ),
				'unchecked_label' 	=> __( 'NO', 'wp-e-commerce-dynamic-gallery' ),
			),
			
			array(
				'type' 		=> 'heading',
				'class'		=> 'nav_bar_container',
			),
			array(  
				'name' 		=> __( 'Font', 'wp-e-commerce-dynamic-gallery' ),
				'id' 		=> 'navbar_font',
				'type' 		=> 'typography',
				'default'	=> array( 'size' => '12px', 'face' => 'Arial, sans-serif', 'style' => 'normal', 'color' => '#000000' )
			),
			array(  
				'name' => __( 'Background', 'wp-e-commerce-dynamic-gallery' ),
				'desc' 		=> __( 'Nav bar background colour. Default [default_value].', 'wp-e-commerce-dynamic-gallery' ),
				'id' 		=> 'bg_nav_color',
				'type' 		=> 'color',
				'default'	=> '#FFFFFF'
			),
			array(  
				'name' 		=> __( 'Container height', 'wp-e-commerce-dynamic-gallery' ),
				'desc'		=> 'px',
				'class'		=> 'wpsc_dgallery_navbar_height',
				'id' 		=> 'navbar_height',
				'type' 		=> 'text',
				'default'	=> 25,
				'css' 		=> 'width:40px;',
			),
			
			array(
            	'name' 		=> __('Lazy-load scroll', 'wp-e-commerce-dynamic-gallery' ),
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'Control', 'wp-e-commerce-dynamic-gallery' ),
				'desc' 		=> __( 'YES to enable lazy-load scroll', 'wp-e-commerce-dynamic-gallery' ),
				'class'		=> 'lazy_load_control',
				'id' 		=> 'lazy_load_scroll',
				'default'	=> 'yes',
				'type' 		=> 'onoff_checkbox',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'YES', 'wp-e-commerce-dynamic-gallery' ),
				'unchecked_label' 	=> __( 'NO', 'wp-e-commerce-dynamic-gallery' ),
			),
			
			array(
				'type' 		=> 'heading',
				'class'		=> 'lazy_load_container',
			),
			array(  
				'name' => __( 'Colour', 'wp-e-commerce-dynamic-gallery' ),
				'desc' 		=> __( 'Scroll bar colour. Default [default_value].', 'wp-e-commerce-dynamic-gallery' ),
				'id' 		=> 'transition_scroll_bar',
				'type' 		=> 'color',
				'default'	=> '#000000'
			),
			
			array(	'name' => __('Variations Galleries', 'wp-e-commerce-dynamic-gallery' ), 'type' => 'heading'),
			array(  
				'name' => __( 'Gallery load effect', 'wp-e-commerce-dynamic-gallery' ),
				'desc' 		=> '',
				'class'		=> 'variation_gallery_effect',
				'id' 		=> 'variation_gallery_effect',
				'default'	=> 'fade',
				'type' 		=> 'switcher_checkbox',
				'checked_value'		=> 'fade',
				'unchecked_value'	=> 'none',
				'checked_label'		=> __( 'FADE', 'wp-e-commerce-dynamic-gallery' ),
				'unchecked_label' 	=> __( 'DEFAULT', 'wp-e-commerce-dynamic-gallery' ),
			),
			array(	
				'type' 		=> 'heading',
				'class'		=> 'variation_load_effect_timing',
			),
			array(  
				'name' 		=> __( 'Load effect timing', 'wp-e-commerce-dynamic-gallery' ),
				'desc' 		=> 'seconds',
				'id' 		=> 'variation_gallery_effect_speed',
				'type' 		=> 'slider',
				'default'	=> 2,
				'min'		=> 1,
				'max'		=> 10,
				'increment'	=> 1,
			),
		
        ));
	}
	
	public function include_script() {
		add_action('admin_footer', array($this, 'wpsc_dynamic_gallery_add_script'), 10);
	?>
<script>
(function($) {
$(document).ready(function() {
	if ( $("input.gallery_width_type:checked").val() == '%') {
		$(".gallery_width_type_percent").show();
		$(".gallery_width_type_fixed").hide();
	} else {
		$(".gallery_width_type_percent").hide();
		$(".gallery_width_type_fixed").show();
	}
	if ( $("input.gallery_nav_control:checked").val() == 'yes') {
		$('.nav_bar_container').css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
	} else {
		$('.nav_bar_container').css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden'} );
	}
	if ( $("input.lazy_load_control:checked").val() == 'yes') {
		$(".lazy_load_container").show();
	} else {
		$(".lazy_load_container").hide();
	}
	if ( $("input.variation_gallery_effect:checked").val() == 'fade') {
		$(".variation_load_effect_timing").show();
	} else {
		$(".variation_load_effect_timing").hide();
	}
	
	$(document).on( "a3rev-ui-onoff_checkbox-switch", '.gallery_width_type', function( event, value, status ) {
		if ( status == 'true' ) {
			$(".gallery_width_type_percent").slideDown();
			$(".gallery_width_type_fixed").slideUp();
		} else {
			$(".gallery_width_type_percent").slideUp();
			$(".gallery_width_type_fixed").slideDown();
		}
	});
	
	$(document).on( "a3rev-ui-onoff_checkbox-switch", '.gallery_nav_control', function( event, value, status ) {
		$('.nav_bar_container').hide().css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
		if ( status == 'true' ) {
			$(".nav_bar_container").slideDown();
		} else {
			$(".nav_bar_container").slideUp();
		}
	});
	
	$(document).on( "a3rev-ui-onoff_checkbox-switch", '.lazy_load_control', function( event, value, status ) {
		if ( status == 'true' ) {
			$(".lazy_load_container").slideDown();
		} else {
			$(".lazy_load_container").slideUp();
		}
	});
	
	$(document).on( "a3rev-ui-onoff_checkbox-switch", '.variation_gallery_effect', function( event, value, status ) {
		if ( status == 'true' ) {
			$(".variation_load_effect_timing").slideDown();
		} else {
			$(".variation_load_effect_timing").slideUp();
		}
	});
});
})(jQuery);
</script>
    <?php	
	}
	
	public function wpsc_dynamic_gallery_add_script(){
		global $woocommerce;
		$current_db_version = get_option( 'woocommerce_db_version', null );
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );
		wp_register_script( 'dynamic-gallery-script', WPSC_DYNAMIC_GALLERY_JS_URL.'/galleries.js' );
		wp_enqueue_script( 'dynamic-gallery-script' );
		
		wp_enqueue_style( 'ad-gallery-style', WPSC_DYNAMIC_GALLERY_JS_URL . '/mygallery/jquery.ad-gallery.css' );
		wp_enqueue_script( 'ad-gallery-script', WPSC_DYNAMIC_GALLERY_JS_URL . '/mygallery/jquery.ad-gallery.js', array(), false, true );
		
		wp_enqueue_style( 'a3_colorbox_style', WPSC_DYNAMIC_GALLERY_JS_URL . '/colorbox/colorbox.css' );
		wp_enqueue_script( 'colorbox_script', WPSC_DYNAMIC_GALLERY_JS_URL . '/colorbox/jquery.colorbox'.$suffix.'.js', array(), false, true );
			
		wp_enqueue_style( 'woocommerce_fancybox_styles', WPSC_DYNAMIC_GALLERY_JS_URL . '/fancybox/fancybox.css' );
		wp_enqueue_script( 'fancybox', WPSC_DYNAMIC_GALLERY_JS_URL . '/fancybox/fancybox'.$suffix.'.js', array(), false, true );
		
	}
}

global $wpsc_dgallery_style_settings;
$wpsc_dgallery_style_settings = new WPSC_Dynamic_Gallery_Style_Settings();

/** 
 * wpsc_dgallery_style_settings_form()
 * Define the callback function to show subtab content
 */
function wpsc_dgallery_style_settings_form() {
	global $wpsc_dgallery_style_settings;
	$wpsc_dgallery_style_settings->settings_form();
}

?>

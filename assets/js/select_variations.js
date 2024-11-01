jQuery(document).ready(function($) {
		
		var gallery_container = $('.gallery_container');
		
		var old_gallery = gallery_container.html();
		
		var galleryHeight = gallery_container.height();
		var galleryWidth = gallery_container.width();
		
		var show_old_gallery = false;
				
		count_length = function(obj) {
			var L=0;
			$.each(obj, function(i, elem) {
				L++;
			});
			return L;
		}
		
		$(document).on("change", ".wpsc_select_variation", function(e) {		
			if (e.originalEvent !== undefined) {
				var eventtrigger = "accept";
			} else {
				if($(this).attr('value') == "" && !$(this).hasClass('triggered')) {
					var eventtrigger = "no-accept";
					$(this).addClass('triggered');
				} else {
					var eventtrigger = "accept";
				}
			}
			
			if(eventtrigger == 'accept') {
				
				var parent_form = $(this).closest("form.product_form");
				if ( parent_form.length == 0 )
					return;
				
				var variations = {};
				parent_form.find('.wpsc_select_variation').each(function(){
					if( parseInt( $(this).val() ) > 0) {
						variations[$(this).attr('name')] = parseInt( $(this).val() );
					}
				});
				
				if( count_length(variations) > 0 ) { // If we have some variation data, show the associated images
					/*gallery_container.html('');
					gallery_container.height(galleryHeight);
					gallery_container.width(galleryWidth);
					gallery_container.addClass('gallery_loading');*/
					
					var form_values =$(".wpsc_select_variation",parent_form).serializeArray( );
					
					var data = {
						action: 		"wpsc_dgallery_change_variation",
						product_id: 	vars.product_id,
						variations:		form_values,
						security: 		vars.security
					};
					$.get( vars.ajax_url, data, function(response) {
						gallery_container.removeClass('gallery_loading');
						gallery_container.css({'height':'auto', 'width':'auto'});
						response = $.parseJSON( response );
						if ( vars.variation_gallery_effect == 'fade' ) {
							gallery_container.fadeTo( parseInt( vars.variation_gallery_effect_speed ), 0 , function() {
								gallery_container.html(response);
							}).fadeTo( parseInt( vars.variation_gallery_effect_speed ), 1 );
						} else {
							gallery_container.html(response);	
						}
					});
					
				} else { // If we dont have any variation data, reset it back to the default images.
					
					show_old_gallery = false;
					clearselection();
					
				}
						
			} // End if('accept')
				
		});	
		
		function clearselection(){
			/*gallery_container.html('');
			gallery_container.height(galleryHeight);
			gallery_container.width(galleryWidth);
			gallery_container.addClass('gallery_loading');*/
			
				if ( show_old_gallery == false ) {
					
					var data = {
						action: 		"wpsc_dgallery_change_variation",
						product_id: 	vars.product_id,
						variations: 	'',
						security: 		vars.security
					};
					
					$.get( vars.ajax_url, data, function(response) {
						gallery_container.removeClass('gallery_loading');
						gallery_container.css({'height':'auto', 'width':'auto'});
						response = $.parseJSON( response );
						if ( vars.variation_gallery_effect == 'fade' ) {
							gallery_container.fadeTo( parseInt( vars.variation_gallery_effect_speed ), 0 , function() {
								gallery_container.html(response);
							}).fadeTo( parseInt( vars.variation_gallery_effect_speed ), 1 );
						} else {
							gallery_container.html(response);	
						}
					});
					show_old_gallery = true;
				}
		}
});
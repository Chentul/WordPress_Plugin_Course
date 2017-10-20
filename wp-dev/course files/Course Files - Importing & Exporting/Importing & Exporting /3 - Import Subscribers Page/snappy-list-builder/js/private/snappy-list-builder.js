// wait until the page and jQuery have loaded before running the code below
jQuery(document).ready(function($){
	
	// stop our admin menus from collapsing
	if( $('body[class*=" slb_"]').length || $('body[class*=" post-type-slb_"]').length ) {

		$slb_menu_li = $('#toplevel_page_slb_dashboard_admin_page');
		
		$slb_menu_li
		.removeClass('wp-not-current-submenu')
		.addClass('wp-has-current-submenu')
		.addClass('wp-menu-open');
		
		$('a:first',$slb_menu_li)
		.removeClass('wp-not-current-submenu')
		.addClass('wp-has-submenu')
		.addClass('wp-has-current-submenu')
		.addClass('wp-menu-open');
		
	}
	
	// wp uploader
	// this adds WordPress's file uploader to specially formatted html div.wp-uploader
	// here's an example of what the html should look like this... 
	/*
	<div class="wp-uploader">
	    <input type="text" name="input_name" class="file-url regular-text" accept="jpg|gif">
	    <input type="hidden" name="input_name" class="file-id" value="0" />
	    <input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload">
	</div>	
	*/
	$('.wp-uploader').each(function(){
		
		$uploader = $(this);
		
		$('.upload-btn',$uploader).click(function(e) {
	        e.preventDefault();
	        var file = wp.media({ 
	            title: 'Upload',
	            // mutiple: true if you want to upload multiple files at once
	            multiple: false
	        }).open()
	        .on('select', function(e){
	            // This will return the selected image from the Media Uploader, the result is an object
	            var uploaded_file = file.state().get('selection').first();
	            // We convert uploaded_image to a JSON object to make accessing it easier
	            // Output to the console uploaded_image
	            var file_url = uploaded_file.attributes.url;
	            var file_id = uploaded_file.id;
	            
				if( $('.file-url',$uploader).attr('accept') !== undefined ) {
					
					
					var filetype = $('.file-url',$uploader).attr('accept');
					
					if( filetype !== uploaded_file.attributes.subtype ) {
						
						
						$('.upload-text',$uploader).val('');
						
						alert('The file must be of type: '+ filetype);
						
						
					} else {
	            
			            // Let's assign the url value to the input field
			            $('.file-url',$uploader).val(file_url).trigger('change');
			            $('.file-id',$uploader).val(file_id).trigger('change');
						
					}
					
				}
	            
	        });
	    });
    });
    
    // setup variables to store our import forms jQuery objects
    $import_form_1 = $('#import_form_1','#import_subscribers');
	$import_form_2 = $('#import_form_2','#import_subscribers');
	
	// this event triggered when import_form_1 file is selected
	$('.file-id',$import_form_1).bind('change',function(){
		
		alert( 'a csv file has been added successfully' );
		
		// get the form data and serialize it
		var form_1_data = $import_form_1.serialize();
		
		
	});

	
});
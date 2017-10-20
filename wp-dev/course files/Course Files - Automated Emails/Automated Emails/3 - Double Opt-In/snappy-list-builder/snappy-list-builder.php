<?php
	
/*
Plugin Name: Snappy List Builder
Plugin URI: http://wordpressplugincourse.com/plugins/snappy-list-builder
Description: The ultimate email list building plugin for WordPress. Capture new subscribers. Reward subscribers with a custom download upon opt-in. Build unlimited lists. Import and export subscribers easily with .csv
Version: 1.0
Author: Joel Funk @ Code College
Author URI: http://joelfunk.codecollege.ca
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: snappy-list-builder
*/


/* !0. TABLE OF CONTENTS */

/*
	
	1. HOOKS
		1.1 - registers all our custom shortcodes
		1.2 - register custom admin column headers
		1.3 - register custom admin column data
		1.4 - register ajax actions
		1.5 - load external files to public website
		1.6 - Advanced Custom Fields Settings
		1.7 - register our custom menus
		1.8 - load external files in WordPress admin
		1.9 - register plugin options
	
	2. SHORTCODES
		2.1 - slb_register_shortcodes()
		2.2 - slb_form_shortcode()
		2.3 - slb_manage_subscriptions_shortcode()
		
	3. FILTERS
		3.1 - slb_subscriber_column_headers()
		3.2 - slb_subscriber_column_data()
		3.3 - slb_list_column_headers()
		3.4 - slb_list_column_data()
		3.5 - slb_admin_menus()
		3.6 - slb_unsubscribe()
		3.7 - slb_remove_subscription()
		
	4. EXTERNAL SCRIPTS
		4.1 - Include ACF
		4.2 - slb_public_scripts()
		
	5. ACTIONS
		5.1 - slb_save_subscription()
		5.2 - slb_save_subscriber()
		5.3 - slb_add_subscription()
		
	6. HELPERS
		6.1 - slb_has_subscriptions()
		6.2 - slb_get_subscriber_id()
		6.3 - slb_get_subscritions()
		6.4 - slb_return_json()
		6.5 - slb_get_acf_key()
		6.6 - slb_get_subscriber_data()
		6.7 - slb_get_page_select()
		6.8 - slb_get_default_page_options()
		6.9 - slb_get_option()
		6.10 - slb_get_current_options()
		6.11 - slb_get_manage_subscriptions_html()
		
	7. CUSTOM POST TYPES
		7.1 - subscribers
		7.2 - lists
	
	8. ADMIN PAGES
		8.1 - slb_dashboard_admin_page()
		8.2 - slb_import_admin_page()
		8.3 - slb_options_admin_page()
	
	9. SETTINGS
		9.1 - slb_register_options()

*/




/* !1. HOOKS */

// 1.1
// hint: registers all our custom shortcodes on init
add_action('init', 'slb_register_shortcodes');

// 1.2
// hint: register custom admin column headers
add_filter('manage_edit-slb_subscriber_columns','slb_subscriber_column_headers');
add_filter('manage_edit-slb_list_columns','slb_list_column_headers');

// 1.3
// hint: register custom admin column data
add_filter('manage_slb_subscriber_posts_custom_column','slb_subscriber_column_data',1,2);
add_filter('manage_slb_list_posts_custom_column','slb_list_column_data',1,2);

// 1.4
// hint: register ajax actions
add_action('wp_ajax_nopriv_slb_save_subscription', 'slb_save_subscription'); // regular website visitor
add_action('wp_ajax_slb_save_subscription', 'slb_save_subscription'); // admin user
add_action('wp_ajax_nopriv_slb_unsubscribe', 'slb_unsubscribe'); // regular website visitor
add_action('wp_ajax_slb_unsubscribe', 'slb_unsubscribe'); // admin user

// 1.5
// load external files to public website
add_action('wp_enqueue_scripts', 'slb_public_scripts');

// 1.6
// Advanced Custom Fields Settings
add_filter('acf/settings/path', 'slb_acf_settings_path');
add_filter('acf/settings/dir', 'slb_acf_settings_dir');
add_filter('acf/settings/show_admin', 'slb_acf_show_admin');
if( !defined('ACF_LITE') ) define('ACF_LITE',true); // turn off ACF plugin menu

// 1.7 
// hint: register our custom menus
add_action('admin_menu', 'slb_admin_menus');

// 1.8
// hint: load external files in WordPress admin
add_action('admin_enqueue_scripts', 'slb_admin_scripts');

// 1.9
// register plugin options
add_action('admin_init', 'slb_register_options');



/* !2. SHORTCODES */

// 2.1
// hint: registers all our custom shortcodes
function slb_register_shortcodes() {
	
	add_shortcode('slb_form', 'slb_form_shortcode');
	add_shortcode('slb_manage_subscriptions', 'slb_manage_subscriptions_shortcode');
	add_shortcode('slb_confirm_subscription','slb_confirm_subscription_shortcode');
	
}

// 2.2
// hint: returns a html string for a email capture form
function slb_form_shortcode( $args, $content="") {
	
	// get the list id
	$list_id = 0;
	if( isset($args['id']) ) $list_id = (int)$args['id'];
	
	// title
	$title = '';
	if( isset($args['title']) ) $title = (string)$args['title'];
	
	// setup our output variable - the form html 
	$output = '
	
		<div class="slb">
		
			<form id="slb_register_form" name="slb_form" class="slb-form" method="post"
			action="/wp-admin/admin-ajax.php?action=slb_save_subscription" method="post">
			
				<input type="hidden" name="slb_list" value="'. $list_id .'">';
				
				
				if( strlen($title) ):
				
					$output .= '<h3 class="slb-title">'. $title .'</h3>';
				
				endif;
			
				$output .='<p class="slb-input-container">
				
					<label>Your Name</label><br />
					<input type="text" name="slb_fname" placeholder="First Name" />
					<input type="text" name="slb_lname" placeholder="Last Name" />
				
				</p>
				
				<p class="slb-input-container">
				
					<label>Your Email</label><br />
					<input type="email" name="slb_email" placeholder="ex. you@email.com" />
				
				</p>';
				
				// including content in our form html if content is passed into the function
				if( strlen($content) ):
				
					$output .= '<div class="slb-content">'. wpautop($content) .'</div>';
				
				endif;
				
				// completing our form html
				$output .= '<p class="slb-input-container">
				
					<input type="submit" name="slb_submit" value="Sign Me Up!" />
				
				</p>
			
			</form>
		
		</div>
	
	';
	
	// return our results/html
	return $output;
	
}

// 2.3
// hint: displays a form for managing the users list subscriptions
// example: [slb_manage_subscriptions]
function slb_manage_subscriptions_shortcode( $args, $content="" ) {
	
	// setup our return string
	$output = '<div class="slb slb-manage-subscriptions">';
	
	try {
		
		// get the email address from the URL
		$email = ( isset( $_GET['email'] ) ) ? esc_attr( $_GET['email'] ) : '';
		
		// get the subscriber id from the email address
		$subscriber_id = slb_get_subscriber_id( $email );
		
		// get subscriber data 
		$subscriber_data = slb_get_subscriber_data( $subscriber_id );
		
		// IF subscriber exists
		if( $subscriber_id ):
		
			// get subscriptions html
			$output = slb_get_manage_subscriptions_html( $subscriber_id );
			
		else:
		
			// invalid link
			$output .= '<p>This link is invalid.</p>';
		
		endif;
	
	
	} catch(Exception $e) {
		
		// php error
		
	}
	
	// close our html div tag
	$output .= '</div>';
	
	// return our html
	return $output;
	
}

// 2.4
// hint: displays subscription opt-in confirmation text and link to manage sunscriptions
// example: [slb_confirm_subscription]
function slb_confirm_subscription_shortcode( $args, $content="" ) {
	
	// setup output variable 
	$output = '<div class="slb">';
	
	// setup email and list_id variables and handle if they are not defined in the GET scope
	$email = ( isset( $_GET['email'] ) ) ? esc_attr( $_GET['email'] ) : '';
	$list_id = ( isset( $_GET['list'] ) ) ? esc_attr( $_GET['list'] ) : 0;
	
	// get subscriber id from email
	$subscriber_id = slb_get_subscriber_id( $email );
	$subscriber = get_post( $subscriber_id );
	
	// IF we found a subscriber matching that email address
	if( $subscriber_id && slb_validate_subscriber( $subscriber ) ):
	
		// get list object
		$list = get_post( $list_id );
		
		// IF list and subscriber are valid
		if( slb_validate_list( $list ) ):
		
		
			// IF subscriptions has not yet been added
			if( !slb_subscriber_has_subscription( $subscriber_id, $list_id) ):
				
				// complete opt-in
				$optin_complete = slb_confirm_subscription( $subscriber_id, $list_id );
				
				if( !$optin_complete ):
				
					$output .= slb_get_message_html('Due to an unknown error, we were unable to confirm your subscription.', 'error');
					$output .= '</div>';
					
					return $output;
				
				endif;
		
			endif;
		
			// get confirmation message html and append it to output
			$output .= slb_get_message_html( 'Your subscription to '. $list->post_title .' has now been confirmed.', 'confirmation' );
			
			// get manage subscriptions link
			$manage_subscriptions_link = slb_get_manage_subscriptions_link( $email );
			
			// append link to output
			$output .= '<p><a href="'. $manage_subscriptions_link .'">Click here to manage your subscriptions.</a></p>';
		
		else:
		
			$output .= slb_get_message_html( 'This link is invalid.', 'error');
		
		endif;
	
	else: 
	
		$output .= slb_get_message_html( 'This link is invalid. Invalid Subscriber '. $email .'.', 'error');
	
	endif;
	
	// close .slb div
	$output .= '</div>';
	
	// return output html
	return $output;
	
}





/* !3. FILTERS */

// 3.1
function slb_subscriber_column_headers( $columns ) {
	
	// creating custom column header data
	$columns = array(
		'cb'=>'<input type="checkbox" />',
		'title'=>__('Subscriber Name'),
		'email'=>__('Email Address'),	
	);
	
	// returning new columns
	return $columns;
	
}

// 3.2
function slb_subscriber_column_data( $column, $post_id ) {
	
	// setup our return text
	$output = '';
	
	switch( $column ) {
		
		case 'name':
			// get the custom name data
			$fname = get_field('slb_fname', $post_id );
			$lname = get_field('slb_lname', $post_id );
			$output .= $fname .' '. $lname;
			break;
		case 'email':
			// get the custom email data
			$email = get_field('slb_email', $post_id );
			$output .= $email;
			break;
		
	}
	
	// echo the output
	echo $output;
	
}

// 3.3
function slb_list_column_headers( $columns ) {
	
	// creating custom column header data
	$columns = array(
		'cb'=>'<input type="checkbox" />',
		'title'=>__('List Name'),
		'shortcode'=>__('Shortcode'),	
	);
	
	// returning new columns
	return $columns;
	
}

// 3.4
function slb_list_column_data( $column, $post_id ) {
	
	// setup our return text
	$output = '';
	
	switch( $column ) {
		
		case 'shortcode':
			$output .= '[slb_form id="'. $post_id .'"]';
			break;
		
	}
	
	// echo the output
	echo $output;
	
}

// 3.5
// hint: registers custom plugin admin menus
function slb_admin_menus() {
	
	/* main menu */
	
		$top_menu_item = 'slb_dashboard_admin_page';
	    
	    add_menu_page( '', 'List Builder', 'manage_options', 'slb_dashboard_admin_page', 'slb_dashboard_admin_page', 'dashicons-email-alt' );
    
    /* submenu items */
    
	    // dashboard
	    add_submenu_page( $top_menu_item, '', 'Dashboard', 'manage_options', $top_menu_item, $top_menu_item );
	    
	    // email lists
	    add_submenu_page( $top_menu_item, '', 'Email Lists', 'manage_options', 'edit.php?post_type=slb_list' );
	    
	    // subscribers
	    add_submenu_page( $top_menu_item, '', 'Subscribers', 'manage_options', 'edit.php?post_type=slb_subscriber' );
	    
	    // import subscribers
	    add_submenu_page( $top_menu_item, '', 'Import Subscribers', 'manage_options', 'slb_import_admin_page', 'slb_import_admin_page' );
	    
	    // plugin options
	    add_submenu_page( $top_menu_item, '', 'Plugin Options', 'manage_options', 'slb_options_admin_page', 'slb_options_admin_page' );

}





/* !4. EXTERNAL SCRIPTS */

// 4.1
// Include ACF
include_once( plugin_dir_path( __FILE__ ) .'lib/advanced-custom-fields/acf.php' );

// 4.2
// hint: loads external files into PUBLIC website
function slb_public_scripts() {
	
	// register scripts with WordPress's internal library
	wp_register_script('snappy-list-builder-js-public', plugins_url('/js/public/snappy-list-builder.js',__FILE__), array('jquery'),'',true);
	wp_register_style('snappy-list-builder-css-public', plugins_url('/css/public/snappy-list-builder.css',__FILE__));
	
	// add to que of scripts that get loaded into every page
	wp_enqueue_script('snappy-list-builder-js-public');
	wp_enqueue_style('snappy-list-builder-css-public');
	
}

// 4.3
// hint: loads external files into wordpress ADMIN
function slb_admin_scripts() {
	
	// register scripts with WordPress's internal library
	wp_register_script('snappy-list-builder-js-private', plugins_url('/js/private/snappy-list-builder.js',__FILE__), array('jquery'),'',true);
	
	// add to que of scripts that get loaded into every admin page
	wp_enqueue_script('snappy-list-builder-js-private');
	
}





/* !5. ACTIONS */

// 5.1
// hint: saves subscription data to an existing or new subscriber
function slb_save_subscription() {
	
	// setup default result data
	$result = array(
		'status' => 0,
		'message' => 'Subscription was not saved. ',
		'error'=>'',
		'errors'=>array()
	);
	
	try {
		
		// get list_id
		$list_id = (int)$_POST['slb_list'];
	
		// prepare subscriber data
		$subscriber_data = array(
			'fname'=> esc_attr( $_POST['slb_fname'] ),
			'lname'=> esc_attr( $_POST['slb_lname'] ),
			'email'=> esc_attr( $_POST['slb_email'] ),
		);
		
		// setup our errors array
		$errors = array();
		
		// form validation
		if( !strlen( $subscriber_data['fname'] ) ) $errors['fname'] = 'First name is required.';
		if( !strlen( $subscriber_data['email'] ) ) $errors['email'] = 'Email address is required.';
		if( strlen( $subscriber_data['email'] ) && !is_email( $subscriber_data['email'] ) ) $errors['email'] = 'Email address must be valid.';
		
		// IF there are errors
		if( count($errors) ):
		
			// append errors to result structure for later use
			$result['error'] = 'Some fields are still required. ';
			$result['errors'] = $errors;
		
		else: 
		// IF there are no errors, proceed...
		
			// attempt to create/save subscriber
			$subscriber_id = slb_save_subscriber( $subscriber_data );
			
			// IF subscriber was saved successfully $subscriber_id will be greater than 0
			if( $subscriber_id ):
			
				// IF subscriber already has this subscription
				if( slb_subscriber_has_subscription( $subscriber_id, $list_id ) ):
				
					// get list object
					$list = get_post( $list_id );
					
					// return detailed error
					$result['error'] = esc_attr( $subscriber_data['email'] .' is already subscribed to '. $list->post_title .'.');
					
				else: 
					
					// send new subscriber a confirmation email, returns true if we were successful
					$email_sent = slb_send_subscriber_email( $subscriber_id, 'new_subscription', $list_id);
					
					// IF email was sent
					if( !$email_sent ):
					
						// email could not be sent
						$result['error'] = 'Unable to send email. ';
					
					else:
					
						// email sent and subscription saved!
						$result['status']=1;
						$result['message']='Success! A confirmation email has been sent to '. $subscriber_data['email'];
					
						// clean up: remove our empty error
						unset( $result['error'] );
					
					endif;
				
				endif;
			
			endif;
		
		endif;
		
	} catch ( Exception $e ) {
		
	}
	
	// return result as json
	slb_return_json($result);
	
}

// 5.2
// hint: creates a new subscriber or updates and existing one
function slb_save_subscriber( $subscriber_data ) {
	
	// setup default subscriber id
	// 0 means the subscriber was not saved
	$subscriber_id = 0;
	
	try {
		
		$subscriber_id = slb_get_subscriber_id( $subscriber_data['email'] );
		
		// IF the subscriber does not already exists...
		if( !$subscriber_id ):
		
			// add new subscriber to database	
			$subscriber_id = wp_insert_post( 
				array(
					'post_type'=>'slb_subscriber',
					'post_title'=>$subscriber_data['fname'] .' '. $subscriber_data['lname'],
					'post_status'=>'publish',
				), 
				true
			);
		
		endif;
		
		// add/update custom meta data
		update_field(slb_get_acf_key('slb_fname'), $subscriber_data['fname'], $subscriber_id);
		update_field(slb_get_acf_key('slb_lname'), $subscriber_data['lname'], $subscriber_id);
		update_field(slb_get_acf_key('slb_email'), $subscriber_data['email'], $subscriber_id);
		
	} catch( Exception $e ) {
		
		// a php error occurred
		
	}
	
	// return subscriber_id
	return $subscriber_id;
	
}

// 5.3
// hint: adds list to subscribers subscriptions
function slb_add_subscription( $subscriber_id, $list_id ) {
	
	// setup default return value
	$subscription_saved = false;
	
	// IF the subscriber does NOT have the current list subscription
	if( !slb_subscriber_has_subscription( $subscriber_id, $list_id ) ):
	
		// get subscriptions and append new $list_id
		$subscriptions = slb_get_subscriptions( $subscriber_id );
		$subscriptions[]=$list_id;
		
		// update slb_subscriptions
		update_field( slb_get_acf_key('slb_subscriptions'), $subscriptions, $subscriber_id );
		
		// subscriptions updated!
		$subscription_saved = true;
	
	endif;
	
	// return result
	return $subscription_saved;
	
}

// 5.4
// hint: removes one or more subscriptions from a subscriber and notifies them via email
// this function is a ajax form handler...
// expects form post data: $_POST['subscriber_id'] and $_POST['list_id']
function slb_unsubscribe() {
	
	// setup default result data
	$result = array(
		'status' => 0,
		'message' => 'Subscriptions were NOT updated. ',
		'error' => '',
		'errors' => array(),
	);
	
	$subscriber_id = ( isset($_POST['subscriber_id']) ) ? esc_attr( (int)$_POST['subscriber_id'] ) : 0;
	$list_ids = ( isset($_POST['list_ids']) ) ? $_POST['list_ids'] : 0;
	
	try {
		
		// if there are lists to remove
		if( is_array($list_ids) ):
	
			// loop over lists to remove
			foreach( $list_ids as &$list_id ):
			
				// remove this subscription
				slb_remove_subscription( $subscriber_id, $list_id );
			
			endforeach;
		
		endif;
		
		// setup success status and message
		$result['status']=1;
		$result['message']='Subscriptions updated. ';
		
		// get the updated list of subscriptions as html
		$result['html']= slb_get_manage_subscriptions_html( $subscriber_id );
	
	} catch( Exception $e ) {
		
		// php error
		
	}
	
	// return result as json
	slb_return_json( $result );
	
}

// 5.5
// hint: removes a single subscription from a subscriber
function slb_remove_subscription( $subscriber_id, $list_id ) {
	
	// setup default return value
	$subscription_saved = false;
	
	// IF the subscriber has the current list subscription
	if( slb_subscriber_has_subscription( $subscriber_id, $list_id ) ):
	
		// get current subscriptions 
		$subscriptions = slb_get_subscriptions( $subscriber_id );
		
		// get the position of the $list_id to remove
		$needle = array_search( $list_id, $subscriptions );
		
		// remove $list_id from $subscriptions array
		unset( $subscriptions[$needle] );
		
		// update slb_subscriptions
		update_field(slb_get_acf_key( 'slb_subscriptions'), $subscriptions, $subscriber_id);
		
		// subscriptions updated!
		$subscription_saved = true;
	
	endif;
	
	// return result
	return $subscription_saved;
	
}

// 5.6
// hint: sends a unqiue customized email to a subscriber
function slb_send_subscriber_email( $subscriber_id, $email_template_name, $list_id ) {
	
	// setup return variable
	$email_sent = false;
	
	// get email template data
	$email_template_object = slb_get_email_template( $subscriber_id, $email_template_name, $list_id );
	
	// IF email template data was found
	if( !empty( $email_template_object ) ):
	
		// get subscriber data
		$subscriber_data = slb_get_subscriber_data( $subscriber_id );
		
		// set wp_mail headers
		$wp_mail_headers = array('Content-Type: text/html; charset=UTF-8');
		
		// use wp_mail to send email
		$email_sent = wp_mail( array( $subscriber_data['email'] ) , $email_template_object['subject'], $email_template_object['body'], $wp_mail_headers );
	
	endif;
	
	return $email_sent;
	
}

// 5.7
// hint: adds subcription to database and emails subscriber confirmation email
function slb_confirm_subscription( $subscriber_id, $list_id ) {
	
	// setup return variable
	$optin_complete = false;
	
	// add new subscription
	$subscription_saved = slb_add_subscription( $subscriber_id, $list_id );
	
	// IF subscription was saved
	if( $subscription_saved ):
		
		// return true
		$optin_complete = true;
	
	endif;
	
	// return result
	return $optin_complete;
	
}









/* !6. HELPERS */

// 6.1
// hint: returns true or false
function slb_subscriber_has_subscription( $subscriber_id, $list_id ) {
	
	// setup default return value
	$has_subscription = false;
	
	// get subscriber
	$subscriber = get_post($subscriber_id);
	
	// get subscriptions
	$subscriptions = slb_get_subscriptions( $subscriber_id );
	
	// check subscriptions for $list_id
	if( in_array($list_id, $subscriptions) ):
	
		// found the $list_id in $subscriptions
		// this subscriber is already subscribed to this list
		$has_subscription = true;
	
	else:
	
		// did not find $list_id in $subscriptions
		// this subscriber is not yet subscribed to this list
	
	endif;
	
	return $has_subscription;
	
}

// 6.2
// hint: retrieves a subscriber_id from an email address
function slb_get_subscriber_id( $email ) {
	
	$subscriber_id = 0;
	
	try {
	
		// check if subscriber already exists
		$subscriber_query = new WP_Query( 
			array(
				'post_type'		=>	'slb_subscriber',
				'posts_per_page' => 1,
				'meta_key' => 'slb_email',
				'meta_query' => array(
				    array(
				        'key' => 'slb_email',
				        'value' => $email,  // or whatever it is you're using here
				        'compare' => '=',
				    ),
				),
			)
		);
		
		// IF the subscriber exists...
		if( $subscriber_query->have_posts() ):
		
			// get the subscriber_id
			$subscriber_query->the_post();
			$subscriber_id = get_the_ID();
			
		endif;
	
	} catch( Exception $e ) {
		
		// a php error occurred
		
	}
		
	// reset the Wordpress post object
	wp_reset_query();
	
	return (int)$subscriber_id;
	
}

// 6.3
// hint: returns an array of list_id's
function slb_get_subscriptions( $subscriber_id ) {
	
	$subscriptions = array();
	
	// get subscriptions (returns array of list objects)
	$lists = get_field( slb_get_acf_key('slb_subscriptions'), $subscriber_id );
	
	// IF $lists returns something
	if( $lists ):
	
		// IF $lists is an array and there is one or more items
		if( is_array($lists) && count($lists) ):
			// build subscriptions: array of list id's
			foreach( $lists as &$list):
				$subscriptions[]= (int)$list->ID;
			endforeach;
		elseif( is_numeric($lists) ):
			// single result returned
			$subscriptions[]= $lists;
		endif;
	
	endif;
	
	return (array)$subscriptions;
	
}

// 6.4
function slb_return_json( $php_array ) {
	
	// encode result as json string
	$json_result = json_encode( $php_array );
	
	// return result
	die( $json_result );
	
	// stop all other processing 
	exit;
	
}


//6.5
// hint: gets the unique act field key from the field name
function slb_get_acf_key( $field_name ) {
	
	$field_key = $field_name;
	
	switch( $field_name ) {
		
		case 'slb_fname':
			$field_key = 'field_55c8ec63416a2';
			break;
		case 'slb_lname':
			$field_key = 'field_55c8ec76416a3';
			break;
		case 'slb_email':
			$field_key = 'field_55c8ec87416a4';
			break;
		case 'slb_subscriptions':
			$field_key = 'field_55c8ecac416a5';
			break;
		
	}
	
	return $field_key;
	
}


// 6.6
// hint: returns an array of subscriber data including subscriptions
function slb_get_subscriber_data( $subscriber_id ) {
	
	// setup subscriber_data
	$subscriber_data = array();
	
	// get subscriber object
	$subscriber = get_post( $subscriber_id );
	
	// IF subscriber object is valid
	if( isset($subscriber->post_type) && $subscriber->post_type == 'slb_subscriber' ):
	
		$fname = get_field( slb_get_acf_key('slb_fname'), $subscriber_id);
		$lname = get_field( slb_get_acf_key('slb_lname'), $subscriber_id);
	
		// build subscriber_data for return
		$subscriber_data = array(
			'name'=> $fname .' '. $lname,
			'fname'=>$fname,
			'lname'=>$lname,
			'email'=>get_field( slb_get_acf_key('slb_email'), $subscriber_id),
			'subscriptions'=>slb_get_subscriptions( $subscriber_id )
		);
		
	
	endif;
	
	// return subscriber_data
	return $subscriber_data;
	
}

// 6.7
// hint: returns html for a page selector
function slb_get_page_select( $input_name="slb_page", $input_id="", $parent=-1, $value_field="id", $selected_value="" ) {
	
	// get WP pages
	$pages = get_pages( 
		array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
			'post_type' => 'page',
			'parent' => $parent,
			'status'=>array('draft','publish'),	
		)
	);
	
	// setup our select html
	$select = '<select name="'. $input_name .'" ';
	
	// IF $input_id was passed in
	if( strlen($input_id) ):
	
		// add an input id to our select html
		$select .= 'id="'. $input_id .'" ';
	
	endif;
	
	// setup our first select option
	$select .= '><option value="">- Select One -</option>';
	
	// loop over all the pages
	foreach ( $pages as &$page ): 
	
		// get the page id as our default option value
		$value = $page->ID;
		
		// determine which page attribute is the desired value field
		switch( $value_field ) {
			case 'slug':
				$value = $page->post_name;
				break;
			case 'url':
				$value = get_page_link( $page->ID );
				break;
			default:
				$value = $page->ID;
		}
		
		// check if this option is the currently selected option
		$selected = '';
		if( $selected_value == $value ):
			$selected = ' selected="selected" ';
		endif;
	
		// build our option html
		$option = '<option value="' . $value . '" '. $selected .'>';
		$option .= $page->post_title;
		$option .= '</option>';
		
		// append our option to the select html
		$select .= $option;
		
	endforeach;
	
	// close our select html tag
	$select .= '</select>';
	
	// return our new select 
	return $select;
	
}

// 6.8
// hint: returns default option values as an associative array
function slb_get_default_options() {
	
	$defaults = array();
	
	try {
		
		// get front page id
		$front_page_id = get_option('page_on_front');
	
		// setup default email footer
		$default_email_footer = '
			<p>
				Sincerely, <br /><br />
				The '. get_bloginfo('name') .' Team<br />
				<a href="'. get_bloginfo('url') .'">'. get_bloginfo('url') .'</a>
			</p>
		';
		
		// setup defaults array
		$defaults = array(
			'slb_manage_subscription_page_id'=>$front_page_id,
			'slb_confirmation_page_id'=>$front_page_id,
			'slb_reward_page_id'=>$front_page_id,
			'slb_default_email_footer'=>$default_email_footer,
			'slb_download_limit'=>3,
		);
	
	} catch( Exception $e) {
		
		// php error
		
	}
	
	// return defaults
	return $defaults;
	
	
}

// 6.9
// hint: returns the requested page option value or it's default
function slb_get_option( $option_name ) {
	
	// setup return variable
	$option_value = '';	
	
	
	try {
		
		// get default option values
		$defaults = slb_get_default_options();
		
		// get the requested option
		switch( $option_name ) {
			
			case 'slb_manage_subscription_page_id':
				// subscription page id
				$option_value = (get_option('slb_manage_subscription_page_id')) ? get_option('slb_manage_subscription_page_id') : $defaults['slb_manage_subscription_page_id'];
				break;
			case 'slb_confirmation_page_id':
				// confirmation page id
				$option_value = (get_option('slb_confirmation_page_id')) ? get_option('slb_confirmation_page_id') : $defaults['slb_confirmation_page_id'];
				break;
			case 'slb_reward_page_id':
				// reward page id
				$option_value = (get_option('slb_reward_page_id')) ? get_option('slb_reward_page_id') : $defaults['slb_reward_page_id'];
				break;
			case 'slb_default_email_footer':
				// email footer
				$option_value = (get_option('slb_default_email_footer')) ? get_option('slb_default_email_footer') : $defaults['slb_default_email_footer'];
				break;
			case 'slb_download_limit':
				// reward download limit
				$option_value = (get_option('slb_download_limit')) ? (int)get_option('slb_download_limit') : $defaults['slb_download_limit'];
				break;
			
		}
		
	} catch( Exception $e) {
		
		// php error
		
	}
	
	// return option value or it's default
	return $option_value;
	
}

// 6.10
// hint: get's the current options and returns values in associative array
function slb_get_current_options() {
	
	// setup our return variable
	$current_options = array();
	
	try {
	
		// build our current options associative array
		$current_options = array(
			'slb_manage_subscription_page_id' => slb_get_option('slb_manage_subscription_page_id'),
			'slb_confirmation_page_id' => slb_get_option('slb_confirmation_page_id'),
			'slb_reward_page_id' => slb_get_option('slb_reward_page_id'),
			'slb_default_email_footer' => slb_get_option('slb_default_email_footer'),
			'slb_download_limit' => slb_get_option('slb_download_limit'),
		);
	
	} catch( Exception $e ) {
		
		// php error
	
	}
	
	// return current options
	return $current_options;
	
}

// 6.11
// hint: generates an html form for managing subscriptions
function slb_get_manage_subscriptions_html( $subscriber_id ) {
	
	$output = '';
	
	try {
		
		// get array of list_ids for this subscriber
		$lists = slb_get_subscriptions( $subscriber_id );
		
		// get the subscriber data
		$subscriber_data = slb_get_subscriber_data( $subscriber_id );
		
		// set the title
		$title = $subscriber_data['fname'] .'\'s Subscriptions';
	
		// build out output html
		$output = '
			<form id="slb_manage_subscriptions_form" class="slb-form" method="post"  
			action="/wp-admin/admin-ajax.php?action=slb_unsubscribe">
				
				<input type="hidden" name="subscriber_id" value="'. $subscriber_id .'">
				
				<h3 class="slb-title">'. $title .'</h3>';
				
				if( !count($lists) ):
					
					$output .='<p>There are no active subscriptions.</p>';
				
				else:
				
					$output .= '<table>
						<tbody>';
						
						// loop over lists
						foreach( $lists as &$list_id ):
						
							$list_object = get_post( $list_id );
						
							$output .= '<tr>
								<td>'.
									$list_object->post_title
								.'</td>
								<td>
									<label>
										<input 
											type="checkbox" name="list_ids[]" 
											value="'. $list_object->ID .'" 
										/> UNSUBSCRIBE
									</label>
								</td>
							</tr>';
							
						endforeach;
						
						// close up our output html
						$output .='</tbody>
					</table>
					
					<p><input type="submit" value="Save Changes" /></p>';
				
				endif;
				
			$output .='
				</form>
			';
	
	} catch( Exception $e ) {
		
		// php error
		
	}
	
	// return output 
	return $output;
	
}

// 6.13
// hint: returns an array of email template data IF the template exists
function slb_get_email_template( $subscriber_id, $email_template_name, $list_id ) {
	
	// setup return variable
	$template_data = array();
	
	// create new array to store email templates
	$email_templates = array();
	
	// get list object
	$list = get_post( $list_id );
	
	// get subscriber object
	$subscriber = get_post( $subscriber_id );
	
	if( !slb_validate_list( $list ) || !slb_validate_subscriber( $subscriber ) ):
	
		// the list or the subscriber is not valid
	
	else:
	
		// get subscriber data 
		$subscriber_data = slb_get_subscriber_data( $subscriber_id );
	
		// get unique manage subscription link
		$manage_subscriptions_link = slb_get_manage_subscriptions_link( $subscriber_data['email'], $list_id );
		
		// get default email header 
		$default_email_header = '
			<p>
				Hello, '. $subscriber_data['fname'] .'
			</p>
		';
		
		// get default email footer 
		$default_email_footer = slb_get_option('slb_default_email_footer');
		
		// setup unsubscribe text
		$unsubscribe_text = '
			<br /><br />
			<hr />
			<p><a href="'. $manage_subscriptions_link .'">Click here to unsubscribe</a> from this or any other email list.</p>';
			
		
		
		// setup email templates
		
			// get unique opt-in link
			$optin_link = slb_get_optin_link( $subscriber_data['email'], $list_id );
			
			// template: new_subscription
			$email_templates['new_subscription'] = array(
				'subject' => 'Thank you for subscribing to '. $list->post_title .'! Please confirm your subscription.',
				'body' => '
					'. $default_email_header .'
					<p>Thank you for subscribing to '. $list->post_title .'!</p>
					<p>Please <a href="'. $optin_link .'">click here to confirm your subscription.</a></p>
					'. $default_email_footer . $unsubscribe_text,
			);

	
	endif;
	
	// IF the requested email template exists
	if( isset( $email_templates[ $email_template_name ] ) ):
	
		// add template data to return variable
		$template_data = $email_templates[ $email_template_name ];
	
	endif;
	
	// return template data
	return $template_data;
	
}

// 6.13
// hint: validates whether the post object exists and that it's a validate post_type
function slb_validate_list( $list_object ) {
	
	$list_valid = false;
	
	if( isset($list_object->post_type) && $list_object->post_type == 'slb_list' ):
	
		$list_valid = true;
	
	endif;
	
	return $list_valid;
	
}

// 6.14
// hint: validates whether the post object exists and that it's a validate post_type
function slb_validate_subscriber( $subscriber_object ) {
	
	$subscriber_valid = false;
	
	if( isset($subscriber_object->post_type) && $subscriber_object->post_type == 'slb_subscriber' ):
	
		$subscriber_valid = true;
	
	endif;
	
	return $subscriber_valid;
	
}

// 6.15
// hint: returns a unique link for managing a particular users subscriptions
function slb_get_manage_subscriptions_link( $email, $list_id=0 ) {
	
	$link_href = '';
	
	try {
		
		$page = get_post( slb_get_option('slb_manage_subscription_page_id') );
		$slug = $page->post_name;
		
		$permalink = get_permalink($page);
		
		// get character to start querystring
		$startquery = slb_get_querystring_start( $permalink );
		
		$link_href = $permalink . $startquery .'email='. urlencode($email) .'&list='. $list_id;
		
	} catch( Exception $e ) {
		
		//$link_href = $e->getMessage();
		
	}
	
	return esc_url($link_href);
	
}

// 6.16
// hint: returns the appropriate character for the begining of a querystring
function slb_get_querystring_start( $permalink ) {
	
	// setup our default return variable
	$querystring_start = '&';
	
	// IF ? is not found in the permalink
	if( strpos($permalink, '?') === false ):
		$querystring_start = '?';
	endif;
	
	return $querystring_start;
	
}

// 6.17
// hint: returns a unique link for opting into an email list
function slb_get_optin_link( $email, $list_id=0 ) {
	
	$link_href = '';
	
	try {
		
		$page = get_post( slb_get_option('slb_confirmation_page_id') );
		$slug = $page->post_name;
		$permalink = get_permalink($page);
		
		// get character to start querystring
		$startquery = slb_get_querystring_start( $permalink );
		
		$link_href = $permalink . $startquery .'email='. urlencode($email) .'&list='. $list_id;
		
	} catch( Exception $e ) {
		
		//$link_href = $e->getMessage();
		
	}
	
	return esc_url($link_href);
	
}

// 6.18
// hint: returns html for messags
function slb_get_message_html( $message, $message_type ) {
	
	$output = '';
	
	try {
		
		$message_class = 'confirmation';
		
		switch( $message_type ) {
			case 'warning': 
				$message_class = 'slb-warning';
				break;
			case 'error': 
				$message_class = 'slb-error';
				break;
			default:
				$message_class = 'slb-confirmation';
				break;
		}
		
		$output .= '
			<div class="slb-message-container">
				<div class="slb-message '. $message_class .'">
					<p>'. $message .'</p>
				</div>
			</div>
		';
		
	} catch( Exception $e ) {
		
	}
	
	return $output;
	
}


/* !7. CUSTOM POST TYPES */

// 7.1
// subscribers
include_once( plugin_dir_path( __FILE__ ) . 'cpt/slb_subscriber.php');

//7.2
// lists
include_once( plugin_dir_path( __FILE__ ) . 'cpt/slb_list.php');




/* !8. ADMIN PAGES */

// 8.1
// hint: dashboard admin page
function slb_dashboard_admin_page() {
	
	
	$output = '
		<div class="wrap">
			
			<h2>Snappy List Builder</h2>
			
			<p>The ultimate email list building plugin for WordPress. Capture new subscribers. Reward subscribers with a custom download upon opt-in. Build unlimited lists. Import and export subscribers easily with .csv</p>
		
		</div>
	';
	
	echo $output;
	
}

// 8.2
// hint: import subscribers admin page
function slb_import_admin_page() {
	
	
	$output = '
		<div class="wrap">
			
			<h2>Import Subscribers</h2>
			
			<p>Page description...</p>
		
		</div>
	';
	
	echo $output;
	
}

// 8.3
// hint: plugin options admin page
function slb_options_admin_page() {
	
	// get the default values for our options
	$options = slb_get_current_options();
	
	echo('<div class="wrap">
		
		<h2>Snappy List Builder Options</h2>
		
		<form action="options.php" method="post">');
		
			// outputs a unique nounce for our plugin options
			settings_fields('slb_plugin_options');
			// generates a unique hidden field with our form handling url
			@do_settings_fields('slb_plugin_options');
			
			echo('<table class="form-table">
			
				<tbody>
			
					<tr>
						<th scope="row"><label for="slb_manage_subscription_page_id">Manage Subscriptions Page</label></th>
						<td>
							'. slb_get_page_select( 'slb_manage_subscription_page_id', 'slb_manage_subscription_page_id', 0, 'id', $options['slb_manage_subscription_page_id'] ) .'
							<p class="description" id="slb_manage_subscription_page_id-description">This is the page where Snappy List Builder will send subscribers to manage their subscriptions. <br />
								IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[slb_manage_subscriptions]</strong>.</p>
						</td>
					</tr>
					
			
					<tr>
						<th scope="row"><label for="slb_confirmation_page_id">Opt-In Page</label></th>
						<td>
							'. slb_get_page_select( 'slb_confirmation_page_id', 'slb_confirmation_page_id', 0, 'id', $options['slb_confirmation_page_id'] ) .'
							<p class="description" id="slb_confirmation_page_id-description">This is the page where Snappy List Builder will send subscribers to confirm their subscriptions. <br />
								IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[slb_confirm_subscription]</strong>.</p>
						</td>
					</tr>
					
			
					<tr>
						<th scope="row"><label for="slb_reward_page_id">Download Reward Page</label></th>
						<td>
							'. slb_get_page_select( 'slb_reward_page_id', 'slb_reward_page_id', 0, 'id', $options['slb_reward_page_id'] ) .'
							<p class="description" id="slb_reward_page_id-description">This is the page where Snappy List Builder will send subscribers to retrieve their reward downloads. <br />
								IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[slb_download_reward]</strong>.</p>
						</td>
					</tr>
			
					<tr>
						<th scope="row"><label for="slb_default_email_footer">Email Footer</label></th>
						<td>');
						
							
							// wp_editor will act funny if it's stored in a string so we run it like this...
							wp_editor( $options['slb_default_email_footer'], 'slb_default_email_footer', array( 'textarea_rows'=>8 ) );
							
							
							echo('<p class="description" id="slb_default_email_footer-description">The default text that appears at the end of emails generated by this plugin.</p>
						</td>
					</tr>
			
					<tr>
						<th scope="row"><label for="slb_download_limit">Reward Download Limit</label></th>
						<td>
							<input type="number" name="slb_download_limit" value="'. $options['slb_download_limit'] .'" class="" />
							<p class="description" id="slb_download_limit-description">The amount of downloads a reward link will allow before expiring.</p>
						</td>
					</tr>
			
				</tbody>
				
			</table>');
		
			// outputs the WP submit button html
			@submit_button();
		
		
		echo('</form>
	
	</div>');
	
}





/* !9. SETTINGS */

// 9.1
// hint: registers all our plugin options
function slb_register_options() {
	// plugin options
	register_setting('slb_plugin_options', 'slb_manage_subscription_page_id');
	register_setting('slb_plugin_options', 'slb_confirmation_page_id');
	register_setting('slb_plugin_options', 'slb_reward_page_id');
	register_setting('slb_plugin_options', 'slb_default_email_footer');
	register_setting('slb_plugin_options', 'slb_download_limit');
}


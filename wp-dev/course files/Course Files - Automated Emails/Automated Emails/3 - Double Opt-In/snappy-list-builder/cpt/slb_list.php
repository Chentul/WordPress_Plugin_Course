<?php
	
add_action( 'init', 'slb_register_slb_list' );
function slb_register_slb_list() {

	$labels = array(
		"name" => "Lists",
		"singular_name" => "Lists",
		);

	$args = array(
		"labels" => $labels,
		"description" => "",
		"public" => false,
		"show_ui" => true,
		"has_archive" => false,
		"show_in_menu" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => array( "slug" => "slb_list", "with_front" => false ),
		"query_var" => true,
				
		"supports" => array( "title" ),		
	);
	register_post_type( "slb_list", $args );

// End of cptui_register_my_cpts()
}

<?php
	$cwk_thumbimg = array(200, 999); // size of featured image in archive/category blog
	$cwk_postimg = array(200, 999); // size of featured image on single post.
	add_image_size( 'cwk-slider', 1420, 447, true ); // Slider

	require_once(get_stylesheet_directory().'/inc/eai_election_results.php');
	
	 register_sidebar(array(
			'name' => 'Top Banner Ad',
			'id' => 'topbanner',
			'description' => 'Widget for a top banner ad.',
			'before_widget' => '<div class="topad"><div id="%1$s" class=" %2$s ad-container">',
			'after_widget'  => '</div></div>'

	));

	add_action('after_setup_theme', ea_setup);
	/**  ea_setup
	*  init stuff that we have to init after the main theme is setup.
	*
	*/
	function ea_setup() {
	 /* do stuff ehre. */
	}


add_filter('tc_category_archive_title' , 'my_cat_title');
function my_cat_title($title) {
 return 'My archives title for : ';
}

add_action( 'post_submitbox_misc_actions', 'fix_autosave' );
function fix_autosave() {
	$html_out = '<a class="button" href="javascript:" style="float:right;margin:10px;" onclick="jQuery("#submitpost input").removeClass("disabled");return false;">unlock</a> ';
	echo $html_out;
}

?>

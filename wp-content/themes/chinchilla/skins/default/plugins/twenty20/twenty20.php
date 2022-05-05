<?php
/* Twenty20 Image Before-After support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('chinchilla_twenty20_theme_setup9')) {
	add_action( 'after_setup_theme', 'chinchilla_twenty20_theme_setup9', 9 );
	function chinchilla_twenty20_theme_setup9() {
		if (is_admin()) {
			add_filter( 'chinchilla_filter_tgmpa_required_plugins',		'chinchilla_twenty20_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'chinchilla_twenty20_tgmpa_required_plugins' ) ) {
	function chinchilla_twenty20_tgmpa_required_plugins($list=array()) {
		if (chinchilla_storage_isset('required_plugins', 'twenty20') && chinchilla_storage_get_array( 'required_plugins', 'twenty20', 'install' ) !== false) {
			$list[] = array(
				'name' 		=> chinchilla_storage_get_array('required_plugins', 'twenty20', 'title'),
				'slug' 		=> 'twenty20',
				'required' 	=> false
			);
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( !function_exists( 'chinchilla_exists_twenty20' ) ) {
	function chinchilla_exists_twenty20() {
		return function_exists('twenty20_dir_init');
	}
}

?>
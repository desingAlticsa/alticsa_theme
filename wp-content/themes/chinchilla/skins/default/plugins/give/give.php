<?php
/* Give (donation forms) support functions
------------------------------------------------------------------------------- */

if ( ! defined( 'CHINCHILLA_GIVE_FORMS_PT_FORMS' ) )			define( 'CHINCHILLA_GIVE_FORMS_PT_FORMS', 'give_forms' );
if ( ! defined( 'CHINCHILLA_GIVE_FORMS_PT_PAYMENT' ) )			define( 'CHINCHILLA_GIVE_FORMS_PT_PAYMENT', 'give_payment' );
if ( ! defined( 'CHINCHILLA_GIVE_FORMS_TAXONOMY_CATEGORY' ) )	define( 'CHINCHILLA_GIVE_FORMS_TAXONOMY_CATEGORY', 'give_forms_category' );
if ( ! defined( 'CHINCHILLA_GIVE_FORMS_TAXONOMY_TAG' ) )		define( 'CHINCHILLA_GIVE_FORMS_TAXONOMY_TAG', 'give_forms_tag' );

// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'chinchilla_give_theme_setup3' ) ) {
	add_action( 'after_setup_theme', 'chinchilla_give_theme_setup3', 3 );
	function chinchilla_give_theme_setup3() {
		if ( chinchilla_exists_give() ) {
			// Section 'Give'
			chinchilla_storage_merge_array(
				'options', '', array_merge(
					array(
						'give' => array(
							'title' => esc_html__( 'Give Donations', 'chinchilla' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display the Give Donations pages', 'chinchilla' ) ),
							'icon'  => 'icon-donation',
							'type'  => 'section',
						),
					),
					chinchilla_options_get_list_cpt_options( 'give', esc_html__( 'Give Donations', 'chinchilla' ) )
				)
			);
		}
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'chinchilla_give_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'chinchilla_give_theme_setup9', 9 );
	function chinchilla_give_theme_setup9() {
		if ( chinchilla_exists_give() ) {
			add_action( 'wp_enqueue_scripts', 'chinchilla_give_frontend_scripts', 1100 );
            add_action( 'wp_enqueue_scripts', 'chinchilla_give_responsive_styles', 2000 );
			add_filter( 'chinchilla_filter_merge_styles', 'chinchilla_give_merge_styles' );
            add_filter( 'chinchilla_filter_merge_styles_responsive', 'chinchilla_give_merge_styles_responsive' );
            add_filter( 'chinchilla_filter_merge_scripts', 'chinchilla_give_merge_scripts');
			add_filter( 'chinchilla_filter_get_post_categories', 'chinchilla_give_get_post_categories', 10, 2 );
			add_filter( 'chinchilla_filter_post_type_taxonomy', 'chinchilla_give_post_type_taxonomy', 10, 2 );
			add_filter( 'chinchilla_filter_detect_blog_mode', 'chinchilla_give_detect_blog_mode' );

            // Search theme-specific templates in the skin dir (if exists)
            add_filter( 'give_get_locate_template', 'chinchilla_give_get_locate_template', 100, 3 );
            add_filter( 'give_get_template_part', 'chinchilla_give_get_template_part', 100, 3 );
		}
		if ( is_admin() ) {
			add_filter( 'chinchilla_filter_tgmpa_required_plugins', 'chinchilla_give_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'chinchilla_give_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('chinchilla_filter_tgmpa_required_plugins', 'chinchilla_give_tgmpa_required_plugins');
	function chinchilla_give_tgmpa_required_plugins( $list = array() ) {
		if ( chinchilla_storage_isset( 'required_plugins', 'give' ) && chinchilla_storage_get_array( 'required_plugins', 'give', 'install' ) !== false ) {
			$list[] = array(
				'name'     => chinchilla_storage_get_array( 'required_plugins', 'give', 'title' ),
				'slug'     => 'give',
				'required' => false,
			);
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( ! function_exists( 'chinchilla_exists_give' ) ) {
	function chinchilla_exists_give() {
		return class_exists( 'Give' );
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'chinchilla_give_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'chinchilla_give_frontend_scripts', 1100 );
	function chinchilla_give_frontend_scripts() {
		if ( chinchilla_is_on( chinchilla_get_theme_option( 'debug_mode' ) ) ) {
			$chinchilla_url = chinchilla_get_file_url( 'plugins/give/give.css' );
			if ( '' != $chinchilla_url ) {
				wp_enqueue_style( 'chinchilla-give', $chinchilla_url, array(), null );
			}
            $chinchilla_url = chinchilla_get_file_url( 'plugins/give/give.js' );
            if ( '' != $chinchilla_url ) {
                wp_enqueue_script( 'chinchilla-give', $chinchilla_url, array( 'jquery' ), null, true );
            }
		}
	}
}
// Enqueue responsive styles for frontend
if ( ! function_exists( 'chinchilla_give_responsive_styles' ) ) {
    //Handler of the add_action( 'wp_enqueue_scripts', 'chinchilla_give_responsive_styles', 2000 );
    function chinchilla_give_responsive_styles() {
        if ( chinchilla_is_on( chinchilla_get_theme_option( 'debug_mode' ) ) ) {
            $chinchilla_url = chinchilla_get_file_url( 'plugins/give/give-responsive.css' );
            if ( '' != $chinchilla_url ) {
                wp_enqueue_style( 'chinchilla-give-responsive', $chinchilla_url, array(), null );
            }
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'chinchilla_give_merge_styles' ) ) {
	//Handler of the add_filter('chinchilla_filter_merge_styles', 'chinchilla_give_merge_styles');
	function chinchilla_give_merge_styles( $list ) {
		$list[] = 'plugins/give/give.css';
		return $list;
	}
}
// Merge responsive styles
if ( ! function_exists( 'chinchilla_give_merge_styles_responsive' ) ) {
    //Handler of the add_filter('chinchilla_filter_merge_styles_responsive', 'chinchilla_give_merge_styles_responsive');
    function chinchilla_give_merge_styles_responsive( $list ) {
        $list[] = 'plugins/give/give-responsive.css';
        return $list;
    }
}
// Merge custom scripts
if ( ! function_exists( 'chinchilla_give_merge_scripts' ) ) {
    //Handler of the add_filter('chinchilla_filter_merge_scripts', 'chinchilla_give_merge_scripts');
    function chinchilla_give_merge_scripts( $list ) {
        $list[] = 'plugins/give/give.js';
        return $list;
    }
}
// Return true, if current page is any give page
if ( ! function_exists( 'chinchilla_is_give_page' ) ) {
	function chinchilla_is_give_page() {
		$rez = chinchilla_exists_give()
					&& ! is_search()
					&& (
						is_singular( CHINCHILLA_GIVE_FORMS_PT_FORMS )
						|| is_post_type_archive( CHINCHILLA_GIVE_FORMS_PT_FORMS )
						|| is_tax( CHINCHILLA_GIVE_FORMS_TAXONOMY_CATEGORY )
						|| is_tax( CHINCHILLA_GIVE_FORMS_TAXONOMY_TAG )
						|| ( function_exists( 'is_give_form' ) && is_give_form() )
						|| ( function_exists( 'is_give_category' ) && is_give_category() )
						|| ( function_exists( 'is_give_tag' ) && is_give_tag() )
						);
		return $rez;
	}
}

// Detect current blog mode
if ( ! function_exists( 'chinchilla_give_detect_blog_mode' ) ) {
	//Handler of the add_filter( 'chinchilla_filter_detect_blog_mode', 'chinchilla_give_detect_blog_mode' );
	function chinchilla_give_detect_blog_mode( $mode = '' ) {
		if ( chinchilla_is_give_page() ) {
			$mode = 'give';
		}
		return $mode;
	}
}


// Search skin-specific templates in the skin dir (if exists)
if ( ! function_exists( 'chinchilla_give_get_locate_template' ) ) {
    //Handler of the add_filter( 'give_get_locate_template', 'chinchilla_give_get_locate_template', 100, 3 );
    function chinchilla_give_get_locate_template( $template, $template_name, $template_path ) {
        $folders = apply_filters( 'chinchilla_filter_give_locate_template_folders', array(
            $template_path,
            'plugins/give/templates'
        ) );
        foreach ( $folders as $f ) {
            $theme_dir = apply_filters( 'chinchilla_filter_get_theme_file_dir', '', trailingslashit( chinchilla_esc( $f ) ) . $template_name );
            if ( '' != $theme_dir ) {
                $template = $theme_dir;
                break;
            }
        }
        return $template;
    }
}


// Search skin-specific templates parts in the skin dir (if exists)
if ( ! function_exists( 'chinchilla_give_get_template_part' ) ) {
    //Handler of the add_filter( 'give_get_template_part', 'chinchilla_give_get_template_part', 100, 3 );
    function chinchilla_give_get_template_part( $template, $slug, $name ) {
        $folders = apply_filters( 'chinchilla_filter_give_get_template_part_folders', array(
            'give',
            'plugins/give/templates'
        ) );
        foreach ( $folders as $f ) {
            $theme_dir = apply_filters( 'chinchilla_filter_get_theme_file_dir', '', trailingslashit( chinchilla_esc( $f ) ) . "{$slug}-{$name}.php" );
            if ( '' != $theme_dir ) {
                $template = $theme_dir;
                break;
            }
        }
        return $template;
    }
}



// Return taxonomy for current post type
if ( ! function_exists( 'chinchilla_give_post_type_taxonomy' ) ) {
	//Handler of the add_filter( 'chinchilla_filter_post_type_taxonomy',	'chinchilla_give_post_type_taxonomy', 10, 2 );
	function chinchilla_give_post_type_taxonomy( $tax = '', $post_type = '' ) {
		if ( chinchilla_exists_give() && CHINCHILLA_GIVE_FORMS_PT_FORMS == $post_type ) {
			$tax = CHINCHILLA_GIVE_FORMS_TAXONOMY_CATEGORY;
		}
		return $tax;
	}
}

// Show categories of the current product
if ( ! function_exists( 'chinchilla_give_get_post_categories' ) ) {
	//Handler of the add_filter( 'chinchilla_filter_get_post_categories', 'chinchilla_give_get_post_categories', 10, 2 );
	function chinchilla_give_get_post_categories( $cats = '', $args = array() ) {
		if ( get_post_type() == CHINCHILLA_GIVE_FORMS_PT_FORMS ) {
			$cat_sep = apply_filters(
									'chinchilla_filter_post_meta_cat_separator',
									'<span class="post_meta_item_cat_separator">' . ( ! isset( $args['cat_sep'] ) || ! empty( $args['cat_sep'] ) ? ', ' : ' ' ) . '</span>',
									$args
									);
			$cats = chinchilla_get_post_terms( $cat_sep, get_the_ID(), CHINCHILLA_GIVE_FORMS_TAXONOMY_CATEGORY );
		}
		return $cats;
	}
}

// Add plugin-specific colors and fonts to the custom CSS
if ( chinchilla_exists_give() ) {
	require_once chinchilla_get_file_dir( 'plugins/give/give-style.php' );
}

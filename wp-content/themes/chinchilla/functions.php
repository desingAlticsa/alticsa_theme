<?php
/**
 * Theme functions: init, enqueue scripts and styles, include required files and widgets
 *
 * @package CHINCHILLA
 * @since CHINCHILLA 1.0
 */

if ( ! defined( 'CHINCHILLA_THEME_DIR' ) ) {
	define( 'CHINCHILLA_THEME_DIR', trailingslashit( get_template_directory() ) );
}
if ( ! defined( 'CHINCHILLA_THEME_URL' ) ) {
	define( 'CHINCHILLA_THEME_URL', trailingslashit( get_template_directory_uri() ) );
}
if ( ! defined( 'CHINCHILLA_CHILD_DIR' ) ) {
	define( 'CHINCHILLA_CHILD_DIR', trailingslashit( get_stylesheet_directory() ) );
}
if ( ! defined( 'CHINCHILLA_CHILD_URL' ) ) {
	define( 'CHINCHILLA_CHILD_URL', trailingslashit( get_stylesheet_directory_uri() ) );
}

//-------------------------------------------------------
//-- Theme init
//-------------------------------------------------------

// Theme init priorities:
// Action 'after_setup_theme'
// 1 - register filters to add/remove lists items in the Theme Options
// 2 - create Theme Options
// 3 - add/remove Theme Options elements
// 5 - load Theme Options. Attention! After this step you can use only basic options (not overriden)
// 9 - register other filters (for installer, etc.)
//10 - standard Theme init procedures (not ordered)
// Action 'wp_loaded'
// 1 - detect override mode. Attention! Only after this step you can use overriden options (separate values for the shop, courses, etc.)

if ( ! function_exists( 'chinchilla_theme_setup1' ) ) {
	add_action( 'after_setup_theme', 'chinchilla_theme_setup1', 1 );
	function chinchilla_theme_setup1() {
		// Make theme available for translation
		// Translations can be filed in the /languages directory
		// Attention! Translations must be loaded before first call any translation functions!
		load_theme_textdomain( 'chinchilla', chinchilla_get_folder_dir( 'languages' ) );
	}
}

if ( ! function_exists( 'chinchilla_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'chinchilla_theme_setup9', 9 );
	function chinchilla_theme_setup9() {

		// Set theme content width
		$GLOBALS['content_width'] = apply_filters( 'chinchilla_filter_content_width', chinchilla_get_theme_option( 'page_width' ) );

		// Theme support '-full' versions of styles and scripts (used in the editors)
		add_theme_support( 'styles-and-scripts-full-merged' );
		
		// Allow external updtates
		if ( CHINCHILLA_THEME_ALLOW_UPDATE ) {
			add_theme_support( 'theme-updates-allowed' );
		}

		// Add default posts and comments RSS feed links to head
		add_theme_support( 'automatic-feed-links' );

		// Custom header setup
		add_theme_support( 'custom-header',
			array(
				'header-text' => false,
				'video'       => true,
			)
		);

		// Custom logo
		add_theme_support( 'custom-logo',
			array(
				'width'       => 250,
				'height'      => 60,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);
		// Custom backgrounds setup
		add_theme_support( 'custom-background', array() );

		// Partial refresh support in the Customize
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Supported posts formats
		add_theme_support( 'post-formats', array( 'gallery', 'video', 'audio', 'link', 'quote', 'image', 'status', 'aside', 'chat' ) );

		// Autogenerate title tag
		add_theme_support( 'title-tag' );

		// Add theme menus
		add_theme_support( 'nav-menus' );

		// Switch default markup for search form, comment form, and comments to output valid HTML5.
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

		// Register navigation menu
		register_nav_menus(
			array(
				'menu_main'   => esc_html__( 'Main Menu', 'chinchilla' ),
				'menu_mobile' => esc_html__( 'Mobile Menu', 'chinchilla' ),
				'menu_footer' => esc_html__( 'Footer Menu', 'chinchilla' ),
			)
		);

		// Register theme-specific thumb sizes
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 370, 0, false );
		$thumb_sizes = chinchilla_storage_get( 'theme_thumbs' );
		$mult        = chinchilla_get_theme_option( 'retina_ready', 1 );
		if ( $mult > 1 ) {
			$GLOBALS['content_width'] = apply_filters( 'chinchilla_filter_content_width', 1170 * $mult );
		}
		foreach ( $thumb_sizes as $k => $v ) {
			add_image_size( $k, $v['size'][0], $v['size'][1], $v['size'][2] );
			if ( $mult > 1 ) {
				add_image_size( $k . '-@retina', $v['size'][0] * $mult, $v['size'][1] * $mult, $v['size'][2] );
			}
		}
		// Add new thumb names
		add_filter( 'image_size_names_choose', 'chinchilla_theme_thumbs_sizes' );

		// Excerpt filters
		add_filter( 'excerpt_length', 'chinchilla_excerpt_length' );
		add_filter( 'excerpt_more', 'chinchilla_excerpt_more' );

		// Comment form
		add_filter( 'comment_form_fields', 'chinchilla_comment_form_fields' );
		add_filter( 'comment_form_fields', 'chinchilla_comment_form_agree', 11 );

		// Add required meta tags in the head
		add_action( 'wp_head', 'chinchilla_wp_head', 0 );

		// Load current page/post customization (if present)
		add_action( 'wp_footer', 'chinchilla_wp_footer' );
		add_action( 'admin_footer', 'chinchilla_wp_footer' );

		// Enqueue scripts and styles for the frontend
		add_action( 'wp_enqueue_scripts', 'chinchilla_load_theme_fonts', 0 );
		add_action( 'wp_enqueue_scripts', 'chinchilla_load_theme_icons', 0 );
		add_action( 'wp_enqueue_scripts', 'chinchilla_wp_styles', 1000 );                  // priority 1000 - load main theme styles
		add_action( 'wp_enqueue_scripts', 'chinchilla_wp_styles_single', 1020);            // priority 1020 - load styles of single posts
		add_action( 'wp_enqueue_scripts', 'chinchilla_wp_styles_plugins', 1100 );          // priority 1100 - load styles of the supported plugins
		add_action( 'wp_enqueue_scripts', 'chinchilla_wp_styles_custom', 1200 );           // priority 1200 - load styles with custom fonts and colors
		add_action( 'wp_enqueue_scripts', 'chinchilla_wp_styles_child', 1500 );            // priority 1500 - load styles of the child theme
		add_action( 'wp_enqueue_scripts', 'chinchilla_wp_styles_responsive', 2000 );       // priority 2000 - load responsive styles after all other styles
		add_action( 'wp_enqueue_scripts', 'chinchilla_wp_styles_single_responsive', 2020); // priority 2020 - load responsive styles of single posts after all other styles
		add_action( 'wp_enqueue_scripts', 'chinchilla_wp_styles_responsive_child', 2500);  // priority 2500 - load responsive styles of the child theme after all other responsive styles

		// Enqueue scripts for the frontend
		add_action( 'wp_enqueue_scripts', 'chinchilla_wp_scripts', 1000 );                 // priority 1000 - load main theme scripts
		add_action( 'wp_footer', 'chinchilla_localize_scripts' );

		// Add body classes
		add_filter( 'body_class', 'chinchilla_add_body_classes' );

		// Register sidebars
		add_action( 'widgets_init', 'chinchilla_register_sidebars' );
	}
}


//-------------------------------------------------------
//-- Theme styles
//-------------------------------------------------------

// Theme-specific fonts icons styles must be loaded before main stylesheet
if ( ! function_exists( 'chinchilla_theme_fonts' ) ) {
	//Handler of the add_action('wp_enqueue_scripts', 'chinchilla_load_theme_fonts', 0);
	function chinchilla_load_theme_fonts() {
		$links = chinchilla_theme_fonts_links();
		if ( count( $links ) > 0 ) {
			foreach ( $links as $slug => $link ) {
				wp_enqueue_style( sprintf( 'chinchilla-font-%s', $slug ), $link, array(), null );
			}
		}
	}
}

// Font icons styles must be loaded before main stylesheet
if ( ! function_exists( 'chinchilla_load_theme_icons' ) ) {
	//Handler of the add_action('wp_enqueue_scripts', 'chinchilla_load_theme_icons', 0);
	function chinchilla_load_theme_icons() {
		// This style NEED the theme prefix, because style 'fontello' in some plugin contain different set of characters
		// and can't be used instead this style!
		wp_enqueue_style( 'chinchilla-fontello', chinchilla_get_file_url( 'css/font-icons/css/fontello.css' ), array(), null );
	}
}


// Load frontend styles
if ( ! function_exists( 'chinchilla_wp_styles' ) ) {
	//Handler of the add_action('wp_enqueue_scripts', 'chinchilla_wp_styles', 1000);
	function chinchilla_wp_styles() {

		// Load main stylesheet
		$main_stylesheet = CHINCHILLA_THEME_URL . 'style.css';
		wp_enqueue_style( 'chinchilla-style', $main_stylesheet, array(), null );

		// Add custom bg image
		$bg_image = chinchilla_remove_protocol_from_url( chinchilla_get_theme_option( 'front_page_bg_image' ), false );
		if ( is_front_page() && ! empty( $bg_image ) && chinchilla_is_on( chinchilla_get_theme_option( 'front_page_enabled', false ) ) ) {
			// Add custom bg image for the Front page
			chinchilla_add_inline_css( 'body.frontpage, body.home-page, body.home { background-image:url(' . esc_url( $bg_image ) . ') !important }' );
		} else {
			// Add custom bg image for the body_style == 'boxed'
			$bg_image = chinchilla_get_theme_option( 'boxed_bg_image' );
			if ( ! empty( $bg_image ) && ( chinchilla_get_theme_option( 'body_style' ) == 'boxed' || is_customize_preview() ) ) {
				chinchilla_add_inline_css( '.body_style_boxed { background-image:url(' . esc_url( $bg_image ) . ') !important }' );
			}
		}

		// Add post nav background
		chinchilla_add_bg_in_post_nav();
	}
}

// Load styles of single posts
if ( ! function_exists( 'chinchilla_wp_styles_single' ) ) {
	//Handler of the add_action('wp_enqueue_scripts', 'chinchilla_wp_styles_single', 1020);
	function chinchilla_wp_styles_single() {
		if ( apply_filters( 'chinchilla_filters_separate_single_styles', false )
			&& apply_filters( 'chinchilla_filters_load_single_styles', chinchilla_is_single() || chinchilla_is_singular( 'attachment' ) || (int) chinchilla_get_theme_option( 'open_full_post_in_blog' ) > 0 )
		) {
			if ( chinchilla_is_off( chinchilla_get_theme_option( 'debug_mode' ) ) ) {
				$file = chinchilla_get_file_url( 'css/__single.css' );
				if ( ! empty( $file ) ) {
					wp_enqueue_style( 'chinchilla-single', $file, array(), null );
				}
			} else {
				$file = chinchilla_get_file_url( 'css/single.css' );
				if ( ! empty( $file ) ) {
					wp_enqueue_style( 'chinchilla-single', $file, array(), null );
				}
			}
		}
	}
}

// Load styles of supported plugins
if ( ! function_exists( 'chinchilla_wp_styles_plugins' ) ) {
	//Handler of the add_action('wp_enqueue_scripts', 'chinchilla_wp_styles_plugins', 1100);
	function chinchilla_wp_styles_plugins() {
		if ( chinchilla_is_off( chinchilla_get_theme_option( 'debug_mode' ) ) ) {
			wp_enqueue_style( 'chinchilla-plugins', chinchilla_get_file_url( 'css/__plugins' . ( chinchilla_is_preview() || ! chinchilla_optimize_css_and_js_loading() ? '-full' : '' ) . '.css' ), array(), null );
		}
	}
}

// Load styles with custom fonts and colors
if ( ! function_exists( 'chinchilla_wp_styles_custom' ) ) {
	//Handler of the add_action('wp_enqueue_scripts', 'chinchilla_wp_styles_custom', 1200);
	function chinchilla_wp_styles_custom() {
		if ( ! is_customize_preview() && chinchilla_is_off( chinchilla_get_theme_option( 'debug_mode' ) ) && ! chinchilla_is_blog_mode_custom() ) {
			wp_enqueue_style( 'chinchilla-custom', chinchilla_get_file_url( 'css/__custom.css' ), array(), null );
		} else {
			wp_enqueue_style( 'chinchilla-custom', chinchilla_get_file_url( 'css/__custom-inline.css' ), array(), null );
			wp_add_inline_style( 'chinchilla-custom', chinchilla_customizer_get_css() );
		}
	}
}

// Load child-theme stylesheet (if different) after all theme styles
if ( ! function_exists( 'chinchilla_wp_styles_child' ) ) {
	//Handler of the add_action('wp_enqueue_scripts', 'chinchilla_wp_styles_child', 1500);
	function chinchilla_wp_styles_child() {
		if ( CHINCHILLA_THEME_URL != CHINCHILLA_CHILD_URL ) {
			wp_enqueue_style( 'chinchilla-child', CHINCHILLA_CHILD_URL . 'style.css', array( 'chinchilla-style' ), null );
		}
	}
}

// Load responsive styles (priority 2500 - load it after other responsive styles)
if ( ! function_exists( 'chinchilla_wp_styles_responsive_child' ) ) {
	//Handler of the add_action('wp_enqueue_scripts', 'chinchilla_wp_styles_responsive_child', 2500);
	function chinchilla_wp_styles_responsive_child() {
		if ( CHINCHILLA_THEME_URL != CHINCHILLA_CHILD_URL && file_exists( CHINCHILLA_CHILD_DIR . 'responsive.css' ) ) {
			wp_enqueue_style( 'chinchilla-responsive-child', CHINCHILLA_CHILD_URL . 'responsive.css', array( 'chinchilla-responsive' ), null, chinchilla_media_for_load_css_responsive( 'main' ) );
		}
	}
}

// Load responsive styles (priority 2000 - load it after main styles and plugins custom styles)
if ( ! function_exists( 'chinchilla_wp_styles_responsive' ) ) {
	//Handler of the add_action('wp_enqueue_scripts', 'chinchilla_wp_styles_responsive', 2000);
	function chinchilla_wp_styles_responsive() {
		if ( chinchilla_is_off( chinchilla_get_theme_option( 'debug_mode' ) ) ) {
			wp_enqueue_style( 'chinchilla-responsive', chinchilla_get_file_url( 'css/__responsive' . ( chinchilla_is_preview() || ! chinchilla_optimize_css_and_js_loading() ? '-full' : '' ) . '.css' ), array(), null, chinchilla_media_for_load_css_responsive( 'main' ) );
		} else {
			wp_enqueue_style( 'chinchilla-responsive', chinchilla_get_file_url( 'css/responsive.css' ), array(), null, chinchilla_media_for_load_css_responsive( 'main' ) );
		}
	}
}

// Load responsive styles for single posts (priority 2020 - load it after plugins responsive styles)
if ( ! function_exists( 'chinchilla_wp_styles_single_responsive' ) ) {
	//Handler of the add_action('wp_enqueue_scripts', 'chinchilla_wp_styles_single_responsive', 2020);
	function chinchilla_wp_styles_single_responsive() {
		if ( apply_filters( 'chinchilla_filters_separate_single_styles', false )
			&& apply_filters( 'chinchilla_filters_load_single_styles', chinchilla_is_single() || chinchilla_is_singular( 'attachment' ) || (int) chinchilla_get_theme_option( 'open_full_post_in_blog' ) > 0 )
		) {
			if ( chinchilla_is_off( chinchilla_get_theme_option( 'debug_mode' ) ) ) {
				$file = chinchilla_get_file_url( 'css/__single-responsive.css' );
				if ( ! empty( $file ) ) {
					wp_enqueue_style( 'chinchilla-single-responsive', $file, array(), null, chinchilla_media_for_load_css_responsive( 'single' ) );
				}
			} else {
				$file = chinchilla_get_file_url( 'css/single-responsive.css' );
				if ( ! empty( $file ) ) {
					wp_enqueue_style( 'chinchilla-single-responsive', $file, array(), null, chinchilla_media_for_load_css_responsive( 'single' ) );
				}
			}
		}
	}
}

// Media for load responsive CSS
if ( ! function_exists( 'chinchilla_media_for_load_css_responsive' ) ) {
	function chinchilla_media_for_load_css_responsive( $slug = 'main', $media = 'all' ) {
		global $CHINCHILLA_STORAGE;
		$condition = 'all';
		$media = apply_filters( 'chinchilla_filter_media_for_load_css_responsive', $media, $slug );
		if ( ! empty( $CHINCHILLA_STORAGE['responsive'][ $media ]['max'] ) ) {
			$condition = sprintf( '(max-width:%dpx)', $CHINCHILLA_STORAGE['responsive'][ $media ]['max'] );
		} 
		return apply_filters( 'chinchilla_filter_condition_for_load_css_responsive', $condition, $slug );
	}
}

// Return maximum media slug for all responsive css-files
if ( ! function_exists( 'chinchilla_media_for_load_css_responsive_callback' ) ) {
	add_filter( 'chinchilla_filter_media_for_load_css_responsive', 'chinchilla_media_for_load_css_responsive_callback', 10, 2 );
	function chinchilla_media_for_load_css_responsive_callback( $media, $slug ) {
		return 'all' == $media ? 'xxl' : $media;
	}
}


//-------------------------------------------------------
//-- Theme scripts
//-------------------------------------------------------

// Load frontend scripts
if ( ! function_exists( 'chinchilla_wp_scripts' ) ) {
	//Handler of the add_action('wp_enqueue_scripts', 'chinchilla_wp_scripts', 1000);
	function chinchilla_wp_scripts() {
		$blog_archive = chinchilla_storage_get( 'blog_archive' ) === true || is_home();
		$blog_style   = chinchilla_get_theme_option( 'blog_style' );
		$use_masonry  = false;
		if ( strpos( $blog_style, 'blog-custom-' ) === 0 ) {
			$blog_id   = chinchilla_get_custom_blog_id( $blog_style );
			$blog_meta = chinchilla_get_custom_layout_meta( $blog_id );
			if ( ! empty( $blog_meta['scripts_required'] ) && ! chinchilla_is_off( $blog_meta['scripts_required'] ) ) {
				$blog_style  = $blog_meta['scripts_required'];
				$use_masonry = strpos( $blog_meta['scripts_required'], 'masonry' ) !== false;
			}
		} else {
			$blog_parts  = explode( '_', $blog_style );
			$blog_style  = $blog_parts[0];
			$use_masonry = chinchilla_is_blog_style_use_masonry( $blog_style );
		}

		// Superfish Menu
		// Attention! To prevent duplicate this script in the plugin and in the menu, don't merge it!
		wp_enqueue_script( 'superfish', chinchilla_get_file_url( 'js/superfish/superfish.min.js' ), array( 'jquery' ), null, true );

		// Background video
		$header_video = chinchilla_get_header_video();
		if ( ! empty( $header_video ) && ! chinchilla_is_inherit( $header_video ) ) {
			if ( chinchilla_is_youtube_url( $header_video ) ) {
				wp_enqueue_script( 'jquery-tubular', chinchilla_get_file_url( 'js/tubular/jquery.tubular.js' ), array( 'jquery' ), null, true );
			} else {
				wp_enqueue_script( 'bideo', chinchilla_get_file_url( 'js/bideo/bideo.js' ), array(), null, true );
			}
		}

		// Merged scripts
		if ( chinchilla_is_off( chinchilla_get_theme_option( 'debug_mode' ) ) ) {
			wp_enqueue_script( 'chinchilla-init', chinchilla_get_file_url( 'js/__scripts' . ( chinchilla_is_preview() || ! chinchilla_optimize_css_and_js_loading() ? '-full' : '' ) . '.js' ), apply_filters( 'chinchilla_filter_script_deps', array( 'jquery' ) ), null, true );
		} else {
			// Skip link focus
			wp_enqueue_script( 'skip-link-focus-fix', chinchilla_get_file_url( 'js/skip-link-focus-fix/skip-link-focus-fix.js' ), null, true );
			// Theme scripts
			wp_enqueue_script( 'chinchilla-utils', chinchilla_get_file_url( 'js/utils.js' ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'chinchilla-init', chinchilla_get_file_url( 'js/init.js' ), array( 'jquery' ), null, true );
		}

		// Load scripts for smooth parallax animation
		if ( chinchilla_is_singular( 'post' ) && chinchilla_get_theme_option( 'single_parallax' ) != 0 ) {
			chinchilla_load_parallax_scripts();
		}

		// Load masonry scripts
		if ( ( $blog_archive && $use_masonry ) || ( chinchilla_is_singular( 'post' ) && str_replace( 'post-format-', '', get_post_format() ) == 'gallery' ) ) {
			chinchilla_load_masonry_scripts();
		}

		// Load tabs to show filters
		if ( $blog_archive && ! is_customize_preview() && ! chinchilla_is_off( chinchilla_get_theme_option( 'show_filters' ) ) ) {
			wp_enqueue_script( 'jquery-ui-tabs', false, array( 'jquery', 'jquery-ui-core' ), null, true );
		}

		// Comments
		if ( chinchilla_is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Media elements library
		if ( chinchilla_get_theme_setting( 'use_mediaelements' ) ) {
			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}
	}
}


// Add variables to the scripts in the frontend
if ( ! function_exists( 'chinchilla_localize_scripts' ) ) {
	//Handler of the add_action('wp_footer', 'chinchilla_localize_scripts');
	function chinchilla_localize_scripts() {

		$video = chinchilla_get_header_video();

		wp_localize_script( 'chinchilla-init', 'CHINCHILLA_STORAGE', apply_filters( 'chinchilla_filter_localize_script', array(
			// AJAX parameters
			'ajax_url'            => esc_url( admin_url( 'admin-ajax.php' ) ),
			'ajax_nonce'          => esc_attr( wp_create_nonce( admin_url( 'admin-ajax.php' ) ) ),

			// Site base url
			'site_url'            => esc_url( get_home_url() ),
			'theme_url'           => CHINCHILLA_THEME_URL,

			// Site color scheme
			'site_scheme'         => sprintf( 'scheme_%s', chinchilla_get_theme_option( 'color_scheme' ) ),

			// User logged in
			'user_logged_in'      => is_user_logged_in() ? true : false,

			// Window width to switch the site header to the mobile layout
			'mobile_layout_width' => 768,
			'mobile_device'       => wp_is_mobile(),

			// Mobile breakpoints for JS (if window width less then)
			'mobile_breakpoint_underpanels_off' => 768,
			'mobile_breakpoint_fullheight_off' => 1025,

			// Sidemenu options
			'menu_side_stretch'   => (int) chinchilla_get_theme_option( 'menu_side_stretch' ) > 0,
			'menu_side_icons'     => (int) chinchilla_get_theme_option( 'menu_side_icons' ) > 0,

			// Video background
			'background_video'    => chinchilla_is_from_uploads( $video ) ? $video : '',

			// Video and Audio tag wrapper
			'use_mediaelements'   => chinchilla_get_theme_setting( 'use_mediaelements' ) ? true : false,

			// Resize video and iframe
			'resize_tag_video'    => false,
			'resize_tag_iframe'   => true,

			// Allow open full post in the blog
			'open_full_post'      => (int) chinchilla_get_theme_option( 'open_full_post_in_blog' ) > 0,

			// Which block to load in the single posts
			'which_block_load'    => chinchilla_get_theme_option( 'posts_navigation_scroll_which_block' ),

			// Current mode
			'admin_mode'          => false,

			// Strings for translation
			'msg_ajax_error'      => esc_html__( 'Invalid server answer!', 'chinchilla' ),
			'msg_i_agree_error'   => esc_html__( 'Please accept the terms of our Privacy Policy.', 'chinchilla' ),
		) ) );
	}
}

// Enqueue masonry scripts
if ( ! function_exists( 'chinchilla_load_masonry_scripts' ) ) {
	function chinchilla_load_masonry_scripts() {
		static $once = true;
		if ( $once ) {
			$once = false;
			wp_enqueue_script( 'imagesloaded' );
			wp_enqueue_script( 'masonry' );
		}
	}
}


// Enqueue parallax scripts
if ( ! function_exists( 'chinchilla_load_parallax_scripts' ) ) {
	function chinchilla_load_parallax_scripts() {
		if ( function_exists( 'trx_addons_enqueue_parallax' ) ) {
			trx_addons_enqueue_parallax();
		}
	}
}

// Enqueue specific styles and scripts for blog style
if ( ! function_exists( 'chinchilla_load_specific_scripts' ) ) {
	add_filter( 'chinchilla_filter_enqueue_blog_scripts', 'chinchilla_load_specific_scripts', 10, 5 );
	function chinchilla_load_specific_scripts( $load, $blog_style, $script_slug, $list, $responsive ) {
		if ( 'masonry' == $script_slug && false === $list ) { // if list === false - called from enqueue_scripts, true - called from merge_script
			chinchilla_load_masonry_scripts();
			$load = false;
		}
		return $load;
	}
}


//-------------------------------------------------------
//-- Head, body and footer
//-------------------------------------------------------

//  Add meta tags in the header for frontend
if ( ! function_exists( 'chinchilla_wp_head' ) ) {
	//Handler of the add_action('wp_head',	'chinchilla_wp_head', 1);
	function chinchilla_wp_head() {
		// Add ', maximum-scale=1' to the content of the viewport to disallow page scaling
		?>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="format-detection" content="telephone=no">
		<link rel="profile" href="//gmpg.org/xfn/11">
		<?php
		if ( chinchilla_is_singular() && pings_open() ) {
			?>
			<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
			<?php
		}	
	}
}

// Add theme specified classes to the body
if ( ! function_exists( 'chinchilla_add_body_classes' ) ) {
	//Handler of the add_filter( 'body_class', 'chinchilla_add_body_classes' );
	function chinchilla_add_body_classes( $classes ) {

		$classes[] = 'scheme_' . esc_attr( chinchilla_get_theme_option( 'color_scheme' ) );

		if ( is_customize_preview() ) {
			$classes[] = 'customize_preview';
		}

		$blog_mode = chinchilla_storage_get( 'blog_mode' );
		$classes[] = 'blog_mode_' . esc_attr( $blog_mode );
		$classes[] = 'body_style_' . esc_attr( chinchilla_get_theme_option( 'body_style' ) );

		if ( in_array( $blog_mode, array( 'post', 'page' ) ) || apply_filters( 'chinchilla_filter_single_post_header', chinchilla_is_singular( 'post' ) ) ) {
			$classes[] = 'is_single';
		} else {
			$classes[] = ' is_stream';
			$classes[] = 'blog_style_' . esc_attr( chinchilla_get_theme_option( 'blog_style' ) );
			if ( chinchilla_storage_get( 'blog_template' ) > 0 ) {
				$classes[] = 'blog_template';
			}
		}

		if ( apply_filters( 'chinchilla_filter_single_post_header', chinchilla_is_singular( 'post' ) || chinchilla_is_singular( 'attachment' ) ) ) {
			$classes[] = 'single_style_' . esc_attr( chinchilla_get_theme_option( 'single_style' ) );
		}

		if ( chinchilla_sidebar_present() ) {
			$classes[] = 'sidebar_show sidebar_' . esc_attr( chinchilla_get_theme_option( 'sidebar_position' ) );
			$classes[] = 'sidebar_small_screen_' . esc_attr( chinchilla_get_theme_option( 'sidebar_position_ss' ) );
		} else {
			$expand = chinchilla_get_theme_option( 'expand_content' );
			// Compatibility with old versions
			if ( "={$expand}" == '=0' ) {
				$expand = 'normal';
			} else if ( "={$expand}" == '=1' ) {
				$expand = 'expand';
			}
			if ( 'narrow' == $expand && ! chinchilla_is_singular( apply_filters('chinchilla_filter_is_singular_type', array('post') ) ) ) {
				$expand = 'normal';
			}
			$classes[] = 'sidebar_hide';
			$classes[] = "{$expand}_content";
		}

		if ( chinchilla_is_on( chinchilla_get_theme_option( 'remove_margins' ) ) ) {
			$classes[] = 'remove_margins';
		}

		$bg_image = chinchilla_get_theme_option( 'front_page_bg_image' );
		if ( is_front_page() && ! empty( $bg_image ) && chinchilla_is_on( chinchilla_get_theme_option( 'front_page_enabled', false ) ) ) {
			$classes[] = 'with_bg_image';
		}

		$classes[] = 'trx_addons_' . esc_attr( chinchilla_exists_trx_addons() ? 'present' : 'absent' );

		$classes[] = 'header_type_' . esc_attr( chinchilla_get_theme_option( 'header_type' ) );
		$classes[] = 'header_style_' . esc_attr( 'default' == chinchilla_get_theme_option( 'header_type' ) ? 'header-default' : chinchilla_get_theme_option( 'header_style' ) );
		$header_position = chinchilla_get_theme_option( 'header_position' );
		if ( 'over' == $header_position && chinchilla_is_single() && ! has_post_thumbnail() ) {
			$header_position = 'default';
		}
		$classes[] = 'header_position_' . esc_attr( $header_position );

		$menu_side = chinchilla_get_theme_option( 'menu_side' );
		$classes[] = 'menu_side_' . esc_attr( $menu_side ) . ( in_array( $menu_side, array( 'left', 'right' ) ) ? ' menu_side_present' : '' );
		$classes[] = 'no_layout';

		if ( chinchilla_get_theme_setting( 'fixed_blocks_sticky' ) ) {
			$classes[] = 'fixed_blocks_sticky';
		}

		if ( chinchilla_get_theme_option( 'blog_content' ) == 'fullpost' ) {
			$classes[] = 'fullpost_exist';
		}

		return $classes;
	}
}

// Load current page/post customization (if present)
if ( ! function_exists( 'chinchilla_wp_footer' ) ) {
	//Handler of the add_action('wp_footer', 'chinchilla_wp_footer');
	//and add_action('admin_footer', 'chinchilla_wp_footer');
	function chinchilla_wp_footer() {
		// Add header zoom
		$header_zoom = max( 0.2, min( 2, (float) chinchilla_get_theme_option( 'header_zoom' ) ) );
		if ( 1 != $header_zoom ) {
			chinchilla_add_inline_css( ".sc_layouts_title_title{font-size:{$header_zoom}em}" );
		}
		// Add logo zoom
		$logo_zoom = max( 0.2, min( 2, (float) chinchilla_get_theme_option( 'logo_zoom' ) ) );
		if ( 1 != $logo_zoom ) {
			chinchilla_add_inline_css( ".custom-logo-link,.sc_layouts_logo{font-size:{$logo_zoom}em}" );
		}
		// Put inline styles to the output
		$css = chinchilla_get_inline_css();
		if ( ! empty( $css ) ) {
			wp_enqueue_style( 'chinchilla-inline-styles', chinchilla_get_file_url( 'css/__inline.css' ), array(), null );
			wp_add_inline_style( 'chinchilla-inline-styles', $css );
		}
	}
}


//-------------------------------------------------------
//-- Sidebars and widgets
//-------------------------------------------------------

// Register widgetized areas
if ( ! function_exists( 'chinchilla_register_sidebars' ) ) {
	// Handler of the add_action('widgets_init', 'chinchilla_register_sidebars');
	function chinchilla_register_sidebars() {
		$sidebars = chinchilla_get_sidebars();
		if ( is_array( $sidebars ) && count( $sidebars ) > 0 ) {
			$cnt = 0;
			foreach ( $sidebars as $id => $sb ) {
				$cnt++;
				register_sidebar(
					apply_filters( 'chinchilla_filter_register_sidebar',
						array(
							'name'          => $sb['name'],
							'description'   => $sb['description'],
							// Translators: Add the sidebar number to the id
							'id'            => ! empty( $id ) ? $id : sprintf( 'theme_sidebar_%d', $cnt),
							'before_widget' => '<aside class="widget %2$s">',	// %1$s - id, %2$s - class
							'after_widget'  => '</aside>',
							'before_title'  => '<h5 class="widget_title">',
							'after_title'   => '</h5>',
						)
					)
				);
			}
		}
	}
}

// Return theme specific widgetized areas
if ( ! function_exists( 'chinchilla_get_sidebars' ) ) {
	function chinchilla_get_sidebars() {
		$list = apply_filters(
			'chinchilla_filter_list_sidebars', array(
				'sidebar_widgets'       => array(
					'name'        => esc_html__( 'Sidebar Widgets', 'chinchilla' ),
					'description' => esc_html__( 'Widgets to be shown on the main sidebar', 'chinchilla' ),
				),
				'header_widgets'        => array(
					'name'        => esc_html__( 'Header Widgets', 'chinchilla' ),
					'description' => esc_html__( 'Widgets to be shown at the top of the page (in the page header area)', 'chinchilla' ),
				),
				'above_page_widgets'    => array(
					'name'        => esc_html__( 'Top Page Widgets', 'chinchilla' ),
					'description' => esc_html__( 'Widgets to be shown below the header, but above the content and sidebar', 'chinchilla' ),
				),
				'above_content_widgets' => array(
					'name'        => esc_html__( 'Above Content Widgets', 'chinchilla' ),
					'description' => esc_html__( 'Widgets to be shown above the content, near the sidebar', 'chinchilla' ),
				),
				'below_content_widgets' => array(
					'name'        => esc_html__( 'Below Content Widgets', 'chinchilla' ),
					'description' => esc_html__( 'Widgets to be shown below the content, near the sidebar', 'chinchilla' ),
				),
				'below_page_widgets'    => array(
					'name'        => esc_html__( 'Bottom Page Widgets', 'chinchilla' ),
					'description' => esc_html__( 'Widgets to be shown below the content and sidebar, but above the footer', 'chinchilla' ),
				),
				'footer_widgets'        => array(
					'name'        => esc_html__( 'Footer Widgets', 'chinchilla' ),
					'description' => esc_html__( 'Widgets to be shown at the bottom of the page (in the page footer area)', 'chinchilla' ),
				),
			)
		);
		return $list;
	}
}


//-------------------------------------------------------
//-- Theme fonts
//-------------------------------------------------------

// Return links for all theme fonts
if ( ! function_exists( 'chinchilla_theme_fonts_links' ) ) {
	function chinchilla_theme_fonts_links() {
		$links = array();

		/*
		Translators: If there are characters in your language that are not supported
		by chosen font(s), translate this to 'off'. Do not translate into your own language.
		*/
		$google_fonts_enabled = ( 'off'  !== _x( 'on', 'Google fonts: on or off', 'chinchilla' ) );
		$google_fonts_api     = ( 'css2' !== _x( 'css2', 'Google fonts API: css or css2', 'chinchilla' ) ? 'css' : 'css2' );
		$adobe_fonts_enabled  = ( 'off'  !== _x( 'on', 'Adobe fonts: on or off', 'chinchilla' ) );
		$custom_fonts_enabled = ( 'off'  !== _x( 'on', 'Custom fonts (included in the theme): on or off', 'chinchilla' ) );

		if ( ( $google_fonts_enabled || $adobe_fonts_enabled || $custom_fonts_enabled ) && ! chinchilla_storage_empty( 'load_fonts' ) ) {
			$load_fonts = chinchilla_storage_get( 'load_fonts' );
			if ( count( $load_fonts ) > 0 ) {
				$google_fonts = '';
				$adobe_fonts  = '';
				foreach ( $load_fonts as $font ) {
					$used = false;
					// Custom (in the theme folder included) font
					if ( $custom_fonts_enabled && empty( $font['styles'] ) && empty( $font['link'] ) ) {
						$slug = chinchilla_get_load_fonts_slug( $font['name'] );
						$url  = chinchilla_get_file_url( "css/font-face/{$slug}/stylesheet.css" );
						if ( ! empty( $url ) ) {
							$links[ $slug ] = $url;
							$used = true;
						}
					}
					// Adobe font
					if ( $adobe_fonts_enabled && ! empty( $font['link'] ) ) {
						if ( ! in_array( $font['link'], $links ) ) {
							$slug = chinchilla_get_load_fonts_slug( $font['name'] );
							$links[ $slug ] = $font['link'];
						}
						$used = true;
					}
					// Google font
					if ( $google_fonts_enabled && ! $used ) {
						$google_fonts .= ( $google_fonts
											? ( 'css2' == $google_fonts_api
												? '&family='
												: '|'			// Attention! Using '%7C' instead '|' damage loading second+ fonts
												)
											: ''
											)
										. str_replace( ' ', '+', $font['name'] )
										. ':'
										. ( empty( $font['styles'] )
											? ( 'css2' == $google_fonts_api
												? 'ital,wght@0,400;0,700;1,400;1,700'
												: '400,700,400italic,700italic'
												)
											: $font['styles']
											);
					}
				}
				if ( $google_fonts_enabled && ! empty( $google_fonts ) ) {
					$google_fonts_subset = chinchilla_get_theme_option( 'load_fonts_subset' );
					$links['google_fonts'] = esc_url( "https://fonts.googleapis.com/{$google_fonts_api}?family={$google_fonts}&subset={$google_fonts_subset}&display=swap" );
				}
			}
		}
		return apply_filters( 'chinchilla_filter_theme_fonts_links', $links );
	}
}

// Return links for WP Editor
if ( ! function_exists( 'chinchilla_theme_fonts_for_editor' ) ) {
	function chinchilla_theme_fonts_for_editor() {
		$links = array_values( chinchilla_theme_fonts_links() );
		if ( is_array( $links ) && count( $links ) > 0 ) {
			for ( $i = 0; $i < count( $links ); $i++ ) {
				$links[ $i ] = str_replace( ',', '%2C', $links[ $i ] );
			}
		}
		return $links;
	}
}


//-------------------------------------------------------
//-- The Excerpt
//-------------------------------------------------------
if ( ! function_exists( 'chinchilla_excerpt_length' ) ) {
	// Handler of the add_filter( 'excerpt_length', 'chinchilla_excerpt_length' );
	function chinchilla_excerpt_length( $length ) {
		$blog_style = explode( '_', chinchilla_get_theme_option( 'blog_style' ) );
		return max( 0, round( chinchilla_get_theme_option( 'excerpt_length' ) / ( in_array( $blog_style[0], array( 'classic', 'masonry', 'portfolio' ) ) ? 2 : 1 ) ) );
	}
}

if ( ! function_exists( 'chinchilla_excerpt_more' ) ) {
	// Handler of the add_filter( 'excerpt_more', 'chinchilla_excerpt_more' );
	function chinchilla_excerpt_more( $more ) {
		return '&hellip;';
	}
}


//-------------------------------------------------------
//-- Comments
//-------------------------------------------------------

// Comment form fields order
if ( ! function_exists( 'chinchilla_comment_form_fields' ) ) {
	// Handler of the add_filter('comment_form_fields', 'chinchilla_comment_form_fields');
	function chinchilla_comment_form_fields( $comment_fields ) {
		if ( chinchilla_get_theme_setting( 'comment_after_name' ) ) {
			$keys = array_keys( $comment_fields );
			if ( 'comment' == $keys[0] ) {
				$comment_fields['comment'] = array_shift( $comment_fields );
			}
		}
		return $comment_fields;
	}
}

// Add checkbox with "I agree ..."
if ( ! function_exists( 'chinchilla_comment_form_agree' ) ) {
	// Handler of the add_filter('comment_form_fields', 'chinchilla_comment_form_agree', 11);
	function chinchilla_comment_form_agree( $comment_fields ) {
		$privacy_text = chinchilla_get_privacy_text();
		if ( ! empty( $privacy_text )
			&& ( ! function_exists( 'chinchilla_exists_gdpr_framework' ) || ! chinchilla_exists_gdpr_framework() )
			&& ( ! function_exists( 'chinchilla_exists_wp_gdpr_compliance' ) || ! chinchilla_exists_wp_gdpr_compliance() )
		) {
			$comment_fields['i_agree_privacy_policy'] = chinchilla_single_comments_field(
				array(
					'form_style'        => 'default',
					'field_type'        => 'checkbox',
					'field_req'         => '',
					'field_icon'        => '',
					'field_value'       => '1',
					'field_name'        => 'i_agree_privacy_policy',
					'field_title'       => $privacy_text,
				)
			);
		}
		return $comment_fields;
	}
}



//-------------------------------------------------------
//-- Thumb sizes
//-------------------------------------------------------
if ( ! function_exists( 'chinchilla_theme_thumbs_sizes' ) ) {
	//Handler of the add_filter( 'image_size_names_choose', 'chinchilla_theme_thumbs_sizes' );
	function chinchilla_theme_thumbs_sizes( $sizes ) {
		$thumb_sizes = chinchilla_storage_get( 'theme_thumbs' );
		$mult        = chinchilla_get_theme_option( 'retina_ready', 1 );
		foreach ( $thumb_sizes as $k => $v ) {
			$sizes[ $k ] = $v['title'];
			if ( $mult > 1 ) {
				$sizes[ $k . '-@retina' ] = $v['title'] . ' ' . esc_html__( '@2x', 'chinchilla' );
			}
		}
		return $sizes;
	}
}



//-------------------------------------------------------
//-- Include theme (or child) PHP-files
//-------------------------------------------------------

require_once CHINCHILLA_THEME_DIR . 'includes/utils.php';
require_once CHINCHILLA_THEME_DIR . 'includes/storage.php';

require_once CHINCHILLA_THEME_DIR . 'includes/lists.php';
require_once CHINCHILLA_THEME_DIR . 'includes/wp.php';

if ( is_admin() ) {
	require_once CHINCHILLA_THEME_DIR . 'includes/tgmpa/class-tgm-plugin-activation.php';
	require_once CHINCHILLA_THEME_DIR . 'includes/admin.php';
}

require_once CHINCHILLA_THEME_DIR . 'theme-options/theme-customizer.php';

require_once CHINCHILLA_THEME_DIR . 'front-page/front-page-options.php';

// Theme skins support
if ( defined( 'CHINCHILLA_ALLOW_SKINS' ) && CHINCHILLA_ALLOW_SKINS && file_exists( CHINCHILLA_THEME_DIR . 'skins/skins.php' ) ) {
	require_once CHINCHILLA_THEME_DIR . 'skins/skins.php';
}

// Load the following files after the skins to allow substitution of files from the skins folder
require_once chinchilla_get_file_dir( 'theme-specific/theme-tags.php' );                     // Substitution from skin is disallowed
require_once chinchilla_get_file_dir( 'theme-specific/theme-about/theme-about.php' );        // Substitution from skin is disallowed

// Free themes support
if ( CHINCHILLA_THEME_FREE ) {
	require_once chinchilla_get_file_dir( 'theme-specific/theme-about/theme-upgrade.php' );
}

require_once chinchilla_get_file_dir( 'theme-specific/theme-hovers/theme-hovers.php' );      // Substitution from skin is allowed

// Plugins support
$chinchilla_required_plugins = chinchilla_storage_get( 'required_plugins' );
if ( is_array( $chinchilla_required_plugins ) ) {
	foreach ( $chinchilla_required_plugins as $chinchilla_plugin_slug => $chinchilla_plugin_data ) {
		$chinchilla_plugin_slug = chinchilla_esc( $chinchilla_plugin_slug );
		$chinchilla_plugin_path = chinchilla_get_file_dir( sprintf( 'plugins/%1$s/%1$s.php', $chinchilla_plugin_slug ) );
		if ( ! empty( $chinchilla_plugin_path ) ) {
			require_once $chinchilla_plugin_path;
		}
	}
}

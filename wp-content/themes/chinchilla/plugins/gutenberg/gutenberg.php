<?php
/* Gutenberg support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'chinchilla_gutenberg_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'chinchilla_gutenberg_theme_setup9', 9 );
	function chinchilla_gutenberg_theme_setup9() {

		// Add wide and full blocks support
		add_theme_support( 'align-wide' );

		// Add editor styles to backend
		add_theme_support( 'editor-styles' );
		if ( is_admin() ) {
			if ( chinchilla_exists_gutenberg() && chinchilla_gutenberg_is_preview() ) {
				if ( ! chinchilla_get_theme_setting( 'gutenberg_add_context' ) ) {
					if ( ! chinchilla_exists_trx_addons() ) {
						// Attention! This place need to use 'trx_addons_filter' instead 'chinchilla_filter'
						add_editor_style( apply_filters( 'trx_addons_filter_add_editor_style', array(), 'gutenberg' ) );
					}
				}
			} else {
				add_editor_style( apply_filters( 'chinchilla_filter_add_editor_style', array(
					chinchilla_get_file_url( 'css/font-icons/css/fontello.css' ),
					chinchilla_get_file_url( 'css/editor-style.css' )
					), 'editor' )
				);
			}
		}

		if ( chinchilla_exists_gutenberg() ) {
			add_action( 'wp_enqueue_scripts', 'chinchilla_gutenberg_frontend_scripts', 1100 );
			add_action( 'wp_enqueue_scripts', 'chinchilla_gutenberg_responsive_styles', 2000 );
			add_filter( 'chinchilla_filter_merge_styles', 'chinchilla_gutenberg_merge_styles' );
			add_filter( 'chinchilla_filter_merge_styles_responsive', 'chinchilla_gutenberg_merge_styles_responsive' );
		}
		add_action( 'enqueue_block_editor_assets', 'chinchilla_gutenberg_editor_scripts' );
		add_filter( 'chinchilla_filter_localize_script_admin',	'chinchilla_gutenberg_localize_script');
		add_action( 'after_setup_theme', 'chinchilla_gutenberg_add_editor_colors' );
		if ( is_admin() ) {
			add_filter( 'chinchilla_filter_tgmpa_required_plugins', 'chinchilla_gutenberg_tgmpa_required_plugins' );
			add_filter( 'chinchilla_filter_theme_plugins', 'chinchilla_gutenberg_theme_plugins' );
		}
	}
}

// Add required styles to the Gutenberg editor
if ( ! function_exists( 'chinchilla_gutenberg_add_editor_style' ) ) {
	add_filter( 'trx_addons_filter_add_editor_style', 'chinchilla_gutenberg_add_editor_style', 1100 );
	function chinchilla_gutenberg_add_editor_style( $styles ) {
		$chinchilla_url = chinchilla_get_file_url( 'plugins/gutenberg/gutenberg-preview.css' );
		if ( '' != $chinchilla_url ) {
			$styles[] = $chinchilla_url;
		}
		return $styles;
	}
}

// Add required styles to the Gutenberg editor
if ( ! function_exists( 'chinchilla_gutenberg_add_editor_style_responsive' ) ) {
	add_filter( 'trx_addons_filter_add_editor_style', 'chinchilla_gutenberg_add_editor_style_responsive', 2000 );
	function chinchilla_gutenberg_add_editor_style_responsive( $styles ) {
		$chinchilla_url = chinchilla_get_file_url( 'plugins/gutenberg/gutenberg-preview-responsive.css' );
		if ( '' != $chinchilla_url ) {
			$styles[] = $chinchilla_url;
		}
		return $styles;
	}
}

// Remove main-theme and child-theme urls from the editor style paths
if ( ! function_exists( 'chinchilla_gutenberg_add_editor_style_remove_theme_url' ) ) {
	add_filter( 'trx_addons_filter_add_editor_style', 'chinchilla_gutenberg_add_editor_style_remove_theme_url', 9999 );
	function chinchilla_gutenberg_add_editor_style_remove_theme_url( $styles ) {
		if ( is_array( $styles ) ) {
			$template_uri   = trailingslashit( get_template_directory_uri() );
			$stylesheet_uri = trailingslashit( get_stylesheet_directory_uri() );
			$plugins_uri    = trailingslashit( defined( 'WP_PLUGIN_URL' ) ? WP_PLUGIN_URL : plugins_url() );
			foreach( $styles as $k => $v ) {
				$styles[ $k ] = str_replace(
									array(
										$template_uri,
										$stylesheet_uri,
										$plugins_uri
									),
									array(
										'',
										'',
										'../'          // up to the folder 'themes'
										. '../'        // up to the folder 'wp-content'
										. 'plugins/'   // open the folder 'plugins'
									),
									$v
								);
			}
		}
		return $styles;
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'chinchilla_gutenberg_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('chinchilla_filter_tgmpa_required_plugins',	'chinchilla_gutenberg_tgmpa_required_plugins');
	function chinchilla_gutenberg_tgmpa_required_plugins( $list = array() ) {
		if ( chinchilla_storage_isset( 'required_plugins', 'gutenberg' ) ) {
			if ( chinchilla_storage_get_array( 'required_plugins', 'gutenberg', 'install' ) !== false && version_compare( get_bloginfo( 'version' ), '5.0', '<' ) ) {
				$list[] = array(
					'name'     => chinchilla_storage_get_array( 'required_plugins', 'gutenberg', 'title' ),
					'slug'     => 'gutenberg',
					'required' => false,
				);
			}
		}
		return $list;
	}
}

// Filter theme-supported plugins list
if ( ! function_exists( 'chinchilla_gutenberg_theme_plugins' ) ) {
	//Handler of the add_filter( 'chinchilla_filter_theme_plugins', 'chinchilla_gutenberg_theme_plugins' );
	function chinchilla_gutenberg_theme_plugins( $list = array() ) {
		$list = chinchilla_add_group_and_logo_to_slave( $list, 'gutenberg', 'coblocks' );
		$list = chinchilla_add_group_and_logo_to_slave( $list, 'gutenberg', 'kadence-blocks' );
		return $list;
	}
}


// Check if Gutenberg is installed and activated
if ( ! function_exists( 'chinchilla_exists_gutenberg' ) ) {
	function chinchilla_exists_gutenberg() {
		return function_exists( 'register_block_type' );
	}
}

// Return true if Gutenberg exists and current mode is preview
if ( ! function_exists( 'chinchilla_gutenberg_is_preview' ) ) {
	function chinchilla_gutenberg_is_preview() {
		return chinchilla_exists_gutenberg() 
				&& (
					chinchilla_gutenberg_is_block_render_action()
					||
					chinchilla_is_post_edit()
					||
					chinchilla_gutenberg_is_widgets_block_editor()
					);
	}
}

// Return true if current mode is "Widgets Block Editor" (a new widgets panel with Gutenberg support)
if ( ! function_exists( 'chinchilla_gutenberg_is_widgets_block_editor' ) ) {
	function chinchilla_gutenberg_is_widgets_block_editor() {
		return is_admin()
				&& chinchilla_exists_gutenberg() 
				&& version_compare( get_bloginfo( 'version' ), '5.8', '>=' )
				&& chinchilla_check_url( 'widgets.php' )
				&& function_exists( 'wp_use_widgets_block_editor' )
				&& wp_use_widgets_block_editor();
	}
}

// Return true if current mode is "Block render"
if ( ! function_exists( 'chinchilla_gutenberg_is_block_render_action' ) ) {
	function chinchilla_gutenberg_is_block_render_action() {
		return chinchilla_exists_gutenberg() 
				&& chinchilla_check_url( 'block-renderer' ) && ! empty( $_GET['context'] ) && 'edit' == $_GET['context'];
	}
}

// Return true if content built with "Gutenberg"
if ( ! function_exists( 'chinchilla_gutenberg_is_content_built' ) ) {
	function chinchilla_gutenberg_is_content_built($content) {
		return chinchilla_exists_gutenberg() 
				&& has_blocks( $content );	// This condition is equval to: strpos($content, '<!-- wp:') !== false;
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'chinchilla_gutenberg_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'chinchilla_gutenberg_frontend_scripts', 1100 );
	function chinchilla_gutenberg_frontend_scripts() {
		if ( chinchilla_is_on( chinchilla_get_theme_option( 'debug_mode' ) ) ) {
			// Theme-specific styles
			$chinchilla_url = chinchilla_get_file_url( 'plugins/gutenberg/gutenberg-general.css' );
			if ( '' != $chinchilla_url ) {
				wp_enqueue_style( 'chinchilla-gutenberg-general', $chinchilla_url, array(), null );
			}
			// Skin-specific styles
			$chinchilla_url = chinchilla_get_file_url( 'plugins/gutenberg/gutenberg.css' );
			if ( '' != $chinchilla_url ) {
				wp_enqueue_style( 'chinchilla-gutenberg', $chinchilla_url, array(), null );
			}
		}
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'chinchilla_gutenberg_responsive_styles' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'chinchilla_gutenberg_responsive_styles', 2000 );
	function chinchilla_gutenberg_responsive_styles() {
		if ( chinchilla_is_on( chinchilla_get_theme_option( 'debug_mode' ) ) ) {
			// Theme-specific styles
			$chinchilla_url = chinchilla_get_file_url( 'plugins/gutenberg/gutenberg-general-responsive.css' );
			if ( '' != $chinchilla_url ) {
				wp_enqueue_style( 'chinchilla-gutenberg-general-responsive', $chinchilla_url, array(), null, chinchilla_media_for_load_css_responsive( 'gutenberg-general' ) );
			}
			// Skin-specific styles
			$chinchilla_url = chinchilla_get_file_url( 'plugins/gutenberg/gutenberg-responsive.css' );
			if ( '' != $chinchilla_url ) {
				wp_enqueue_style( 'chinchilla-gutenberg-responsive', $chinchilla_url, array(), null, chinchilla_media_for_load_css_responsive( 'gutenberg' ) );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'chinchilla_gutenberg_merge_styles' ) ) {
	//Handler of the add_filter('chinchilla_filter_merge_styles', 'chinchilla_gutenberg_merge_styles');
	function chinchilla_gutenberg_merge_styles( $list ) {
		$list[ 'plugins/gutenberg/gutenberg-general.css' ] = true;
		$list[ 'plugins/gutenberg/gutenberg.css' ] = true;
		return $list;
	}
}

// Merge responsive styles
if ( ! function_exists( 'chinchilla_gutenberg_merge_styles_responsive' ) ) {
	//Handler of the add_filter('chinchilla_filter_merge_styles_responsive', 'chinchilla_gutenberg_merge_styles_responsive');
	function chinchilla_gutenberg_merge_styles_responsive( $list ) {
		$list[ 'plugins/gutenberg/gutenberg-general-responsive.css' ] = true;
		$list[ 'plugins/gutenberg/gutenberg-responsive.css' ] = true;
		return $list;
	}
}


// Load required styles and scripts for Gutenberg Editor mode
if ( ! function_exists( 'chinchilla_gutenberg_editor_scripts' ) ) {
	//Handler of the add_action( 'enqueue_block_editor_assets', 'chinchilla_gutenberg_editor_scripts');
	function chinchilla_gutenberg_editor_scripts() {
		chinchilla_admin_scripts(true);
		chinchilla_admin_localize_scripts();
		// Editor styles
		wp_enqueue_style( 'chinchilla-gutenberg-editor', chinchilla_get_file_url( 'plugins/gutenberg/gutenberg-editor.css' ), array(), null );
		// Block styles
		if ( chinchilla_get_theme_setting( 'gutenberg_add_context' ) ) {
			wp_enqueue_style( 'chinchilla-gutenberg-preview', chinchilla_get_file_url( 'plugins/gutenberg/gutenberg-preview.css' ), array(), null );
			wp_enqueue_style( 'chinchilla-gutenberg-preview-responsive', chinchilla_get_file_url( 'plugins/gutenberg/gutenberg-preview-responsive.css' ), array(), null );
		}
		// Load merged scripts ?????
		wp_enqueue_script( 'chinchilla-main', chinchilla_get_file_url( 'js/__scripts-full.js' ), apply_filters( 'chinchilla_filter_script_deps', array( 'jquery' ) ), null, true );
		// Editor scripts
		wp_enqueue_script( 'chinchilla-gutenberg-preview', chinchilla_get_file_url( 'plugins/gutenberg/gutenberg-preview.js' ), array( 'jquery' ), null, true );
	}
}

// Add plugin's specific variables to the scripts
if ( ! function_exists( 'chinchilla_gutenberg_localize_script' ) ) {
	//Handler of the add_filter( 'chinchilla_filter_localize_script_admin',	'chinchilla_gutenberg_localize_script');
	function chinchilla_gutenberg_localize_script( $arr ) {
		// Color scheme
		$arr['color_scheme'] = chinchilla_get_theme_option( 'color_scheme' );
		// Sidebar position on the single posts
		$arr['sidebar_position'] = 'inherit';
		$arr['expand_content']   = 'inherit';
		$post_type               = 'post';
		$post_id                 = chinchilla_get_value_gpc( 'post' );
		if ( chinchilla_gutenberg_is_preview() )  {
			if ( ! empty( $post_id ) ) {
				$post_type = chinchilla_get_edited_post_type();
				$meta = get_post_meta( $post_id, 'chinchilla_options', true );
				if ( 'page' != $post_type && ! empty( $meta['sidebar_position_single'] ) ) {
					$arr['sidebar_position'] = $meta['sidebar_position_single'];
				} elseif ( 'page' == $post_type && ! empty( $meta['sidebar_position'] ) ) {
					$arr['sidebar_position'] = $meta['sidebar_position'];
				}
				if ( 'page' != $post_type && ! empty( $meta['expand_content_single'] ) ) {
					$arr['expand_content'] = $meta['expand_content_single'];
				} elseif ( 'page' == $post_type && ! empty( $meta['expand_content'] ) ) {
					$arr['expand_content'] = $meta['expand_content'];
				}
			} else {
				$post_type = chinchilla_get_value_gpc( 'post_type' );
				if ( empty( $post_type ) ) {
					$post_type = 'post';
				}
			}
		}
		$post_slug = str_replace( 'cpt_', '', $post_type );
		if ( 'inherit' == $arr['sidebar_position'] ) {
			if ( 'post' == $post_type ) {
				$arr['sidebar_position'] = chinchilla_get_theme_option( 'sidebar_position_single' );
				if ( 'inherit' == $arr['sidebar_position'] ) {
					$arr['sidebar_position'] = chinchilla_get_theme_option( 'sidebar_position_blog' );
				}
			} else if ( 'page' != $post_type && chinchilla_check_theme_option( 'sidebar_position_single_' . sanitize_title( $post_slug ) ) ) {
				$arr['sidebar_position'] = chinchilla_get_theme_option( 'sidebar_position_single_' . sanitize_title( $post_slug ) );
				if ( 'inherit' == $arr['sidebar_position'] && chinchilla_check_theme_option( 'sidebar_position_' . sanitize_title( $post_slug ) ) ) {
					$arr['sidebar_position'] = chinchilla_get_theme_option( 'sidebar_position_' . sanitize_title( $post_slug ) );
				}
			}
			if ( 'inherit' == $arr['sidebar_position'] ) {
				$arr['sidebar_position'] = chinchilla_get_theme_option( 'sidebar_position' );
			}
		}
		if ( 'inherit' == $arr['expand_content'] ) {
			if ( 'post' == $post_type ) {
				$arr['expand_content'] = chinchilla_get_theme_option( 'expand_content_single' );
				if ( 'inherit' == $arr['expand_content'] ) {
					$arr['expand_content'] = chinchilla_get_theme_option( 'expand_content_blog' );
				}
			} else if ( 'page' != $post_type && chinchilla_check_theme_option( 'expand_content_single_' . sanitize_title( $post_slug ) ) ) {
				$arr['expand_content'] = chinchilla_get_theme_option( 'expand_content_single_' . sanitize_title( $post_slug ) );
				if ( 'inherit' == $arr['expand_content'] && chinchilla_check_theme_option( 'expand_content_' . sanitize_title( $post_slug ) ) ) {
					$arr['expand_content'] = chinchilla_get_theme_option( 'expand_content_' . sanitize_title( $post_slug ) );
				}
			}
			if ( 'inherit' == $arr['expand_content'] ) {
				$arr['expand_content'] = chinchilla_get_theme_option( 'expand_content' );
			}
		}
		return $arr;
	}
}

// Save CSS with custom colors and fonts to the gutenberg-preview.css
if ( ! function_exists( 'chinchilla_gutenberg_save_css' ) ) {
	add_action( 'chinchilla_action_save_options', 'chinchilla_gutenberg_save_css', 30 );
	add_action( 'trx_addons_action_save_options', 'chinchilla_gutenberg_save_css', 30 );
	function chinchilla_gutenberg_save_css() {

		$msg = '/* ' . esc_html__( "ATTENTION! This file was generated automatically! Don't change it!!!", 'chinchilla' )
				. "\n----------------------------------------------------------------------- */\n";

		$add_context = array(
							'context'      => '.edit-post-visual-editor ',
							'context_self' => array( 'html', 'body', '.edit-post-visual-editor' )
							);

		// Get main styles
		//----------------------------------------------
		$css = apply_filters( 'chinchilla_filter_gutenberg_get_styles', chinchilla_fgc( chinchilla_get_file_dir( 'style.css' ) ) );
		// Append single post styles
		if ( apply_filters( 'chinchilla_filters_separate_single_styles', false ) ) {
			$css .= chinchilla_fgc( chinchilla_get_file_dir( 'css/__single.css' ) );
		}
		// Append supported plugins styles
		$css .= chinchilla_fgc( chinchilla_get_file_dir( 'css/__plugins-full.css' ) );
		// Append theme-vars styles
		$css .= chinchilla_customizer_get_css();
		// Add context class to each selector
		if ( chinchilla_get_theme_setting( 'gutenberg_add_context' ) && function_exists( 'trx_addons_css_add_context' ) ) {
			$css = trx_addons_css_add_context( $css, $add_context );
		} else {
			$css = apply_filters( 'chinchilla_filter_prepare_css', $css );
		}

		// Get responsive styles
		//-----------------------------------------------
		$css_responsive = apply_filters( 'chinchilla_filter_gutenberg_get_styles_responsive',
								chinchilla_fgc( chinchilla_get_file_dir( 'css/__responsive-full.css' ) )
								. ( apply_filters( 'chinchilla_filters_separate_single_styles', false )
									? chinchilla_fgc( chinchilla_get_file_dir( 'css/__single-responsive.css' ) )
									: ''
									)
								);
		// Add context class to each selector
		if ( chinchilla_get_theme_setting( 'gutenberg_add_context' ) && function_exists( 'trx_addons_css_add_context' ) ) {
			$css_responsive = trx_addons_css_add_context( $css_responsive, $add_context );
		} else {
			$css_responsive = apply_filters( 'chinchilla_filter_prepare_css', $css_responsive );
		}

		// Save styles to separate files
		//-----------------------------------------------

		// Save responsive styles
		$preview = chinchilla_get_file_dir( 'plugins/gutenberg/gutenberg-preview-responsive.css' );
		if ( $preview ) {
			chinchilla_fpc( $preview, $msg . $css_responsive );
			$css_responsive = '';
		}
		// Save main styles (and append responsive if its not saved to the separate file)
		chinchilla_fpc( chinchilla_get_file_dir( 'plugins/gutenberg/gutenberg-preview.css' ), $msg . $css . $css_responsive );
	}
}


// Add theme-specific colors to the Gutenberg color picker
if ( ! function_exists( 'chinchilla_gutenberg_add_editor_colors' ) ) {
	//Hamdler of the add_action( 'after_setup_theme', 'chinchilla_gutenberg_add_editor_colors' );
	function chinchilla_gutenberg_add_editor_colors() {
		$scheme = chinchilla_get_scheme_colors();
		$groups = chinchilla_storage_get( 'scheme_color_groups' );
		$names  = chinchilla_storage_get( 'scheme_color_names' );
		$colors = array();
		foreach( $groups as $g => $group ) {
			foreach( $names as $n => $name ) {
				$c = 'main' == $g ? ( 'text' == $n ? 'text_color' : $n ) : $g . '_' . str_replace( 'text_', '', $n );
				if ( isset( $scheme[ $c ] ) ) {
					$colors[] = array(
						'name'  => ( 'main' == $g ? '' : $group['title'] . ' ' ) . $name['title'],
						'slug'  => $c,
						'color' => $scheme[ $c ]
					);
				}
			}
			// Add only one group of colors
			// Delete next condition (or add false && to them) to add all groups
			if ( 'main' == $g ) {
				break;
			}
		}
		add_theme_support( 'editor-color-palette', $colors );
	}
}

// Add plugin-specific colors and fonts to the custom CSS
if ( chinchilla_exists_gutenberg() ) {
	require_once chinchilla_get_file_dir( 'plugins/gutenberg/gutenberg-style.php' );
	require_once chinchilla_get_file_dir( 'plugins/gutenberg/gutenberg-fse.php' );
}

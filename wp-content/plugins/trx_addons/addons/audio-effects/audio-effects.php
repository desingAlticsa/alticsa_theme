<?php
/**
 * Background music and audio effects
 *
 * @addon audio-effects
 * @version 1.2
 *
 * @package ThemeREX Addons
 * @since v1.84.2
 */


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_audio_effects_load_scripts_front2' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_audio_effects_load_scripts_front2', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY - 1 );
	function trx_addons_audio_effects_load_scripts_front2() {
		if ( trx_addons_is_off( trx_addons_get_option( 'optimize_css_and_js_loading' ) ) ) {
			wp_enqueue_script( 'howler', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'audio-effects/howler/howler.core.min.js' ), array(), null, true );
		}
	}
}


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_audio_effects_load_scripts_front' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_audio_effects_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_audio_effects_load_scripts_front', 10, 1 );
	function trx_addons_audio_effects_load_scripts_front( $force = false ) {
		static	$loaded  = false, $loaded2 = false;
		$debug       = trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) );
		$optimize    = ! trx_addons_is_off( trx_addons_get_option( 'optimize_css_and_js_loading' ) );
		$preview_elm = trx_addons_is_preview( 'elementor' );
		$preview_gb  = trx_addons_is_preview( 'gutenberg' );
		$theme_full  = current_theme_supports( 'styles-and-scripts-full-merged' );
		$need        = ! $loaded && ( ! $preview_elm || $debug ) && ! $preview_gb && $optimize && (
						$force === true
							|| ( $preview_elm && $debug )
							|| ( trx_addons_is_on( trx_addons_get_option( 'audio_effects_allowed' ) )
									&& count( trx_addons_get_option( 'audio_effects' ) ) > 0
								)
						);
		if ( ! $loaded2 && ( ! $optimize || $need || $preview_elm ) ) {
			$loaded2 = true;
			wp_enqueue_script( 'howler', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'audio-effects/howler/howler.core.min.js' ), array(), null, true );
		}
		if ( ! $loaded && ! $preview_gb && ( ( ! $optimize && $debug ) || ( $optimize && $need ) ) ) {
			$loaded = true;
			wp_enqueue_style(  'trx_addons-audio-effects', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'audio-effects/audio-effects.css' ), array(), null );
			wp_enqueue_script( 'trx_addons-audio-effects', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'audio-effects/audio-effects.js' ), array('jquery'), null, true );
			do_action( 'trx_addons_action_load_scripts_front', $force, 'audio_effects' );
		} 
		if ( ! $loaded && $preview_elm && $optimize && ! $debug && ! $theme_full ) {
			do_action( 'trx_addons_action_load_scripts_front', false, 'audio_effects', 2 );
		}
	}
}

	
// Merge styles to the single stylesheet
if ( ! function_exists( 'trx_addons_audio_effects_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_audio_effects_merge_styles');
	function trx_addons_audio_effects_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'audio-effects/audio-effects.css' ] = false;
		return $list;
	}
}

	
// Merge specific scripts into single file
if ( !function_exists( 'trx_addons_audio_effects_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_audio_effects_merge_scripts');
	function trx_addons_audio_effects_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'audio-effects/audio-effects.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu or layouts or finally in the whole page output
if ( !function_exists( 'trx_addons_audio_effects_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_audio_effects_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_audio_effects_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_audio_effects_check_in_html_output', 10, 1 );
	function trx_addons_audio_effects_check_in_html_output( $content = '' ) {
		if ( ! trx_addons_need_frontend_scripts( 'audio_effects' )
			&& ! trx_addons_is_off( trx_addons_get_option( 'optimize_css_and_js_loading' ) )
		) {
			$checklist = apply_filters( 'trx_addons_filter_check_in_html', array(
							'data-trx-addons-audio-effects'
							),
							'audio-effects'
						);
			foreach ( $checklist as $item ) {
				if ( strpos( $content, $item ) !== false ) {
					trx_addons_audio_effects_load_scripts_front( true );
					break;
				}
			}
		}
		return $content;
	}
}


// Add audio effects to the list with JS vars
if ( !function_exists( 'trx_addons_audio_effects_localize_script' ) ) {
	add_action("trx_addons_filter_localize_script", 'trx_addons_audio_effects_localize_script');
	function trx_addons_audio_effects_localize_script($vars) {
		// Get global options
		$audio_effects_allowed = trx_addons_is_on( trx_addons_get_option('audio_effects_allowed') ) ? 1 : 0;
		$audio_effects = trx_addons_get_option('audio_effects');
		// Check current post type meta data
		$cpt_list = apply_filters( 'trx_addons_filter_audio_effects_post_types', array('page') );
		$cpt = get_post_type();
		if ( in_array($cpt, $cpt_list) ) {
			$meta = get_post_meta( get_the_ID(), 'trx_addons_options', true );
			if ( ! empty( $meta['audio_effects_allowed'] ) && (int) $meta['audio_effects_allowed'] > 0 ) {
				$audio_effects_allowed = (int) $meta['audio_effects_allowed'] > 0 ? 1 : 0;
				if ( $audio_effects_allowed && ! empty( $meta['audio_effects'][0]['local'] ) || ! empty( $meta['audio_effects'][0]['link'] ) ) {
					if ( apply_filters('trx_addons_filter_audio_effects_replace_with_meta', false ) ) {
						// Replace global effects with page options
						$audio_effects = $meta['audio_effects'];
					} else {
						// Remove 'load' and 'background' from global effects if its overrided on this page
						foreach( $meta['audio_effects'] as $e ) {
							if ( in_array( $e['event'], array('load', 'background') ) && ( ! empty( $e['local'] ) || ! empty( $e['link'] ) ) ) {
								foreach( $audio_effects as $k => $v ) {
									if ( $v['event'] == $e['event'] ) {
										unset($audio_effects[$k]);
									}
								}
							}
						}
						// Merge effects from page options with global list. 'Load' and 'Background' will be replaced
						$audio_effects = array_merge( $audio_effects, $meta['audio_effects'] );
					}
				}
			}
		}
		// Prepare selectors
		if ( is_array($audio_effects) ) {
			foreach( $audio_effects as $k => $v ) {
				if ( ! empty( $v['selectors'] ) ) {
					$audio_effects[$k]['selectors'] = preg_replace( "/(,|\r|\r\n)+[\s]*/", ',', $v['selectors'] );
				}
			}
		}
		// Set vars
		$vars['audio_effects_allowed'] = $audio_effects_allowed;
		if ( ! empty( $audio_effects[0]['local'] ) || ! empty( $audio_effects[0]['link'] ) ) {
			$vars['audio_effects'] = $audio_effects;
		}
		return $vars;
	}
}


// Return list of events for shortcodes
if ( ! function_exists( 'trx_addons_audio_effects_get_list_sc_events' ) ) {
	function trx_addons_audio_effects_get_list_sc_events() {
		return apply_filters( 'trx_addons_filter_audio_effects_sc_events', array(
			'hover' => esc_html__( 'Mouse hover', 'trx_addons' ),
			'click' => esc_html__( 'Click', 'trx_addons' ),
		) );
	}
}


// Return list of events for page
if ( ! function_exists( 'trx_addons_audio_effects_get_list_page_events' ) ) {
	function trx_addons_audio_effects_get_list_page_events() {
		return apply_filters( 'trx_addons_filter_audio_effects_page_events', array(
			'load'       => esc_html__( 'On page load', 'trx_addons' ),
			'background' => esc_html__( 'Background music', 'trx_addons' ),
			'hover' => esc_html__( 'On hover', 'trx_addons' ),
			'click' => esc_html__( 'On click', 'trx_addons' ),
		) );
	}
}


// Return the list of params for Audio Effects
if ( ! function_exists( 'trx_addons_audio_effects_get_options_list' ) ) {
	function trx_addons_audio_effects_get_options_list( $mode = 'options' ) {
		$list = array(
					'audio_effects_section' => array(
						"title" => esc_html__('Audio effects', 'trx_addons'),
						'icon' => 'trx_addons_icon-volume-up',
						"type" => "section"
					),
					'audio_effects_section_info' => array(
						"title" => esc_html__('Audio effects settings', 'trx_addons'),
						"desc" => wp_kses_data( __("Settings of the background music and sound effects on mouse events (click, hover, etc.)", 'trx_addons') ),
						"type" => "info"
					),
					'audio_effects_allowed' => array(
						"title" => esc_html__('Allow audio effects', 'trx_addons'),
						"desc" => wp_kses_data( __("Check to allow effects on the entire site. Otherwise, the effects will only be available on pages where they are explicitly specified in the page options or in the settings of the Elementor blocks", 'trx_addons') ),
						"std" => "0",
						"type" => "switch"
					),
					'audio_effects' => array(
						"title" => esc_html__("List of the audio effects", 'trx_addons'),
						"desc" => wp_kses_data( __("Select event and specify audio to create effect", 'trx_addons') ),
						"clone" => true,
						"std" => array(array()),
						"type" => "group",
						"fields" => array(
							"event" => array(
								"title" => esc_html__("Event", 'trx_addons'),
								"class" => "trx_addons_column-1_5",
								"options" => trx_addons_audio_effects_get_list_page_events(),
								"std" => "background",
								"type" => "select"
							),
							"selectors" => array(
								"title" => esc_html__("Selectors", 'trx_addons'),
								"desc" => esc_html__("Comma separated CSS-selectors. Used only for 'Event' equals to 'On click' or 'On hover'", 'trx_addons'),
								"class" => "trx_addons_column-1_5",
								"rows" => "4",
								"std" => "",
								"type" => "textarea"
							),
							"local" => array(
								"title" => esc_html__("Local audio", 'trx_addons'),
								"class" => "trx_addons_column-1_5",
								"std" => "",
								"type" => "audio"
							),
							"link" => array(
								"title" => esc_html__("or External URL", 'trx_addons'),
								"class" => "trx_addons_column-1_5",
								"std" => "",
								"type" => "text"
							),
							"volume" => array(
								"title" => esc_html__("Volume", 'trx_addons'),
								"class" => "trx_addons_column-1_5",
								"min" => 0,
								"max" => 100,
								"std" => 50,
								"type" => "slider"
							),
						)
					)
				);
		if ( $mode == 'options' ) {
			trx_addons_array_insert_before( $list, 'audio_effects_allowed', array(
				"audio_effects_post_types" => array(
					"title" => esc_html__("Post types", 'trx_addons'),
					"desc" => wp_kses_data( __("Select post types to add params with audio effects", 'trx_addons') ),
					"dir" => 'horizontal',
					"std" => array( 'page' => 1 ),
					"options" => array(),
					"type" => "checklist"
				)
			) );
		} else {
			$list['audio_effects_allowed']['type']    = 'select';
			$list['audio_effects_allowed']['std']     = '-1';
			$list['audio_effects_allowed']['desc']    = wp_kses_data( __("Allow/disallow audio effects on this page. If you select 'Inherit' - global settings are used.", 'trx_addons') );
			$list['audio_effects_allowed']['options'] = array(
				'-1' => __( 'Inherit', 'trx_addons'),
				'0'  => __( 'Disallow', 'trx_addons'),
				'1'  => __( 'Allow', 'trx_addons'),
			);
			$list['audio_effects']['dependency'] = array(
				'audio_effects_allowed' => array('1')
			);
		}
		return apply_filters( 'trx_addons_filter_options_audio_effects', $list, $mode );
	}
}

// Fill 'Post types' before show ThemeREX Addons Options
if ( ! function_exists('trx_addons_audio_effects_before_show_options')) {
	add_filter( 'trx_addons_filter_before_show_options', 'trx_addons_audio_effects_before_show_options', 10, 2);
	function trx_addons_audio_effects_before_show_options($options, $pt='') {
		if ( isset($options['audio_effects_post_types']) ) {
			$options['audio_effects_post_types']['options'] = trx_addons_get_list_audio_effects_posts_types();
		}
		return $options;
	}
}

// Return list of allowed post's types
if ( !function_exists( 'trx_addons_get_list_audio_effects_posts_types' ) ) {
	function trx_addons_get_list_audio_effects_posts_types($prepend_inherit=false) {
		static $list = false;
		if ($list === false) {
			$list = array();
			$post_types = get_post_types(
								array(
									'public' => true,
									'exclude_from_search' => false
								),
								'objects'
							);
			if (is_array($post_types)) {
				foreach ($post_types as $pt) {
					$list[$pt->name] = $pt->label;
				}
			}
		}
		return $prepend_inherit 
					? trx_addons_array_merge(array('inherit' => esc_html__("Inherit", 'trx_addons')), $list) 
					: $list;
	}
}


// Add params to the ThemeREX Addons Options.
if ( ! function_exists( 'trx_addons_audio_effects_add_options' ) ) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_audio_effects_add_options' );
	function trx_addons_audio_effects_add_options( $options ) {
		trx_addons_array_insert_before($options, 'sc_section', trx_addons_audio_effects_get_options_list( 'options' ) );
		return $options;
	}
}


// Add parameters to the Meta Box
if (!function_exists('trx_addons_audio_effects_meta_box')) {
	add_action( 'init', 'trx_addons_audio_effects_meta_box' );
	function trx_addons_audio_effects_meta_box() {
		$cpt_list = apply_filters( 'trx_addons_filter_audio_effects_post_types', trx_addons_array_get_keys_by_value( trx_addons_get_option('audio_effects_post_types') ) );
		if ( is_array($cpt_list) ) {
			foreach( $cpt_list as $cpt ) {
				trx_addons_meta_box_register( $cpt, trx_addons_audio_effects_get_options_list( "meta_box_{$cpt}" ) );
			}
		}
	}
}


// Add "Audio Effects" params to all elements
if (!function_exists('trx_addons_elm_add_params_audio_effects')) {
	add_action( 'elementor/element/before_section_start', 'trx_addons_elm_add_params_audio_effects', 10, 3 );
	add_action( 'elementor/widget/before_section_start', 'trx_addons_elm_add_params_audio_effects', 10, 3 );
	function trx_addons_elm_add_params_audio_effects($element, $section_id, $args) {

		if ( !is_object($element) ) return;

		if ( in_array( $element->get_name(), array( 'common', 'section', 'column' ) ) && $section_id == '_section_responsive' ) {
			
			$element->start_controls_section( 'section_trx_audio_effects', array(
																		'tab' => !empty($args['tab']) ? $args['tab'] : \Elementor\Controls_Manager::TAB_ADVANCED,
																		'label' => __( 'Audio Effects', 'trx_addons' )
																	) );
			$element->add_control( 'audio_effects_allowed', array(
				'label' => __( 'Allow audio effects', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'trx_addons' ),
				'label_on' => __( 'On', 'trx_addons' ),
				'return_value' => 'on',
				'prefix_class' => 'trx_addons_audio_effects_'
			) );

			$element->add_control( 'audio_effects', array(
				'label' => '',
				'type' => \Elementor\Controls_Manager::REPEATER,
				'condition' => array(
					'audio_effects_allowed' => array( 'on' )
				),
				'default' => apply_filters('trx_addons_sc_param_group_value', array(
					array(
						'event' => 'hover',
						'local' => array( 'url' => '' ),
						'link'  => '',
						'volume'=> array( 'size' => 50, 'unit' => 'px')
					)
				), 'trx_sc_audio_effects'),
				'fields' => apply_filters('trx_addons_sc_param_group_params', array_merge(
					array(
						array(
							'name' => 'event',
							'label' => __( 'Event', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SELECT,
							'options' => trx_addons_audio_effects_get_list_sc_events(),
							'default' => 'hover',
						),
						array(
							'name' => 'selectors',
							'label' => __( 'Selectors', 'trx_addons' ),
							'label_block' => false,
							'description' => __( 'Comma separated CSS-selectors of inner elements. If empty, the effect is assigned to the element itself', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::TEXT,
							'placeholder' => __( "For example: a,.inner-blocks", 'trx_addons' ),
							'default' => ''
						),
						array(
							'name' => 'local',
							'label' => __( 'Local file', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::MEDIA,
							'media_type' => 'audio',
							'default' => array(
								'url' => '',
							),
						),
						array(
							'name' => 'link',
							'label' => __( 'or Ext. link', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::TEXT,
							'placeholder' => __( "External link", 'trx_addons' ),
							'default' => ''
						),
						array(
							'name' => 'volume',
							'label' => __( 'Volume', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'default' => array(
								'size' => 50,
								'unit' => 'px'
							),
							'size_units' => array( 'px' ),
							'range' => array(
								'px' => array(
									'min' => 0,
									'max' => 100
								)
							),
						)
					)
				), 'trx_sc_audio_effects' ),
				'title_field' => '{{{ event }}}'
			) );

			$element->end_controls_section();
		}
	}
}

// Add "data-audio-effects" to the wrapper of the row
if ( !function_exists( 'trx_addons_elm_add_audio_effects_data' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render',  'trx_addons_elm_add_audio_effects_data', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/section/before_render', 'trx_addons_elm_add_audio_effects_data', 10, 1 );
	add_action( 'elementor/frontend/column/before_render', 'trx_addons_elm_add_audio_effects_data', 10, 1 );
	add_action( 'elementor/frontend/widget/before_render', 'trx_addons_elm_add_audio_effects_data', 10, 1 );
	function trx_addons_elm_add_audio_effects_data($element) {
		if ( is_object($element) ) {
			//$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
			$audio_effects_allowed = $element->get_settings( 'audio_effects_allowed' );
			if ( ! empty( $audio_effects_allowed ) && ! trx_addons_is_off( $audio_effects_allowed ) ) {
				$audio_effects = $element->get_settings( 'audio_effects' );
				if ( ! empty( $audio_effects[0]['link'] ) || ! empty( $audio_effects[0]['local']['url'] ) ) {
					$element->add_render_attribute( '_wrapper', array(
						'data-trx-addons-audio-effects' => json_encode( trx_addons_elm_prepare_global_params( $audio_effects ), JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT ),
					) );
					trx_addons_audio_effects_load_scripts_front( true );
				}
			}
		}
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'audio-effects/audio-effects-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'audio-effects/audio-effects-sc-elementor.php';
}

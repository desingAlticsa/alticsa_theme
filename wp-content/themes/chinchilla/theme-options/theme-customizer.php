<?php
/**
 * Theme customizer
 *
 * @package CHINCHILLA
 * @since CHINCHILLA 1.0
 */


//--------------------------------------------------------------
//-- First run actions after switch theme
//--------------------------------------------------------------
if ( ! function_exists( 'chinchilla_customizer_action_switch_theme' ) ) {
	add_action( 'after_switch_theme', 'chinchilla_customizer_action_switch_theme' );
	function chinchilla_customizer_action_switch_theme() {
		// Duplicate theme options between parent and child themes
		$duplicate = chinchilla_get_theme_setting( 'duplicate_options' );
		if ( in_array( $duplicate, array( 'child', 'both' ) ) ) {
			$theme_slug      = get_template();
			$theme_time      = (int) get_option( "chinchilla_options_timestamp_{$theme_slug}" );
			$stylesheet_slug = get_stylesheet();

			// If child-theme is activated - duplicate options from template to the child-theme
			if ( $theme_slug != $stylesheet_slug ) {
				$stylesheet_time = (int) get_option( "chinchilla_options_timestamp_{$stylesheet_slug}" );
				if ( $theme_time > $stylesheet_time ) {
					chinchilla_customizer_duplicate_theme_options( $theme_slug, $stylesheet_slug, $theme_time );
				}

				// If main theme (template) is activated and 'duplicate_options' == 'child'
				// (duplicate options only from template to the child-theme) - regenerate CSS  with custom colors and fonts
			} elseif ( 'child' == $duplicate && $theme_time > 0 ) {
				chinchilla_customizer_save_css();
			}
		}
	}
}


// Duplicate theme options between template and child-theme
if ( ! function_exists( 'chinchilla_customizer_duplicate_theme_options' ) ) {
	function chinchilla_customizer_duplicate_theme_options( $from, $to, $timestamp = 0 ) {
		if ( 0 == $timestamp ) {
			$timestamp = get_option( "chinchilla_options_timestamp_{$from}" );
		}
		$from         = "theme_mods_{$from}";
		$from_options = get_option( $from );
		$to           = "theme_mods_{$to}";
		$to_options   = get_option( $to );
		if ( is_array( $from_options ) ) {
			if ( ! is_array( $to_options ) ) {
				$to_options = array();
			}
			// List of theme options to duplicate
			$theme_options = chinchilla_storage_get( 'options' );
			// List of core options to duplicate
			$additional_options = apply_filters( 'chinchilla_filter_duplicate_core_options_list', array(
				'header_image',
				'header_image_data',
				'header_video',
				'external_header_video',
				'background_color',
				'background_image',
			) );
			// Start duplicate
			foreach ( $from_options as $k => $v ) {
				if ( apply_filters( 'chinchilla_filter_duplicate_theme_option',
						isset( $theme_options[ $k ] )                           // If it's a theme option
						|| in_array( $k, $additional_options ),                 // or one of core options from list
						$k,
						$v
						)
				) {
					$to_options[ $k ] = $v;
				}
			}
			update_option( $to, $to_options );
			update_option( "chinchilla_options_timestamp_{$to}", $timestamp );
		}
	}
}


//--------------------------------------------------------------
//-- New panel in the Customizer Controls
//--------------------------------------------------------------

// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'chinchilla_customizer_setup3' ) ) {
	add_action( 'after_setup_theme', 'chinchilla_customizer_setup3', 3 );
	function chinchilla_customizer_setup3() {
		chinchilla_storage_merge_array( 'options', '', array(
			'cpt' => array(
				'title'    => esc_html__( 'Plugins settings', 'chinchilla' ),
				'desc'     => '',
				'priority' => 400,
				'icon'     => 'icon-plugins',
				'type'     => 'panel',
			),
		) );
	}
}

// 3 - add/remove Theme Options elements
if ( ! function_exists( 'chinchilla_customizer_setup4' ) ) {
	add_action( 'after_setup_theme', 'chinchilla_customizer_setup4', 4 );
	function chinchilla_customizer_setup4() {
		chinchilla_storage_merge_array( 'options', '', array(
			'cpt_end' => array(
				'type' => 'panel_end',
			),
		) );
	}
}

// 3 - add/remove Theme Options elements
if ( ! function_exists( 'chinchilla_customizer_setup3_2' ) ) {
	add_action( 'after_setup_theme', 'chinchilla_customizer_setup3_2', 3 );
	function chinchilla_customizer_setup3_2() {
		chinchilla_storage_set_array_after( 'options', 'color_schemes_info', array(
			'color_scheme_helpers' => array(
				'title'      => esc_html__( 'Show helpers', 'chinchilla' ),
				'desc'       => wp_kses_data( __( 'Display color scheme helpers in Customizer over each block with assigned color scheme', 'chinchilla' ) ),
				'std'        => 0,
				'refresh'    => false,
				'pro_only'   => CHINCHILLA_THEME_FREE,
				'type'       => 'switch',
			),
		) );
	}
}


//--------------------------------------------------------------
//-- Register Customizer Controls
//--------------------------------------------------------------

define( 'CHINCHILLA_CUSTOMIZE_PRIORITY', 200 );      // Start priority for the new controls


// Register custom controls for the customizer
if ( ! function_exists( 'chinchilla_customizer_custom_controls' ) ) {
	add_action( 'customize_register', 'chinchilla_customizer_custom_controls' );
	function chinchilla_customizer_custom_controls( $wp_customize ) {
		require_once CHINCHILLA_THEME_DIR . 'theme-options/theme-customizer-controls.php';
	}
}

// Parse Theme Options and add controls to the customizer
if ( ! function_exists( 'chinchilla_customizer_register_controls' ) ) {
	add_action( 'customize_register', 'chinchilla_customizer_register_controls', 20 );
	function chinchilla_customizer_register_controls( $wp_customize ) {

		$is_demo = false;
		if ( is_admin() ) {
			$user = wp_get_current_user();
			$is_demo = is_object( $user ) && ! empty( $user->data->user_login ) && 'backstage_customizer_user' == $user->data->user_login;
		}

		$refresh_auto = chinchilla_get_theme_setting( 'customize_refresh' ) != 'manual';

		$panels   = array( '' );
		$p        = 0;
		$sections = array( '' );
		$s        = 0;

		$expand = array();

		$i = CHINCHILLA_CUSTOMIZE_PRIORITY;

		// Reload Theme Options before create controls
		if ( is_admin() ) {
			chinchilla_storage_set( 'options_reloaded', true );
			chinchilla_load_theme_options();
		}

		$options = chinchilla_storage_get( 'options' );

		foreach ( $options as $id => $opt ) {
			$i = ! empty( $opt['priority'] )
					? $opt['priority']
					: ( in_array( $opt['type'], array( 'panel', 'section' ) )
							? CHINCHILLA_CUSTOMIZE_PRIORITY
							: $i++
						);

			if ( ! empty( $opt['hidden'] ) ) {
				continue;
			}

			if ( $is_demo && empty( $opt['demo'] ) ) {
				continue;
			}

			if ( ! isset( $opt['title'] ) ) {
				$opt['title'] = '';
			}
			if ( ! isset( $opt['desc'] ) ) {
				$opt['desc'] = '';
			}

			$transport = $refresh_auto && ( ! isset( $opt['refresh'] ) || true === $opt['refresh'] ) ? 'refresh' : 'postMessage';

			if ( ! empty( $opt['override'] ) ) {
				$opt['title'] .= ' *';
			}

			// URL to redirect preview area and/or JS callback on expand panel
			if ( in_array( $opt['type'], array( 'panel', 'section' ) ) && ! empty( $opt['expand_url'] ) || ! empty( $opt['expand_callback'] ) ) {
				$expand[ $id ] = array( 'type' => $opt['type'] );
				if ( ! empty( $opt['expand_url'] ) ) {
					$expand[ $id ]['url'] = $opt['expand_url'];
				}
				if ( ! empty( $opt['expand_callback'] ) ) {
					$expand[ $id ]['callback'] = $opt['expand_callback'];
				}
			}

			if ( 'panel' == $opt['type'] ) {

				if ( $p > 0 ) {
					array_pop( $panels );
					$p--;
				}
				if ( $s > 0 ) {
					array_pop( $sections );
					$s--;
				}

				$sec = $wp_customize->get_panel( $id );
				if ( is_object( $sec ) && ! empty( $sec->title ) ) {
					$sec->title       = $opt['title'];
					$sec->description = $opt['desc'];
					if ( ! empty( $opt['priority'] ) ) {
						$sec->priority = $opt['priority'];
					}
					if ( ! empty( $opt['active_callback'] ) ) {
						$sec->active_callback = $opt['active_callback'];
					}
				} else {
					$wp_customize->add_panel(
						esc_attr( $id ), array(
							'title'           => $opt['title'],
							'description'     => $opt['desc'],
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
						)
					);
				}

				array_push( $panels, $id );
				$p++;

			} elseif ( 'panel_end' == $opt['type'] ) {

				if ( $p > 0 ) {
					array_pop( $panels );
					$p--;
				}

			} elseif ( 'section' == $opt['type'] ) {

				if ( $s > 0 ) {
					array_pop( $sections );
					$s--;
				}

				$sec = $wp_customize->get_section( $id );
				if ( is_object( $sec ) && ! empty( $sec->title ) ) {
					$sec->title       = $opt['title'];
					$sec->description = $opt['desc'];
					$sec->panel       = esc_attr( $panels[ $p ] );
					if ( ! empty( $opt['priority'] ) ) {
						$sec->priority = $opt['priority'];
					}
					if ( ! empty( $opt['active_callback'] ) ) {
						$sec->active_callback = $opt['active_callback'];
					}
				} else {
					$wp_customize->add_section(
						esc_attr( $id ), array(
							'title'           => $opt['title'],
							'description'     => $opt['desc'],
							'panel'           => esc_attr( $panels[ $p ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
						)
					);
				}

				array_push( $sections, $id );
				$s++;

			} elseif ( 'section_end' == $opt['type'] ) {

				if ( $s > 0 ) {
					array_pop( $sections );
					$s--;
				}

			} elseif ( 'select' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => chinchilla_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead chinchilla_get_theme_option( $id )
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$args = array(
						'label'           => $opt['title'],
						'description'     => $opt['desc'],
						'section'         => esc_attr( $sections[ $s ] ),
						'priority'        => $i,
						'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
						'type'            => 'select',
						'choices'         => apply_filters( 'chinchilla_filter_options_get_list_choises', $opt['options'], $id ),
						'input_attrs'     => array(
							'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
						),
					);

				if ( ! empty( $opt['pro_only'] ) ) {
					$args['input_attrs']['data-pro-only'] = 'true';
					$wp_customize->add_control( new Chinchilla_Customize_Theme_Control( $wp_customize, $id, $args ) );
				} else {
					$wp_customize->add_control( $id, $args );
				}

			} elseif ( 'radio' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => chinchilla_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead chinchilla_get_theme_option( $id ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$args = array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'type'            => 'radio',
							'choices'         => apply_filters( 'chinchilla_filter_options_get_list_choises', $opt['options'], $id ),
							'input_attrs'     => array(
								'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
							)
						);

				if ( ! empty( $opt['pro_only'] ) ) {
					$args['input_attrs']['data-pro-only'] = 'true';
					$wp_customize->add_control( new Chinchilla_Customize_Theme_Control( $wp_customize, $id, $args ) );
				} else {
					$wp_customize->add_control( $id, $args );
				}

			} elseif ( 'checkbox' == $opt['type'] ) {												// Add " || 'switch' == $opt['type'] " to the condition to use checkbox instead switch
				$wp_customize->add_setting(
					$id, array(
						'default'           => chinchilla_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead chinchilla_get_theme_option( $id ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$args = array(
						'label'           => $opt['title'],
						'description'     => $opt['desc'],
						'section'         => esc_attr( $sections[ $s ] ),
						'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
						'priority'        => $i,
						'type'            => 'checkbox',
						'input_attrs'     => array(
							'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
						),
					);

				if ( ! empty( $opt['pro_only'] ) ) {
					$args['input_attrs']['data-pro-only'] = 'true';
					$wp_customize->add_control( new Chinchilla_Customize_Theme_Control( $wp_customize, $id, $args ) );
				} else {
					$wp_customize->add_control( $id, $args );
				}

			} elseif ( 'switch' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => chinchilla_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead chinchilla_get_theme_option( $id ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$args = array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'input_attrs'     => array(
								'value'    => chinchilla_get_theme_option( $id ),
								'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
							),
						);
				if ( ! empty( $opt['pro_only'] ) ) {
					$args['input_attrs']['data-pro-only'] = 'true';
				}

				$wp_customize->add_control( new Chinchilla_Customize_Switch_Control( $wp_customize, $id, $args ) );

			} elseif ( 'color' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => chinchilla_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead chinchilla_get_theme_option( $id ),
						'sanitize_callback' => 'sanitize_hex_color',
						'transport'         => $transport,
					)
				);

				$args = array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'priority'        => $i,
							'input_attrs'     => array(
								'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
							),
						);
				if ( ! empty( $opt['pro_only'] ) ) {
					$args['input_attrs']['data-pro-only'] = 'true';
				}

				$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, $args ) );

			} elseif ( 'image' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => chinchilla_remove_protocol_from_url( chinchilla_get_theme_option_std( $id, $opt['std'] ), false ),	// From 1.0.59 used instead chinchilla_remove_protocol_from_url( chinchilla_get_theme_option( $id ), false ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$args = array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'priority'        => $i,
							'input_attrs'     => array(
								'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
							),
						);
				if ( ! empty( $opt['pro_only'] ) ) {
					$args['input_attrs']['data-pro-only'] = 'true';
				}

				$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $id, $args ) );

			} elseif ( in_array( $opt['type'], array( 'media', 'audio', 'video' ) ) ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => chinchilla_remove_protocol_from_url( chinchilla_get_theme_option_std( $id, $opt['std'] ), false ),	// From 1.0.59 used instead chinchilla_remove_protocol_from_url( chinchilla_get_theme_option( $id ), false ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$args = array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'priority'        => $i,
							'input_attrs'     => array(
								'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
							),
						);
				if ( ! empty( $opt['pro_only'] ) ) {
					$args['input_attrs']['data-pro-only'] = 'true';
				}

				$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $id, $args ) );

			} elseif ( 'icon' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => chinchilla_remove_protocol_from_url( chinchilla_get_theme_option_std( $id, $opt['std'] ), false ),	// From 1.0.59 used instead chinchilla_remove_protocol_from_url( chinchilla_get_theme_option( $id ), false ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$args = array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'input_attrs'     => array(
								'value'    => chinchilla_get_theme_option( $id ),
								'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
							),
						);
				if ( ! empty( $opt['pro_only'] ) ) {
					$args['input_attrs']['data-pro-only'] = 'true';
				}

				$wp_customize->add_control( new Chinchilla_Customize_Icon_Control( $wp_customize, $id, $args ) );

			} elseif ( 'checklist' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => chinchilla_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead chinchilla_get_theme_option( $id ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$args = array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'choices'         => apply_filters( 'chinchilla_filter_options_get_list_choises', $opt['options'], $id ),
							'input_attrs'     => array_merge(
								$opt, array(
									'value'    => chinchilla_get_theme_option( $id ),
									'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
								)
							),
						);
				if ( ! empty( $opt['pro_only'] ) ) {
					$args['input_attrs']['data-pro-only'] = 'true';
				}

				$wp_customize->add_control( new Chinchilla_Customize_Checklist_Control( $wp_customize, $id, $args ) );

			} elseif ( 'choice' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => chinchilla_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead chinchilla_get_theme_option( $id ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$args = array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'choices'         => apply_filters( 'chinchilla_filter_options_get_list_choises', $opt['options'], $id ),
							'input_attrs'     => array_merge(
								$opt, array(
									'value'    => chinchilla_get_theme_option( $id ),
									'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
								)
							),
						);
				if ( ! empty( $opt['pro_only'] ) ) {
					$args['input_attrs']['data-pro-only'] = 'true';
				}

				$wp_customize->add_control( new Chinchilla_Customize_Choice_Control( $wp_customize, $id, $args ) );

			} elseif ( in_array( $opt['type'], array( 'slider', 'range' ) ) ) {

				$std = chinchilla_get_theme_option_std( $id, $opt['std'] );
				if ( chinchilla_is_inherit( $std ) ) {
					$std = 0;
				}

				$wp_customize->add_setting(
					$id, array(
						'default'           => $std,	// From 1.0.59 used instead chinchilla_get_theme_option( $id ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$args = array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'input_attrs'     => array_merge(
								$opt, array(
									'show_value' => ! isset( $opt['show_value'] ) || $opt['show_value'],
									'value'      => chinchilla_get_theme_option( $id ),
									'var_name'   => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
								)
							),
						);
				if ( ! empty( $opt['pro_only'] ) ) {
					$args['input_attrs']['data-pro-only'] = 'true';
				}

				$wp_customize->add_control( new Chinchilla_Customize_Range_Control( $wp_customize, $id, $args ) );


			} elseif ( 'scheme_editor' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => chinchilla_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead chinchilla_get_theme_option( $id ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$args = array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'input_attrs'     => array_merge(
								$opt, array(
									'value'    => chinchilla_get_theme_option( $id ),
									'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
								)
							),
						);
				if ( ! empty( $opt['pro_only'] ) ) {
					$args['input_attrs']['data-pro-only'] = 'true';
				}

				$wp_customize->add_control( new Chinchilla_Customize_Scheme_Editor_Control( $wp_customize, $id, $args ) );

			} elseif ( 'text_editor' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => chinchilla_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead chinchilla_get_theme_option( $id ),
						'sanitize_callback' => 'wp_kses_post',
						'transport'         => $transport,
					)
				);

				$args = array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'input_attrs'     => array_merge(
								$opt, array(
									'value'    => chinchilla_get_theme_option( $id ),
									'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
								)
							),
						);
				if ( ! empty( $opt['pro_only'] ) ) {
					$args['input_attrs']['data-pro-only'] = 'true';
				}

				$wp_customize->add_control( new Chinchilla_Customize_Text_Editor_Control( $wp_customize, $id, $args ) );

			} elseif ( 'button' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => chinchilla_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead chinchilla_get_theme_option( $id ),
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $transport,
					)
				);

				$args = array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'input_attrs'     => $opt,
						);
				if ( ! empty( $opt['pro_only'] ) ) {
					$args['input_attrs']['data-pro-only'] = 'true';
				}

				$wp_customize->add_control( new Chinchilla_Customize_Button_Control( $wp_customize, $id, $args ) );

			} elseif ( 'info' == $opt['type'] ) {

				$wp_customize->add_setting(
					$id, array(
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => 'postMessage',
					)
				);

				$args = array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
						);

				$wp_customize->add_control( new Chinchilla_Customize_Info_Control( $wp_customize, $id, $args ) );

			} elseif ( 'hidden' == $opt['type'] ) {

				if ( isset( $opt['std']) ) {		// Need for options without 'std', i.e. type => 'info'
					$wp_customize->add_setting(
						$id, array(
							'default'           => chinchilla_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead chinchilla_get_theme_option( $id ),
							'sanitize_callback' => 'chinchilla_sanitize_html',
							'transport'         => 'postMessage',
						)
					);

					$args = array(
								'label'           => $opt['title'],
								'description'     => $opt['desc'],
								'section'         => esc_attr( $sections[ $s ] ),
								'priority'        => $i,
								'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
								'input_attrs'     => array(
									'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
								),
							);

					$wp_customize->add_control( new Chinchilla_Customize_Hidden_Control( $wp_customize, $id, $args ) );
				}

			} else {    // if in_array($opt['type'], array('text', 'textarea'))

				if ( ! apply_filters( 'chinchilla_filter_register_customizer_control', false, $wp_customize, $id, $sections[ $s ], $i, $transport, $opt ) ) {
					
					if ( 'text_editor' == $opt['type'] ) {
						$opt['type'] = 'textarea';
					}

					$wp_customize->add_setting(
						$id, array(
							'default'           => chinchilla_get_theme_option_std( $id, $opt['std'] ),	// From 1.0.59 used instead chinchilla_get_theme_option( $id ),
							'sanitize_callback' => ! empty( $opt['sanitize'] )
														? $opt['sanitize']
														: ( 'text' == $opt['type']
																? 'sanitize_text_field'
																: 'wp_kses_post'
															),
							'transport'         => $transport,
						)
					);

					$args = array(
							'label'           => $opt['title'],
							'description'     => $opt['desc'],
							'section'         => esc_attr( $sections[ $s ] ),
							'priority'        => $i,
							'active_callback' => ! empty( $opt['active_callback'] ) ? $opt['active_callback'] : '',
							'type'            => $opt['type'],
							'input_attrs'     => array(
								'var_name' => ! empty( $opt['customizer'] ) ? $opt['customizer'] : '',
							),
						);

					if ( ! empty( $opt['pro_only'] ) ) {
						$args['input_attrs']['data-pro-only'] = 'true';
						$wp_customize->add_control( new Chinchilla_Customize_Theme_Control( $wp_customize, $id, $args ) );
					} else {
						$wp_customize->add_control( $id, $args );
					}

				}
			}

			// Register Partial Refresh (if supported)
			if ( $refresh_auto && isset( $opt['refresh'] ) && is_string( $opt['refresh'] )
				&& empty( $opt['pro_only'] )
				&& function_exists( "chinchilla_customizer_partial_refresh_{$id}" )
				&& isset( $wp_customize->selective_refresh )
			) {
				$wp_customize->selective_refresh->add_partial(
					$id, array(
						'selector'            => $opt['refresh'],
						'settings'            => $id,
						'render_callback'     => "chinchilla_customizer_partial_refresh_{$id}",
						'container_inclusive' => ! empty( $opt['refresh_wrapper'] ),
					)
				);
			}
		}

		// Save expand callbacks to use it in the localize scripts
		chinchilla_storage_set( 'customizer_expand', $expand );

		// Setup standard WP Controls
		// ---------------------------------

		// Reorder standard WP sections
		$sec = $wp_customize->get_panel( 'nav_menus' );
		if ( is_object( $sec ) ) {
			$sec->priority = 60;
		}
		$sec = $wp_customize->get_panel( 'widgets' );
		if ( is_object( $sec ) ) {
			$sec->priority = 61;
		}
		$sec = $wp_customize->get_section( 'static_front_page' );
		if ( is_object( $sec ) ) {
			$sec->priority = 62;
		}
		$sec = $wp_customize->get_section( 'custom_css' );
		if ( is_object( $sec ) ) {
			$sec->priority = 2000;
		}

		// Modify standard WP controls
		$sec = $wp_customize->get_control( 'blogname' );
		if ( is_object( $sec ) ) {
			$sec->description = esc_html__( 'Use "((" and "))", "{{" and "}}" to modify style and color of parts of the text, "||" to break current line', 'chinchilla' );
		}
		$sec = $wp_customize->get_setting( 'blogname' );
		if ( is_object( $sec ) ) {
			$sec->transport = 'postMessage';
		}

		$sec = $wp_customize->get_setting( 'blogdescription' );
		if ( is_object( $sec ) ) {
			$sec->transport = 'postMessage';
		}

		$sec = $wp_customize->get_control( 'site_icon' );
		if ( is_object( $sec ) ) {
			$sec->priority = 15;
		}
		$sec = $wp_customize->get_control( 'custom_logo' );
		if ( is_object( $sec ) ) {
			$sec->priority    = 50;
			$sec->description = wp_kses_data( __( 'Select or upload the site logo', 'chinchilla' ) );
		}

		$sec  = $wp_customize->get_section( 'header_image' );
		$sec2 = $wp_customize->get_control( 'header_image_info' );
		if ( is_object( $sec ) && is_object( $sec2 ) ) {
			$sec2->description = ( ! empty( $sec2->description ) ? $sec2->description . '<br>' : '' ) . $sec->description;
		}

		$sec = $wp_customize->get_control( 'header_image' );
		if ( is_object( $sec ) ) {
			$sec->priority = 300;
			$sec->section  = 'header';
		}
		$sec = $wp_customize->get_control( 'header_video' );
		if ( is_object( $sec ) ) {
			$sec->priority = 310;
			$sec->section  = 'header';
		}
		$sec = $wp_customize->get_control( 'external_header_video' );
		if ( is_object( $sec ) ) {
			$sec->priority = 320;
			$sec->section  = 'header';
		}

		$sec = $wp_customize->get_section( 'background_image' );
		if ( is_object( $sec ) ) {
			$sec->title       = esc_html__( 'Background', 'chinchilla' );
			$sec->priority    = 310;
			$sec->description = esc_html__( 'Used only if "General settings - Body style" equal to "boxed"', 'chinchilla' );
		}

		$sec = $wp_customize->get_control( 'background_color' );
		if ( is_object( $sec ) ) {
			$sec->priority = 10;
			$sec->section  = 'background_image';
		}

		// Remove unused sections
		$wp_customize->remove_section( 'colors' );
		$wp_customize->remove_section( 'header_image' );
	}
}


// Sanitize simple value - remove all tags and spaces
if ( ! function_exists( 'chinchilla_sanitize_value' ) ) {
	function chinchilla_sanitize_value( $value ) {
		return empty( $value ) ? $value : trim( strip_tags( $value ) );
	}
}


// Sanitize html value - keep only allowed tags
if ( ! function_exists( 'chinchilla_sanitize_html' ) ) {
	function chinchilla_sanitize_html( $value ) {
		return empty( $value ) ? $value : wp_kses_post( $value );
	}
}


// Return url to autofocus related field
if ( ! function_exists( 'chinchilla_customizer_get_focus_url' ) ) {
	function chinchilla_customizer_get_focus_url( $field ) {
		return admin_url( "customize.php?autofocus&#91;control&#93;={$field}" );
	}
}

// Return link to autofocus related field
if ( ! function_exists( 'chinchilla_customizer_get_focus_link' ) ) {
	function chinchilla_customizer_get_focus_link( $field, $text ) {
		return sprintf(
			'<a href="%1$s" class="chinchilla_customizer_link">%2$s</a>',
			esc_url( chinchilla_customizer_get_focus_url( $field ) ),
			$text
		);
	}
}

// Display message "Need to select widgets"
if ( ! function_exists( 'chinchilla_customizer_need_widgets_message' ) ) {
	function chinchilla_customizer_need_widgets_message( $field, $text ) {
		?><div class="chinchilla_customizer_message">
		<?php
			echo wp_kses_data(
				sprintf(
					// Translators: Add widget's name or link to focus specified section
					__( 'You have to choose widget "<b>%s</b>" in this section. You can also select any other widget, and change the purpose of this section', 'chinchilla' ),
					is_customize_preview()
						? $text
						: chinchilla_customizer_get_focus_link( $field, $text )
				)
			);
		?>
		</div>
		<?php
	}
}

// Display message "Need to install plugin ThemeREX Addons"
if ( ! function_exists( 'chinchilla_customizer_need_trx_addons_message' ) ) {
	function chinchilla_customizer_need_trx_addons_message() {
		?>
		<div class="chinchilla_customizer_message">
			<?php
			echo wp_kses_data(
				sprintf(
					// Translators: Add the link to install plugin and its name
					__( 'You need to install the <b>%s</b> plugin to be able to add Team members, Testimonials, Services and many other widgets', 'chinchilla' ),
					is_customize_preview()
						? __( 'ThemeREX Addons', 'chinchilla' )
						: sprintf(
							// Translators: Make the tag with link to install plugin
							'<a href="%1$s" class="chinchilla_customizer_link">%2$s</a>',
							esc_url(
								wp_nonce_url(
									self_admin_url( 'update.php?action=install-plugin&plugin=trx_addons' ),
									'install-plugin_trx_addons'
								)
							),
							__( 'ThemeREX Addons', 'chinchilla' )
						)
				)
			);
			echo '<br>' . wp_kses_data( __( 'Also you can insert in this section any other widgets and to modify its purpose', 'chinchilla' ) );
			?>
		</div>
		<?php
	}
}


//--------------------------------------------------------------
// Save custom settings in CSS file
//--------------------------------------------------------------

// Set a flag to regenerate styles and scripts on first run
if ( ! function_exists( 'chinchilla_set_action_save_options' ) ) {
	function chinchilla_set_action_save_options() {
		if ( chinchilla_exists_trx_addons() ) {
			update_option( 'chinchilla_action', '' );
			update_option( 'trx_addons_action', 'trx_addons_action_save_options' );
		} else {
			update_option( 'chinchilla_action', 'chinchilla_action_save_options' );
		}
	}
}


// Save CSS with custom colors and fonts after save custom options
if ( ! function_exists( 'chinchilla_customizer_action_save_after' ) ) {
	add_action( 'customize_save_after', 'chinchilla_customizer_action_save_after' );
	function chinchilla_customizer_action_save_after( $api = false ) {

		// Get saved settings
		$settings = $api->settings();

		// Store new schemes colors
		$scheme_storage = $settings['scheme_storage']->value();
		if ( $scheme_storage == serialize( chinchilla_storage_get( 'schemes_original' ) ) ) {
			remove_theme_mod( 'scheme_storage' );
		} else {
			$schemes = chinchilla_unserialize( $scheme_storage );
			if ( is_array( $schemes ) && count( $schemes ) > 0 ) {
				chinchilla_storage_set( 'schemes', $schemes );
			}
		}

		// Store new fonts parameters
		$fonts = chinchilla_get_theme_fonts();
		foreach ( $fonts as $tag => $v ) {
			foreach ( $v as $css_prop => $css_value ) {
				if ( in_array( $css_prop, array( 'title', 'description' ) ) ) {
					continue;
				}
				if ( isset( $settings[ "{$tag}_{$css_prop}" ] ) ) {
					$fonts[ $tag ][ $css_prop ] = $settings[ "{$tag}_{$css_prop}" ]->value();
				}
			}
		}
		chinchilla_storage_set( 'theme_fonts', $fonts );

		// Collect options from the external storages
		$theme_mods        = array();
		$external_storages = array();
		$options           = chinchilla_storage_get( 'options' );
		foreach ( $options as $k => $v ) {
			// Skip non-data options - sections, info, etc.
			if ( ! isset( $v['std'] ) ) {
				continue;
			}
			// Get option value from Customizer
			$value            = isset( $settings[ $k ] )
							? $settings[ $k ]->value()
							: ( in_array( $v['type'], array( 'checkbox', 'switch' ) )  ? 0 : '' );
			$theme_mods[ $k ] = $value;
			// Skip internal options
			if ( empty( $v['options_storage'] ) ) {
				continue;
			}
			// Save option to the external storage
			if ( ! isset( $external_storages[ $v['options_storage'] ] ) ) {
				$external_storages[ $v['options_storage'] ] = array();
			}
			$external_storages[ $v['options_storage'] ][ $k ] = $value;
		}

		// Update options in the external storages
		foreach ( $external_storages as $storage_name => $storage_values ) {
			$storage = get_option( $storage_name, false );
			if ( is_array( $storage ) ) {
				foreach ( $storage_values as $k => $v ) {
					$storage[ $k ] = $v;
				}
				update_option( $storage_name, apply_filters( 'chinchilla_filter_options_save', $storage, $storage_name ) );
			}
		}

		do_action( 'chinchilla_action_just_save_options', $theme_mods );

		// Update ThemeOptions save timestamp
		$stylesheet_slug = get_stylesheet();
		$stylesheet_time = time();
		update_option( "chinchilla_options_timestamp_{$stylesheet_slug}", $stylesheet_time );

		// Synchronize theme options between child and parent themes
		if ( chinchilla_get_theme_setting( 'duplicate_options' ) == 'both' ) {
			$theme_slug = get_template();
			if ( $theme_slug != $stylesheet_slug ) {
				chinchilla_customizer_duplicate_theme_options( $stylesheet_slug, $theme_slug, $stylesheet_time );
			}
		}

		// Apply action - moved to the delayed state (see below) to load all enabled modules and apply changes after
		// Attention! Don't remove comment the line below!
		// Not need here: do_action('chinchilla_action_save_options');
		update_option( 'chinchilla_action', 'chinchilla_action_save_options' );
	}
}

// Save CSS with custom colors and fonts to the custom.css
if ( ! function_exists( 'chinchilla_customizer_save_css' ) ) {
	add_action( 'chinchilla_action_save_options', 'chinchilla_customizer_save_css', 20 );
	add_action( 'trx_addons_action_save_options', 'chinchilla_customizer_save_css', 20 );
	function chinchilla_customizer_save_css() {
		$msg = '/* ' . esc_html__( "ATTENTION! This file was generated automatically! Don't change it!!!", 'chinchilla' )
				. "\n----------------------------------------------------------------------- */\n";

		// Save CSS with custom fonts and vars to the __custom.css
		$css = chinchilla_customizer_get_css();
		chinchilla_fpc( chinchilla_get_file_dir( 'css/__custom.css' ), $msg . $css );

		// Merge styles
		// CSS list must be in the next format:
		// 'relative url for css-file' => true | false
		//     true - merge this file always (to the __plugins and to the __plugins-full),
		//     false - not merge this file for optimized mode (only to the __plugins-full)
    	$css_list = apply_filters( 'chinchilla_filter_merge_styles', array() );
		chinchilla_merge_css( 'css/__plugins.css', array_keys( $css_list, true ) );
		chinchilla_merge_css( 'css/__plugins-full.css', array_keys( $css_list ) );

		// Merge responsive styles
		$css_list = apply_filters( 'chinchilla_filter_merge_styles_responsive', array(
																				'css/responsive.css' => true,
																				)
								);
		chinchilla_merge_css( 'css/__responsive.css', array_keys( $css_list, true ), true );
		chinchilla_merge_css( 'css/__responsive-full.css', array_keys( $css_list ), true );

		// If separate single styles are supported with current skin - place its to the stand-alone files
		if ( apply_filters( 'chinchilla_filters_separate_single_styles', false ) ) {
			// Merge styles for single posts
			chinchilla_merge_css( 'css/__single.css', array_keys( apply_filters( 'chinchilla_filter_merge_styles_single', array(
				'css/single.css' => true,
			) ) ) );

			// Merge responsive styles for single posts
			chinchilla_merge_css( 'css/__single-responsive.css', array_keys( apply_filters( 'chinchilla_filter_merge_styles_responsive_single', array(
				'css/single-responsive.css' => true,
			) ) ), true );
		}

		// Merge scripts
		// JS list must be in the next format:
		// 'relative url for js-file' => true | false
		//     true - merge this file always (to the __scripts and to the __scripts-full),
		//     false - not merge this file for optimized mode (only to the __scripts-full)
		$js_list = apply_filters( 'chinchilla_filter_merge_scripts', array(
			'js/skip-link-focus-fix/skip-link-focus-fix.js' => true,
			'js/utils.js' => true,
			'js/init.js' => true,
		) );
		chinchilla_merge_js( 'js/__scripts.js', array_keys( $js_list, true ) );
		chinchilla_merge_js( 'js/__scripts-full.js', array_keys( $js_list ) );
	}
}


// Convert an array items with numeric keys to the new format
// ( to compatibility with old themes )
if ( ! function_exists( 'chinchilla_merge_styles_convert_keys' ) ) {
	add_filter( 'chinchilla_filter_merge_styles', 'chinchilla_merge_styles_convert_keys', 9999, 1 );
	add_filter( 'chinchilla_filter_merge_styles_responsive', 'chinchilla_merge_styles_convert_keys', 9999, 1 );
	add_filter( 'chinchilla_filter_merge_styles_single', 'chinchilla_merge_styles_convert_keys', 9999, 1 );
	add_filter( 'chinchilla_filter_merge_styles_responsive_single', 'chinchilla_merge_styles_convert_keys', 9999, 1 );
	add_filter( 'chinchilla_filter_merge_scripts', 'chinchilla_merge_styles_convert_keys', 9999, 1 );
	function chinchilla_merge_styles_convert_keys( $list ) {
		if ( is_array( $list ) ) {
			$new_list = array();
			foreach( $list as $k => $v ) {
				if ( is_numeric( $k ) ) {
					$new_list[ $v ] = true;
				} else {
					$new_list[ $k ] = $v;
				}
			}
			$list = $new_list;
			unset( $new_list );
		}
		return $list;
	}
}


// Add theme-specific blog styles and scripts to the list
//-------------------------------------------------------------------------------
if ( ! function_exists( 'chinchilla_customizer_add_blog_styles_and_scripts' ) ) {
	function chinchilla_customizer_add_blog_styles_and_scripts( $list = false, $type = 'styles', $responsive = false ) {
		$styles = chinchilla_storage_get( 'blog_styles' );
		if ( is_array( $styles ) ) {
			if ( chinchilla_exists_trx_addons() ) {
				$styles = array_merge(
					$styles,
					array(
						'custom' => array( 'styles' => 'custom' )
					)
				);
			}
			foreach ( $styles as $k => $v ) {
				if ( ! empty( $v[ $type ] ) ) {
					foreach ( (array) $v[ $type ] as $s ) {
						if ( apply_filters( "chinchilla_filter_enqueue_blog_{$type}", true, $k, $s, $list, $responsive ) ) {
							$path = sprintf(
								'templates/blog-styles/%1$s%2$s.%3$s',
								$s,
								$responsive ? '-responsive' : '',
								'styles' == $type ? 'css' : 'js'
							);
							if ( is_array( $list ) ) {
								if ( ! isset( $list[ $path ] ) ) {
									$list[ $path ] = true;
								}
							} else {
								$path = chinchilla_get_file_url( $path );
								if ( '' != $path ) {
									if ( 'scripts' == $type ) {
										wp_enqueue_script( 'chinchilla-blog-script-' . esc_attr( $s ), $path, array( 'jquery' ), null, true );
									} else {
										wp_enqueue_style( 'chinchilla-blog-style-' . esc_attr( $s . ( $responsive ? '-responsive' : '' ) ),  $path, array(), null, $responsive ? chinchilla_media_for_load_css_responsive( 'blog-styles' ) : 'all' );
									}
								}
							}
						}
					}
				}
			}
		}
		return $list;
	}
}

// Merge theme-specific blog styles
if ( ! function_exists( 'chinchilla_customizer_merge_blog_styles' ) ) {
	add_filter( 'chinchilla_filter_merge_styles', 'chinchilla_customizer_merge_blog_styles', 8, 1 );
	function chinchilla_customizer_merge_blog_styles( $list ) {
		return chinchilla_customizer_add_blog_styles_and_scripts( $list, 'styles' );
	}
}

// Merge theme-specific blog styles
if ( ! function_exists( 'chinchilla_customizer_merge_blog_styles_responsive' ) ) {
	add_filter( 'chinchilla_filter_merge_styles_responsive', 'chinchilla_customizer_merge_blog_styles_responsive', 8, 1 );
	function chinchilla_customizer_merge_blog_styles_responsive( $list ) {
		return chinchilla_customizer_add_blog_styles_and_scripts( $list, 'styles', true );
	}
}

// Merge theme-specific blog scripts
if ( ! function_exists( 'chinchilla_customizer_merge_blog_scripts' ) ) {
	add_filter( 'chinchilla_filter_merge_scripts', 'chinchilla_customizer_merge_blog_scripts' );
	function chinchilla_customizer_merge_blog_scripts( $list ) {
		return chinchilla_customizer_add_blog_styles_and_scripts( $list, 'scripts' );
	}
}

// Enqueue theme-specific blog scripts
if ( ! function_exists( 'chinchilla_customizer_blog_styles' ) ) {
	add_action( 'wp_enqueue_scripts', 'chinchilla_customizer_blog_styles', 1100 );
	function chinchilla_customizer_blog_styles() {
		if ( chinchilla_is_on( chinchilla_get_theme_option( 'debug_mode' ) ) ) {
			chinchilla_customizer_add_blog_styles_and_scripts( false, 'styles' );
			chinchilla_customizer_add_blog_styles_and_scripts( false, 'scripts' );
		}
	}
}

// Enqueue theme-specific blog scripts for responsive
if ( ! function_exists( 'chinchilla_customizer_blog_styles_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'chinchilla_customizer_blog_styles_responsive', 2000 );
	function chinchilla_customizer_blog_styles_responsive() {
		if ( chinchilla_is_on( chinchilla_get_theme_option( 'debug_mode' ) ) ) {
			chinchilla_customizer_add_blog_styles_and_scripts( false, 'styles', true );
		}
	}
}


// Add theme-specific single styles and scripts to the list
//-------------------------------------------------------------------------------
if ( ! function_exists( 'chinchilla_customizer_add_single_styles_and_scripts' ) ) {
	function chinchilla_customizer_add_single_styles_and_scripts( $list = false, $type = 'styles', $responsive = false, $single_style = '' ) {
		$styles = chinchilla_storage_get( 'single_styles' );
		if ( is_array( $styles ) && ( is_array( $list ) || apply_filters( 'chinchilla_filter_single_post_header', chinchilla_is_singular( 'post' ) || chinchilla_is_singular( 'attachment' ) ) ) ) {
			if ( empty( $single_style ) ) {
				$single_style = chinchilla_get_theme_option( 'single_style' );
			}
			foreach ( $styles as $k => $v ) {
				if ( ( is_array( $list ) || $k == $single_style ) && ! empty( $v[ $type ] ) ) {
					foreach ( (array) $v[ $type ] as $s ) {
						$path = sprintf(
							'templates/single-styles/%1$s%2$s.%3$s',
							$s,
							$responsive ? '-responsive' : '',
							'styles' == $type ? 'css' : 'js'
						);
						if ( is_array( $list ) ) {
							if ( ! isset( $list[ $path ] ) ) {
								$list[ $path ] = true;
							}
						} else {
							$path = chinchilla_get_file_url( $path );
							if ( '' != $path ) {
								if ( 'scripts' == $type ) {
									wp_enqueue_script( 'chinchilla-single-script-' . esc_attr( $s ), $path, array( 'jquery' ), null, true );
								} else {
									if ( false === $list 
										&& chinchilla_is_on( chinchilla_get_theme_option( 'debug_mode' ) ) 
										&& chinchilla_get_theme_option( 'posts_navigation' ) === 'scroll' 
										&& in_array( trx_addons_get_value_gp( 'action' ), array( 'prev_post_loading' ) )
									) {
										chinchilla_add_inline_css( chinchilla_fgc( $path ) );
									} else {
										wp_enqueue_style( 'chinchilla-single-style-' . esc_attr( $s . ( $responsive ? '-responsive' : '' ) ),  $path, array(), null, $responsive ? chinchilla_media_for_load_css_responsive( 'single-styles' ) : 'all' );
									}
								}
							}
						}
					}
				}
			}
		}
		return $list;
	}
}

// Merge theme-specific single styles
if ( ! function_exists( 'chinchilla_customizer_merge_single_styles' ) ) {
	add_filter( 'chinchilla_filter_merge_styles', 'chinchilla_customizer_merge_single_styles', 8, 1 );
	add_filter( 'chinchilla_filter_merge_styles_single', 'chinchilla_customizer_merge_single_styles', 8, 1 );
	function chinchilla_customizer_merge_single_styles( $list ) {
		// If separate single styles is supported with current skin
		if ( apply_filters( 'chinchilla_filters_separate_single_styles', false ) ) {
			if ( current_filter() == 'chinchilla_filter_merge_styles_single' ) {
				return chinchilla_customizer_add_single_styles_and_scripts( $list, 'styles' );
			}
		} else {   // If separate single styles is not supported with current skin - place all styles together
			if ( current_filter() == 'chinchilla_filter_merge_styles' ) {
				return chinchilla_customizer_add_single_styles_and_scripts( $list, 'styles' );
			}
		}
		return $list;
	}
}

// Merge theme-specific single styles
if ( ! function_exists( 'chinchilla_customizer_merge_single_styles_responsive' ) ) {
	add_filter( 'chinchilla_filter_merge_styles_responsive', 'chinchilla_customizer_merge_single_styles_responsive', 8, 1 );
	add_filter( 'chinchilla_filter_merge_styles_responsive_single', 'chinchilla_customizer_merge_single_styles_responsive', 8, 1 );
	function chinchilla_customizer_merge_single_styles_responsive( $list ) {
		if ( apply_filters( 'chinchilla_filters_separate_single_styles', false ) ) {
			// If separate single styles is supported with current skin
			if ( current_filter() == 'chinchilla_filter_merge_styles_responsive_single' ) {
				return chinchilla_customizer_add_single_styles_and_scripts( $list, 'styles', true );
			}
		} else {
			// If separate single styles is not supported with current skin - place all styles together
			if ( current_filter() == 'chinchilla_filter_merge_styles_responsive' ) {
				return chinchilla_customizer_add_single_styles_and_scripts( $list, 'styles', true );
			}
		}
		return $list;
	}
}

// Merge theme-specific single scripts
if ( ! function_exists( 'chinchilla_customizer_merge_single_scripts' ) ) {
	add_filter( 'chinchilla_filter_merge_scripts', 'chinchilla_customizer_merge_single_scripts' );
	function chinchilla_customizer_merge_single_scripts( $list ) {
		return chinchilla_customizer_add_single_styles_and_scripts( $list, 'scripts' );
	}
}

// Enqueue theme-specific single scripts
if ( ! function_exists( 'chinchilla_customizer_single_styles' ) ) {
	add_action( 'wp_enqueue_scripts', 'chinchilla_customizer_single_styles', 1020 );
	function chinchilla_customizer_single_styles() {
		if ( chinchilla_is_on( chinchilla_get_theme_option( 'debug_mode' ) )
			&& apply_filters( 'chinchilla_filters_load_single_styles', chinchilla_is_singular( 'post' ) || chinchilla_is_singular( 'attachment' ) || (int) chinchilla_get_theme_option( 'open_full_post_in_blog' ) > 0 )
		) {
			chinchilla_customizer_add_single_styles_and_scripts( false, 'styles' );
			chinchilla_customizer_add_single_styles_and_scripts( false, 'scripts' );
		}
	}
}

// Enqueue theme-specific single scripts for responsive
if ( ! function_exists( 'chinchilla_customizer_single_styles_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'chinchilla_customizer_single_styles_responsive', 2020 );
	function chinchilla_customizer_single_styles_responsive() {
		if ( chinchilla_is_on( chinchilla_get_theme_option( 'debug_mode' ) )
			&& apply_filters( 'chinchilla_filters_load_single_styles', chinchilla_is_singular( 'post' ) || chinchilla_is_singular( 'attachment' ) || (int) chinchilla_get_theme_option( 'open_full_post_in_blog' ) > 0 )
		) {
			chinchilla_customizer_add_single_styles_and_scripts( false, 'styles', true );
		}
	}
}


//--------------------------------------------------------------
// Customizer JS and CSS
//--------------------------------------------------------------

// Binds JS listener to Customizer controls.
if ( ! function_exists( 'chinchilla_customizer_control_js' ) ) {
	add_action( 'customize_controls_enqueue_scripts', 'chinchilla_customizer_control_js' );
	function chinchilla_customizer_control_js() {
		wp_enqueue_style( 'chinchilla-customizer', chinchilla_get_file_url( 'theme-options/theme-customizer.css' ), array(), null );
		wp_enqueue_script(
			'chinchilla-customizer',
			chinchilla_get_file_url( 'theme-options/theme-customizer.js' ),
			array( 'customize-controls', 'iris', 'underscore', 'wp-util' ), null, true
		);
		wp_enqueue_style(  'spectrum', chinchilla_get_file_url( 'js/colorpicker/spectrum/spectrum.css' ), array(), null );
		wp_enqueue_script( 'spectrum', chinchilla_get_file_url( 'js/colorpicker/spectrum/spectrum.js' ), array( 'jquery' ), null, true );
		wp_localize_script( 'chinchilla-customizer', 'chinchilla_color_schemes', chinchilla_storage_get( 'schemes' ) );
		wp_localize_script( 'chinchilla-customizer', 'chinchilla_simple_schemes', chinchilla_storage_get( 'schemes_simple' ) );
		wp_localize_script( 'chinchilla-customizer', 'chinchilla_sorted_schemes', chinchilla_storage_get( 'schemes_sorted' ) );
		wp_localize_script( 'chinchilla-customizer', 'chinchilla_color_presets', chinchilla_get_color_presets() );
		wp_localize_script( 'chinchilla-customizer', 'chinchilla_additional_colors', chinchilla_storage_get( 'scheme_colors_add' ) );
		wp_localize_script( 'chinchilla-customizer', 'chinchilla_theme_fonts', chinchilla_storage_get( 'theme_fonts' ) );
		wp_localize_script( 'chinchilla-customizer', 'chinchilla_font_presets', chinchilla_get_font_presets() );
		wp_localize_script( 'chinchilla-customizer', 'chinchilla_theme_vars', chinchilla_get_theme_vars() );
		wp_localize_script(
			'chinchilla-customizer', 'chinchilla_customizer_vars', apply_filters(
				'chinchilla_filter_customizer_vars', array(
					'max_load_fonts'     => chinchilla_get_theme_setting( 'max_load_fonts' ),
					'decorate_fonts'     => chinchilla_get_theme_setting( 'decorate_fonts' ),
					'page_width_default' => apply_filters( 'chinchilla_filter_content_width', chinchilla_get_theme_option( 'page_width' ) ),
					'msg_reset'          => esc_html__( 'Reset', 'chinchilla' ),
					'msg_reset_confirm'  => esc_html__( 'Are you sure you want to reset all Theme Options?', 'chinchilla' ),
					'msg_reload'         => esc_html__( 'Reload', 'chinchilla' ),
					'msg_reload_title'   => esc_html__( 'Reload preview area to display changes', 'chinchilla' ),
					'actions'            => array(
						'expand' => chinchilla_storage_get( 'customizer_expand', array() ),
					),
				)
			)
		);
		wp_localize_script( 'chinchilla-customizer', 'chinchilla_dependencies', chinchilla_get_theme_dependencies() );
		chinchilla_admin_scripts(true);
		chinchilla_admin_localize_scripts();
	}
}


// Binds JS handlers to make the Customizer preview reload changes asynchronously.
if ( ! function_exists( 'chinchilla_customizer_preview_js' ) ) {
	add_action( 'customize_preview_init', 'chinchilla_customizer_preview_js' );
	function chinchilla_customizer_preview_js() {
		wp_enqueue_script(
			'chinchilla-customizer-preview',
			chinchilla_get_file_url( 'theme-options/theme-customizer-preview.js' ),
			array( 'customize-preview' ), null, true
		);
		wp_localize_script( 'chinchilla-customizer-preview', 'chinchilla_color_schemes', chinchilla_storage_get( 'schemes' ) );
	}
}

// Output an Underscore template for generating CSS for the color scheme.
// The template generates the css dynamically for instant display in the Customizer preview.
if ( ! function_exists( 'chinchilla_customizer_css_template' ) ) {
	add_action( 'customize_controls_print_footer_scripts', 'chinchilla_customizer_css_template' );
	function chinchilla_customizer_css_template() {
		$colors = array();
		foreach ( chinchilla_get_scheme_colors() as $k => $v ) {
			$colors[ $k ] = '{{ data.' . esc_attr( $k ) . ' }}';
		}

		$tmpl_holder = 'script';

		$schemes = array_keys( chinchilla_get_list_schemes() );
		if ( count( $schemes ) > 0 ) {
			foreach ( $schemes as $scheme ) {
				chinchilla_show_layout(
					chinchilla_customizer_get_css(
						array(
							'colors'        => $colors,
							'scheme'        => $scheme,
							'fonts'         => false,
							'vars'          => false,
							'remove_spaces' => false,
						)
					),
					'<' . esc_html( $tmpl_holder ) . ' type="text/html" id="tmpl-chinchilla-color-scheme-' . esc_attr( $scheme ) . '">',
					'</' . esc_html( $tmpl_holder ) . '>'
				);
			}
		}

		// Fonts
		$fonts = chinchilla_get_theme_fonts();
		if ( is_array( $fonts ) && count( $fonts ) > 0 ) {
			foreach ( $fonts as $tag => $font ) {
				$fonts[ $tag ]['font-family']     = '{{ data["' . $tag . '"]["font-family"] }}';
				$fonts[ $tag ]['font-size']       = '{{ data["' . $tag . '"]["font-size"] }}';
				$fonts[ $tag ]['line-height']     = '{{ data["' . $tag . '"]["line-height"] }}';
				$fonts[ $tag ]['font-weight']     = '{{ data["' . $tag . '"]["font-weight"] }}';
				$fonts[ $tag ]['font-style']      = '{{ data["' . $tag . '"]["font-style"] }}';
				$fonts[ $tag ]['text-decoration'] = '{{ data["' . $tag . '"]["text-decoration"] }}';
				$fonts[ $tag ]['text-transform']  = '{{ data["' . $tag . '"]["text-transform"] }}';
				$fonts[ $tag ]['letter-spacing']  = '{{ data["' . $tag . '"]["letter-spacing"] }}';
				$fonts[ $tag ]['margin-top']      = '{{ data["' . $tag . '"]["margin-top"] }}';
				$fonts[ $tag ]['margin-bottom']   = '{{ data["' . $tag . '"]["margin-bottom"] }}';
			}
			chinchilla_show_layout(
				chinchilla_customizer_get_css(
					array(
						'colors'        => false,
						'scheme'        => '',
						'fonts'         => $fonts,
						'vars'          => false,
						'remove_spaces' => false,
					)
				),
				'<' . esc_html( $tmpl_holder ) . ' type="text/html" id="tmpl-chinchilla-fonts">',
				'</' . esc_html( $tmpl_holder ) . '>'
			);
		}

		// Theme vars
		$vars = chinchilla_get_theme_vars();
		if ( is_array( $vars ) && count( $vars ) > 0 ) {
			foreach ( $vars as $k => $v ) {
				$vars[ $k ] = '{{ data.' . esc_attr( $k ) . ' }}';
			}
			chinchilla_show_layout(
				chinchilla_customizer_get_css(
					array(
						'colors'        => false,
						'scheme'        => '',
						'fonts'         => false,
						'vars'          => $vars,
						'remove_spaces' => false,
					)
				),
				'<' . esc_html( $tmpl_holder ) . ' type="text/html" id="tmpl-chinchilla-vars">',
				'</' . esc_html( $tmpl_holder ) . '>'
			);
		}

	}
}


// Additional (calculated) theme-specific colors
// Attention! Don't forget setup additional colors also in the theme-customizer.js
if ( ! function_exists( 'chinchilla_customizer_add_theme_colors' ) ) {
	function chinchilla_customizer_add_theme_colors( $colors ) {
		$add = chinchilla_storage_get( 'scheme_colors_add' );
		if ( is_array( $add ) ) {
			foreach ( $add as $k => $v ) {
				if ( substr( $colors['text'], 0, 1 ) == '#' ) {
					$clr = $colors[ $v['color'] ];
					if ( isset( $v['hue'] ) || isset( $v['saturation'] ) || isset( $v['brightness'] ) ) {
						$clr = chinchilla_hsb2hex(
							chinchilla_hex2hsb(
								$clr,
								isset( $v['hue'] ) ? $v['hue'] : 0,
								isset( $v['saturation'] ) ? $v['saturation'] : 0,
								isset( $v['brightness'] ) ? $v['brightness'] : 0
							)
						);
					}
					if ( isset( $v['alpha'] ) ) {
						$clr = chinchilla_hex2rgba( $clr, $v['alpha'] );
					}
					$colors[ $k ] = $clr;
				} else {
					$colors[ $k ] = sprintf( '{{ data.%s }}', $k );
				}
			}
		}
		return $colors;
	}
}



// Additional theme-specific fonts rules
// Attention! Don't forget setup fonts rules also in the theme-customizer.js
if ( ! function_exists( 'chinchilla_customizer_add_theme_fonts' ) ) {
	function chinchilla_customizer_add_theme_fonts( $fonts ) {
		$rez = array();
		foreach ( $fonts as $tag => $font ) {
			
			$rez[ $tag ] = $font;
			
			if ( substr( $font['font-family'], 0, 2 ) != '{{' ) {
				$rez[ $tag . '_font-family' ]     = ! empty( $font['font-family'] ) && ! chinchilla_is_inherit( $font['font-family'] )
														? 'font-family:' . trim( $font['font-family'] ) . ';'
														: '';
				$rez[ $tag . '_font-size' ]       = ! empty( $font['font-size'] ) && ! chinchilla_is_inherit( $font['font-size'] )
														? 'font-size:' . chinchilla_prepare_css_value( $font['font-size'] ) . ';'
														: '';
				$rez[ $tag . '_line-height' ]     = ! empty( $font['line-height'] ) && ! chinchilla_is_inherit( $font['line-height'] )
														? 'line-height:' . trim( $font['line-height'] ) . ';'
														: '';
				$rez[ $tag . '_font-weight' ]     = ! empty( $font['font-weight'] ) && ! chinchilla_is_inherit( $font['font-weight'] )
														? 'font-weight:' . trim( $font['font-weight'] ) . ';'
														: '';
				$rez[ $tag . '_font-style' ]      = ! empty( $font['font-style'] ) && ! chinchilla_is_inherit( $font['font-style'] )
														? 'font-style:' . trim( $font['font-style'] ) . ';'
														: '';
				$rez[ $tag . '_text-decoration' ] = ! empty( $font['text-decoration'] ) && ! chinchilla_is_inherit( $font['text-decoration'] )
														? 'text-decoration:' . trim( $font['text-decoration'] ) . ';'
														: '';
				$rez[ $tag . '_text-transform' ]  = ! empty( $font['text-transform'] ) && ! chinchilla_is_inherit( $font['text-transform'] )
														? 'text-transform:' . trim( $font['text-transform'] ) . ';'
														: '';
				$rez[ $tag . '_letter-spacing' ]  = ! empty( $font['letter-spacing'] ) && ! chinchilla_is_inherit( $font['letter-spacing'] )
														? 'letter-spacing:' . chinchilla_prepare_css_value( $font['letter-spacing'] ) . ';'
														: '';
				$rez[ $tag . '_margin-top' ]      = ! empty( $font['margin-top'] ) && ! chinchilla_is_inherit( $font['margin-top'] )
														? 'margin-top:' . chinchilla_prepare_css_value( $font['margin-top'] ) . ';'
														: '';
				$rez[ $tag . '_margin-bottom' ]   = ! empty( $font['margin-bottom'] ) && ! chinchilla_is_inherit( $font['margin-bottom'] )
														? 'margin-bottom:' . chinchilla_prepare_css_value( $font['margin-bottom'] ) . ';'
														: '';
			} else {
				$rez[ $tag . '_font-family' ]     = '{{ data["' . $tag . '_font-family"] }}';
				$rez[ $tag . '_font-size' ]       = '{{ data["' . $tag . '_font-size"] }}';
				$rez[ $tag . '_line-height' ]     = '{{ data["' . $tag . '_line-height"] }}';
				$rez[ $tag . '_font-weight' ]     = '{{ data["' . $tag . '_font-weight"] }}';
				$rez[ $tag . '_font-style' ]      = '{{ data["' . $tag . '_font-style"] }}';
				$rez[ $tag . '_text-decoration' ] = '{{ data["' . $tag . '_text-decoration"] }}';
				$rez[ $tag . '_text-transform' ]  = '{{ data["' . $tag . '_text-transform"] }}';
				$rez[ $tag . '_letter-spacing' ]  = '{{ data["' . $tag . '_letter-spacing"] }}';
				$rez[ $tag . '_margin-top' ]      = '{{ data["' . $tag . '_margin-top"] }}';
				$rez[ $tag . '_margin-bottom' ]   = '{{ data["' . $tag . '_margin-bottom"] }}';
			}
		}
		return $rez;
	}
}



// Additional theme-specific vars rules
// Attention! Don't forget setup vars rules also in the theme-customizer.js
if ( ! function_exists( 'chinchilla_customizer_add_theme_vars' ) ) {
	function chinchilla_customizer_add_theme_vars( $vars ) {
		$rez = $vars;
		// Add border radius
		if ( isset( $vars['rad'] ) ) {
			if ( substr( $vars['rad'], 0, 2 ) != '{{' ) {
				$rez['rad']      = chinchilla_prepare_css_value( ! empty( $vars['rad'] ) ? $vars['rad'] : 0 );
				$rez['rad_koef'] = ! empty( $vars['rad'] ) ? 1 : 0;
			} else {
				$rez['rad_koef'] = '{{ data.rad_koef }}';
			}
		}
		// Add page components
		if ( isset( $vars['page_width'] ) ) {
			if ( substr( $vars['page_width'], 0, 2 ) != '{{' ) {
				if ( empty( $vars['page_width'] ) ) {
					$vars['page_width'] = apply_filters( 'chinchilla_filter_content_width', chinchilla_get_theme_option( 'page_width' ) );
				}
				$rez['page_width']          = chinchilla_prepare_css_value( $vars['page_width'] );
				$rez['page_boxed_extra']    = chinchilla_prepare_css_value( $vars['page_boxed_extra'] );
				$rez['page_fullwide_extra'] = chinchilla_prepare_css_value( $vars['page_fullwide_extra'] );
				$rez['page_fullwide_max']   = chinchilla_prepare_css_value( $vars['page_fullwide_max'] );
				$rez['grid_gap']            = chinchilla_prepare_css_value( $vars['grid_gap'] );
				$rez['sidebar_prc']         = $vars['sidebar_width'] / $vars['page_width'];
				$rez['sidebar_gap_prc']     = $vars['sidebar_gap'] / $vars['page_width'];
			} else {
				$rez['sidebar_prc']     = '{{ data.sidebar_prc }}';
				$rez['sidebar_gap_prc'] = '{{ data.sidebar_gap_prc }}';
			}
		}
		return apply_filters( 'chinchilla_filter_add_theme_vars', $rez, $vars );
	}
}


//----------------------------------------------------------------------------------------------
// Add fix to allow theme-specific sidebars in Customizer (if is_customize_preview() mode)
//----------------------------------------------------------------------------------------------
if ( ! function_exists( 'chinchilla_customizer_fix_sidebars' ) && is_customize_preview() && is_front_page() ) {
	add_action( 'wp_footer', 'chinchilla_customizer_fix_sidebars' );
	function chinchilla_customizer_fix_sidebars() {
		$sidebars = chinchilla_get_sidebars();
		if ( is_array( $sidebars ) ) {
			foreach ( $sidebars as $sb => $params ) {
				if ( ! empty( $params['front_page_section'] ) && is_active_sidebar( $sb ) ) {
					?>
					<div class="hidden"><?php dynamic_sidebar( $sb ); ?></div><?php
				}
			}
		}
	}
}


// Load theme options and styles
require_once CHINCHILLA_THEME_DIR . 'theme-specific/theme-setup.php';
require_once CHINCHILLA_THEME_DIR . 'theme-options/theme-customizer-css-vars.php';
require_once CHINCHILLA_THEME_DIR . 'theme-options/theme-options.php';
require_once CHINCHILLA_THEME_DIR . 'theme-options/theme-options-override.php';
if ( ! CHINCHILLA_THEME_FREE ) {
	require_once CHINCHILLA_THEME_DIR . 'theme-options/theme-options-qsetup.php';
}

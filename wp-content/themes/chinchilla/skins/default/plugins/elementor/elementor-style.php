<?php
// Add plugin-specific fonts to the custom CSS
if ( ! function_exists( 'chinchilla_elm_get_css' ) ) {
    add_filter( 'chinchilla_filter_get_css', 'chinchilla_elm_get_css', 10, 2 );
    function chinchilla_elm_get_css( $css, $args ) {

        if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
            $fonts         = $args['fonts'];
            $css['fonts'] .= <<<CSS
.elementor-widget-progress .elementor-title,
.elementor-widget-progress .elementor-progress-percentage,
.elementor-widget-toggle .elementor-toggle-title,
.elementor-widget-tabs .elementor-tab-title,
.custom_icon_btn.elementor-widget-button .elementor-button .elementor-button-text,
.elementor-widget-counter .elementor-counter-number-wrapper,
.elementor-widget-counter .elementor-counter-title {
	{$fonts['h5_font-family']}
}
.elementor-widget-icon-box .elementor-widget-container .elementor-icon-box-title small {
    {$fonts['p_font-family']}
}

CSS;
        }

        return $css;
    }
}


// Add theme-specific CSS-animations
if ( ! function_exists( 'chinchilla_elm_add_theme_animations' ) ) {
	add_filter( 'elementor/controls/animations/additional_animations', 'chinchilla_elm_add_theme_animations' );
	function chinchilla_elm_add_theme_animations( $animations ) {
		/* To add a theme-specific animations to the list:
			1) Merge to the array 'animations': array(
													esc_html__( 'Theme Specific', 'chinchilla' ) => array(
														'ta_custom_1' => esc_html__( 'Custom 1', 'chinchilla' )
													)
												)
			2) Add a CSS rules for the class '.ta_custom_1' to create a custom entrance animation
		*/
		$animations = array_merge(
						$animations,
						array(
							esc_html__( 'Theme Specific', 'chinchilla' ) => array(
									'ta_under_strips' => esc_html__( 'Under the strips', 'chinchilla' ),
									'chinchilla-fadeinup' => esc_html__( 'Chinchilla - Fade In Up', 'chinchilla' ),
									'chinchilla-fadeinright' => esc_html__( 'Chinchilla - Fade In Right', 'chinchilla' ),
									'chinchilla-fadeinleft' => esc_html__( 'Chinchilla - Fade In Left', 'chinchilla' ),
									'chinchilla-fadeindown' => esc_html__( 'Chinchilla - Fade In Down', 'chinchilla' ),
									'chinchilla-fadein' => esc_html__( 'Chinchilla - Fade In', 'chinchilla' ),
									'chinchilla-infinite-rotate' => esc_html__( 'Chinchilla - Infinite Rotate', 'chinchilla' ),
								)
							)
						);

		return $animations;
	}
}

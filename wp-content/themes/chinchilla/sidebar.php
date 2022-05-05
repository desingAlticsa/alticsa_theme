<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package CHINCHILLA
 * @since CHINCHILLA 1.0
 */

if ( chinchilla_sidebar_present() ) {
	
	$chinchilla_sidebar_type = chinchilla_get_theme_option( 'sidebar_type' );
	if ( 'custom' == $chinchilla_sidebar_type && ! chinchilla_is_layouts_available() ) {
		$chinchilla_sidebar_type = 'default';
	}
	
	// Catch output to the buffer
	ob_start();
	if ( 'default' == $chinchilla_sidebar_type ) {
		// Default sidebar with widgets
		$chinchilla_sidebar_name = chinchilla_get_theme_option( 'sidebar_widgets' );
		chinchilla_storage_set( 'current_sidebar', 'sidebar' );
		if ( is_active_sidebar( $chinchilla_sidebar_name ) ) {
			dynamic_sidebar( $chinchilla_sidebar_name );
		}
	} else {
		// Custom sidebar from Layouts Builder
		$chinchilla_sidebar_id = chinchilla_get_custom_sidebar_id();
		do_action( 'chinchilla_action_show_layout', $chinchilla_sidebar_id );
	}
	$chinchilla_out = trim( ob_get_contents() );
	ob_end_clean();
	
	// If any html is present - display it
	if ( ! empty( $chinchilla_out ) ) {
		$chinchilla_sidebar_position    = chinchilla_get_theme_option( 'sidebar_position' );
		$chinchilla_sidebar_position_ss = chinchilla_get_theme_option( 'sidebar_position_ss' );
		?>
		<div class="sidebar widget_area
			<?php
			echo ' ' . esc_attr( $chinchilla_sidebar_position );
			echo ' sidebar_' . esc_attr( $chinchilla_sidebar_position_ss );
			echo ' sidebar_' . esc_attr( $chinchilla_sidebar_type );

			if ( 'float' == $chinchilla_sidebar_position_ss ) {
				echo ' sidebar_float';
			}
			$chinchilla_sidebar_scheme = chinchilla_get_theme_option( 'sidebar_scheme' );
			if ( ! empty( $chinchilla_sidebar_scheme ) && ! chinchilla_is_inherit( $chinchilla_sidebar_scheme ) ) {
				echo ' scheme_' . esc_attr( $chinchilla_sidebar_scheme );
			}
			?>
		" role="complementary">
			<?php

			// Skip link anchor to fast access to the sidebar from keyboard
			?>
			<a id="sidebar_skip_link_anchor" class="chinchilla_skip_link_anchor" href="#"></a>
			<?php

			do_action( 'chinchilla_action_before_sidebar_wrap', 'sidebar' );

			// Button to show/hide sidebar on mobile
			if ( in_array( $chinchilla_sidebar_position_ss, array( 'above', 'float' ) ) ) {
				$chinchilla_title = apply_filters( 'chinchilla_filter_sidebar_control_title', 'float' == $chinchilla_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'chinchilla' ) : '' );
				$chinchilla_text  = apply_filters( 'chinchilla_filter_sidebar_control_text', 'above' == $chinchilla_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'chinchilla' ) : '' );
				?>
				<a href="#" class="sidebar_control" title="<?php echo esc_attr( $chinchilla_title ); ?>"><?php echo esc_html( $chinchilla_text ); ?></a>
				<?php
			}
			?>
			<div class="sidebar_inner">
				<?php
				do_action( 'chinchilla_action_before_sidebar', 'sidebar' );
				chinchilla_show_layout( preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $chinchilla_out ) );
				do_action( 'chinchilla_action_after_sidebar', 'sidebar' );
				?>
			</div>
			<?php

			do_action( 'chinchilla_action_after_sidebar_wrap', 'sidebar' );

			?>
		</div>
		<div class="clearfix"></div>
		<?php
	}
}

<?php
/**
 * The Header: Logo and main menu
 *
 * @package CHINCHILLA
 * @since CHINCHILLA 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js<?php
	// Class scheme_xxx need in the <html> as context for the <body>!
	echo ' scheme_' . esc_attr( chinchilla_get_theme_option( 'color_scheme' ) );
?>">

<head>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php
	if ( function_exists( 'wp_body_open' ) ) {
		wp_body_open();
	} else {
		do_action( 'wp_body_open' );
	}
	do_action( 'chinchilla_action_before_body' );
	?>

	<div class="<?php echo esc_attr( apply_filters( 'chinchilla_filter_body_wrap_class', 'body_wrap' ) ); ?>" <?php do_action('chinchilla_action_body_wrap_attributes'); ?>>

		<?php do_action( 'chinchilla_action_before_page_wrap' ); ?>

		<div class="<?php echo esc_attr( apply_filters( 'chinchilla_filter_page_wrap_class', 'page_wrap' ) ); ?>" <?php do_action('chinchilla_action_page_wrap_attributes'); ?>>

			<?php do_action( 'chinchilla_action_page_wrap_start' ); ?>

			<?php
			$chinchilla_full_post_loading = ( chinchilla_is_singular( 'post' ) || chinchilla_is_singular( 'attachment' ) ) && chinchilla_get_value_gp( 'action' ) == 'full_post_loading';
			$chinchilla_prev_post_loading = ( chinchilla_is_singular( 'post' ) || chinchilla_is_singular( 'attachment' ) ) && chinchilla_get_value_gp( 'action' ) == 'prev_post_loading';

			// Don't display the header elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ! $chinchilla_full_post_loading && ! $chinchilla_prev_post_loading ) {

				// Short links to fast access to the content, sidebar and footer from the keyboard
				?>
				<a class="chinchilla_skip_link skip_to_content_link" href="#content_skip_link_anchor" tabindex="1"><?php esc_html_e( "Skip to content", 'chinchilla' ); ?></a>
				<?php if ( chinchilla_sidebar_present() ) { ?>
				<a class="chinchilla_skip_link skip_to_sidebar_link" href="#sidebar_skip_link_anchor" tabindex="1"><?php esc_html_e( "Skip to sidebar", 'chinchilla' ); ?></a>
				<?php } ?>
				<a class="chinchilla_skip_link skip_to_footer_link" href="#footer_skip_link_anchor" tabindex="1"><?php esc_html_e( "Skip to footer", 'chinchilla' ); ?></a>

				<?php
				do_action( 'chinchilla_action_before_header' );

				// Header
				$chinchilla_header_type = chinchilla_get_theme_option( 'header_type' );
				if ( 'custom' == $chinchilla_header_type && ! chinchilla_is_layouts_available() ) {
					$chinchilla_header_type = 'default';
				}
				get_template_part( apply_filters( 'chinchilla_filter_get_template_part', "templates/header-" . sanitize_file_name( $chinchilla_header_type ) ) );

				// Side menu
				if ( in_array( chinchilla_get_theme_option( 'menu_side' ), array( 'left', 'right' ) ) ) {
					get_template_part( apply_filters( 'chinchilla_filter_get_template_part', 'templates/header-navi-side' ) );
				}

				// Mobile menu
				get_template_part( apply_filters( 'chinchilla_filter_get_template_part', 'templates/header-navi-mobile' ) );

				do_action( 'chinchilla_action_after_header' );

			}
			?>

			<?php do_action( 'chinchilla_action_before_page_content_wrap' ); ?>

			<div class="page_content_wrap<?php
				if ( chinchilla_is_off( chinchilla_get_theme_option( 'remove_margins' ) ) ) {
					if ( empty( $chinchilla_header_type ) ) {
						$chinchilla_header_type = chinchilla_get_theme_option( 'header_type' );
					}
					if ( 'custom' == $chinchilla_header_type && chinchilla_is_layouts_available() ) {
						$chinchilla_header_id = chinchilla_get_custom_header_id();
						if ( $chinchilla_header_id > 0 ) {
							$chinchilla_header_meta = chinchilla_get_custom_layout_meta( $chinchilla_header_id );
							if ( ! empty( $chinchilla_header_meta['margin'] ) ) {
								?> page_content_wrap_custom_header_margin<?php
							}
						}
					}
					$chinchilla_footer_type = chinchilla_get_theme_option( 'footer_type' );
					if ( 'custom' == $chinchilla_footer_type && chinchilla_is_layouts_available() ) {
						$chinchilla_footer_id = chinchilla_get_custom_footer_id();
						if ( $chinchilla_footer_id ) {
							$chinchilla_footer_meta = chinchilla_get_custom_layout_meta( $chinchilla_footer_id );
							if ( ! empty( $chinchilla_footer_meta['margin'] ) ) {
								?> page_content_wrap_custom_footer_margin<?php
							}
						}
					}
				}
				do_action( 'chinchilla_action_page_content_wrap_class', $chinchilla_prev_post_loading );
				?>"<?php
				if ( apply_filters( 'chinchilla_filter_is_prev_post_loading', $chinchilla_prev_post_loading ) ) {
					?> data-single-style="<?php echo esc_attr( chinchilla_get_theme_option( 'single_style' ) ); ?>"<?php
				}
				do_action( 'chinchilla_action_page_content_wrap_data', $chinchilla_prev_post_loading );
			?>>
				<?php
				do_action( 'chinchilla_action_page_content_wrap', $chinchilla_full_post_loading || $chinchilla_prev_post_loading );

				// Single posts banner
				if ( apply_filters( 'chinchilla_filter_single_post_header', chinchilla_is_singular( 'post' ) || chinchilla_is_singular( 'attachment' ) ) ) {
					if ( $chinchilla_prev_post_loading ) {
						if ( chinchilla_get_theme_option( 'posts_navigation_scroll_which_block' ) != 'article' ) {
							do_action( 'chinchilla_action_between_posts' );
						}
					}
					// Single post thumbnail and title
					$chinchilla_path = apply_filters( 'chinchilla_filter_get_template_part', 'templates/single-styles/' . chinchilla_get_theme_option( 'single_style' ) );
					if ( chinchilla_get_file_dir( $chinchilla_path . '.php' ) != '' ) {
						get_template_part( $chinchilla_path );
					}
				}

				// Widgets area above page
				$chinchilla_body_style   = chinchilla_get_theme_option( 'body_style' );
				$chinchilla_widgets_name = chinchilla_get_theme_option( 'widgets_above_page' );
				$chinchilla_show_widgets = ! chinchilla_is_off( $chinchilla_widgets_name ) && is_active_sidebar( $chinchilla_widgets_name );
				if ( $chinchilla_show_widgets ) {
					if ( 'fullscreen' != $chinchilla_body_style ) {
						?>
						<div class="content_wrap">
							<?php
					}
					chinchilla_create_widgets_area( 'widgets_above_page' );
					if ( 'fullscreen' != $chinchilla_body_style ) {
						?>
						</div>
						<?php
					}
				}

				// Content area
				do_action( 'chinchilla_action_before_content_wrap' );
				?>
				<div class="content_wrap<?php echo 'fullscreen' == $chinchilla_body_style ? '_fullscreen' : ''; ?>">

					<div class="content">
						<?php
						do_action( 'chinchilla_action_page_content_start' );

						// Skip link anchor to fast access to the content from keyboard
						?>
						<a id="content_skip_link_anchor" class="chinchilla_skip_link_anchor" href="#"></a>
						<?php
						// Single posts banner between prev/next posts
						if ( ( chinchilla_is_singular( 'post' ) || chinchilla_is_singular( 'attachment' ) )
							&& $chinchilla_prev_post_loading 
							&& chinchilla_get_theme_option( 'posts_navigation_scroll_which_block' ) == 'article'
						) {
							do_action( 'chinchilla_action_between_posts' );
						}

						// Widgets area above content
						chinchilla_create_widgets_area( 'widgets_above_content' );

						do_action( 'chinchilla_action_page_content_start_text' );

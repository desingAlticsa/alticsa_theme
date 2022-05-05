<?php
/**
 * The template to display Admin notices
 *
 * @package CHINCHILLA
 * @since CHINCHILLA 1.0.1
 */

$chinchilla_theme_slug = get_option( 'template' );
$chinchilla_theme_obj  = wp_get_theme( $chinchilla_theme_slug );
?>
<div class="chinchilla_admin_notice chinchilla_welcome_notice notice notice-info is-dismissible" data-notice="admin">
	<?php
	// Theme image
	$chinchilla_theme_img = chinchilla_get_file_url( 'screenshot.jpg' );
	if ( '' != $chinchilla_theme_img ) {
		?>
		<div class="chinchilla_notice_image"><img src="<?php echo esc_url( $chinchilla_theme_img ); ?>" alt="<?php esc_attr_e( 'Theme screenshot', 'chinchilla' ); ?>"></div>
		<?php
	}

	// Title
	?>
	<h3 class="chinchilla_notice_title">
		<?php
		echo esc_html(
			sprintf(
				// Translators: Add theme name and version to the 'Welcome' message
				__( 'Welcome to %1$s v.%2$s', 'chinchilla' ),
				$chinchilla_theme_obj->get( 'Name' ) . ( CHINCHILLA_THEME_FREE ? ' ' . __( 'Free', 'chinchilla' ) : '' ),
				$chinchilla_theme_obj->get( 'Version' )
			)
		);
		?>
	</h3>
	<?php

	// Description
	?>
	<div class="chinchilla_notice_text">
		<p class="chinchilla_notice_text_description">
			<?php
			echo str_replace( '. ', '.<br>', wp_kses_data( $chinchilla_theme_obj->description ) );
			?>
		</p>
		<p class="chinchilla_notice_text_info">
			<?php
			echo wp_kses_data( __( 'Attention! Plugin "ThemeREX Addons" is required! Please, install and activate it!', 'chinchilla' ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="chinchilla_notice_buttons">
		<?php
		// Link to the page 'About Theme'
		?>
		<a href="<?php echo esc_url( admin_url() . 'themes.php?page=chinchilla_about' ); ?>" class="button button-primary"><i class="dashicons dashicons-nametag"></i> 
			<?php
			echo esc_html__( 'Install plugin "ThemeREX Addons"', 'chinchilla' );
			?>
		</a>
	</div>
</div>

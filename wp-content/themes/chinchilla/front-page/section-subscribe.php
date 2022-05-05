<div class="front_page_section front_page_section_subscribe<?php
	$chinchilla_scheme = chinchilla_get_theme_option( 'front_page_subscribe_scheme' );
	if ( ! empty( $chinchilla_scheme ) && ! chinchilla_is_inherit( $chinchilla_scheme ) ) {
		echo ' scheme_' . esc_attr( $chinchilla_scheme );
	}
	echo ' front_page_section_paddings_' . esc_attr( chinchilla_get_theme_option( 'front_page_subscribe_paddings' ) );
	if ( chinchilla_get_theme_option( 'front_page_subscribe_stack' ) ) {
		echo ' sc_stack_section_on';
	}
?>"
		<?php
		$chinchilla_css      = '';
		$chinchilla_bg_image = chinchilla_get_theme_option( 'front_page_subscribe_bg_image' );
		if ( ! empty( $chinchilla_bg_image ) ) {
			$chinchilla_css .= 'background-image: url(' . esc_url( chinchilla_get_attachment_url( $chinchilla_bg_image ) ) . ');';
		}
		if ( ! empty( $chinchilla_css ) ) {
			echo ' style="' . esc_attr( $chinchilla_css ) . '"';
		}
		?>
>
<?php
	// Add anchor
	$chinchilla_anchor_icon = chinchilla_get_theme_option( 'front_page_subscribe_anchor_icon' );
	$chinchilla_anchor_text = chinchilla_get_theme_option( 'front_page_subscribe_anchor_text' );
if ( ( ! empty( $chinchilla_anchor_icon ) || ! empty( $chinchilla_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_subscribe"'
									. ( ! empty( $chinchilla_anchor_icon ) ? ' icon="' . esc_attr( $chinchilla_anchor_icon ) . '"' : '' )
									. ( ! empty( $chinchilla_anchor_text ) ? ' title="' . esc_attr( $chinchilla_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_subscribe_inner
	<?php
	if ( chinchilla_get_theme_option( 'front_page_subscribe_fullheight' ) ) {
		echo ' chinchilla-full-height sc_layouts_flex sc_layouts_columns_middle';
	}
	?>
			"
			<?php
			$chinchilla_css      = '';
			$chinchilla_bg_mask  = chinchilla_get_theme_option( 'front_page_subscribe_bg_mask' );
			$chinchilla_bg_color_type = chinchilla_get_theme_option( 'front_page_subscribe_bg_color_type' );
			if ( 'custom' == $chinchilla_bg_color_type ) {
				$chinchilla_bg_color = chinchilla_get_theme_option( 'front_page_subscribe_bg_color' );
			} elseif ( 'scheme_bg_color' == $chinchilla_bg_color_type ) {
				$chinchilla_bg_color = chinchilla_get_scheme_color( 'bg_color', $chinchilla_scheme );
			} else {
				$chinchilla_bg_color = '';
			}
			if ( ! empty( $chinchilla_bg_color ) && $chinchilla_bg_mask > 0 ) {
				$chinchilla_css .= 'background-color: ' . esc_attr(
					1 == $chinchilla_bg_mask ? $chinchilla_bg_color : chinchilla_hex2rgba( $chinchilla_bg_color, $chinchilla_bg_mask )
				) . ';';
			}
			if ( ! empty( $chinchilla_css ) ) {
				echo ' style="' . esc_attr( $chinchilla_css ) . '"';
			}
			?>
	>
		<div class="front_page_section_content_wrap front_page_section_subscribe_content_wrap content_wrap">
			<?php
			// Caption
			$chinchilla_caption = chinchilla_get_theme_option( 'front_page_subscribe_caption' );
			if ( ! empty( $chinchilla_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<h2 class="front_page_section_caption front_page_section_subscribe_caption front_page_block_<?php echo ! empty( $chinchilla_caption ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( $chinchilla_caption, 'chinchilla_kses_content' ); ?></h2>
				<?php
			}

			// Description (text)
			$chinchilla_description = chinchilla_get_theme_option( 'front_page_subscribe_description' );
			if ( ! empty( $chinchilla_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_description front_page_section_subscribe_description front_page_block_<?php echo ! empty( $chinchilla_description ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( wpautop( $chinchilla_description ), 'chinchilla_kses_content' ); ?></div>
				<?php
			}

			// Content
			$chinchilla_sc = chinchilla_get_theme_option( 'front_page_subscribe_shortcode' );
			if ( ! empty( $chinchilla_sc ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_output front_page_section_subscribe_output front_page_block_<?php echo ! empty( $chinchilla_sc ) ? 'filled' : 'empty'; ?>">
				<?php
					chinchilla_show_layout( do_shortcode( $chinchilla_sc ) );
				?>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>

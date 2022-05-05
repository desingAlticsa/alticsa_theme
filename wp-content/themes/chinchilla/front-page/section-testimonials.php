<div class="front_page_section front_page_section_testimonials<?php
	$chinchilla_scheme = chinchilla_get_theme_option( 'front_page_testimonials_scheme' );
	if ( ! empty( $chinchilla_scheme ) && ! chinchilla_is_inherit( $chinchilla_scheme ) ) {
		echo ' scheme_' . esc_attr( $chinchilla_scheme );
	}
	echo ' front_page_section_paddings_' . esc_attr( chinchilla_get_theme_option( 'front_page_testimonials_paddings' ) );
	if ( chinchilla_get_theme_option( 'front_page_testimonials_stack' ) ) {
		echo ' sc_stack_section_on';
	}
?>"
		<?php
		$chinchilla_css      = '';
		$chinchilla_bg_image = chinchilla_get_theme_option( 'front_page_testimonials_bg_image' );
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
	$chinchilla_anchor_icon = chinchilla_get_theme_option( 'front_page_testimonials_anchor_icon' );
	$chinchilla_anchor_text = chinchilla_get_theme_option( 'front_page_testimonials_anchor_text' );
if ( ( ! empty( $chinchilla_anchor_icon ) || ! empty( $chinchilla_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_testimonials"'
									. ( ! empty( $chinchilla_anchor_icon ) ? ' icon="' . esc_attr( $chinchilla_anchor_icon ) . '"' : '' )
									. ( ! empty( $chinchilla_anchor_text ) ? ' title="' . esc_attr( $chinchilla_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_testimonials_inner
	<?php
	if ( chinchilla_get_theme_option( 'front_page_testimonials_fullheight' ) ) {
		echo ' chinchilla-full-height sc_layouts_flex sc_layouts_columns_middle';
	}
	?>
			"
			<?php
			$chinchilla_css      = '';
			$chinchilla_bg_mask  = chinchilla_get_theme_option( 'front_page_testimonials_bg_mask' );
			$chinchilla_bg_color_type = chinchilla_get_theme_option( 'front_page_testimonials_bg_color_type' );
			if ( 'custom' == $chinchilla_bg_color_type ) {
				$chinchilla_bg_color = chinchilla_get_theme_option( 'front_page_testimonials_bg_color' );
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
		<div class="front_page_section_content_wrap front_page_section_testimonials_content_wrap content_wrap">
			<?php
			// Caption
			$chinchilla_caption = chinchilla_get_theme_option( 'front_page_testimonials_caption' );
			if ( ! empty( $chinchilla_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<h2 class="front_page_section_caption front_page_section_testimonials_caption front_page_block_<?php echo ! empty( $chinchilla_caption ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( $chinchilla_caption, 'chinchilla_kses_content' ); ?></h2>
				<?php
			}

			// Description (text)
			$chinchilla_description = chinchilla_get_theme_option( 'front_page_testimonials_description' );
			if ( ! empty( $chinchilla_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_description front_page_section_testimonials_description front_page_block_<?php echo ! empty( $chinchilla_description ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( wpautop( $chinchilla_description ), 'chinchilla_kses_content' ); ?></div>
				<?php
			}

			// Content (widgets)
			?>
			<div class="front_page_section_output front_page_section_testimonials_output">
				<?php
				if ( is_active_sidebar( 'front_page_testimonials_widgets' ) ) {
					dynamic_sidebar( 'front_page_testimonials_widgets' );
				} elseif ( current_user_can( 'edit_theme_options' ) ) {
					if ( ! chinchilla_exists_trx_addons() ) {
						chinchilla_customizer_need_trx_addons_message();
					} else {
						chinchilla_customizer_need_widgets_message( 'front_page_testimonials_caption', 'ThemeREX Addons - Testimonials' );
					}
				}
				?>
			</div>
		</div>
	</div>
</div>

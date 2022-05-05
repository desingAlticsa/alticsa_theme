<div class="front_page_section front_page_section_woocommerce<?php
	$chinchilla_scheme = chinchilla_get_theme_option( 'front_page_woocommerce_scheme' );
	if ( ! empty( $chinchilla_scheme ) && ! chinchilla_is_inherit( $chinchilla_scheme ) ) {
		echo ' scheme_' . esc_attr( $chinchilla_scheme );
	}
	echo ' front_page_section_paddings_' . esc_attr( chinchilla_get_theme_option( 'front_page_woocommerce_paddings' ) );
	if ( chinchilla_get_theme_option( 'front_page_woocommerce_stack' ) ) {
		echo ' sc_stack_section_on';
	}
?>"
		<?php
		$chinchilla_css      = '';
		$chinchilla_bg_image = chinchilla_get_theme_option( 'front_page_woocommerce_bg_image' );
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
	$chinchilla_anchor_icon = chinchilla_get_theme_option( 'front_page_woocommerce_anchor_icon' );
	$chinchilla_anchor_text = chinchilla_get_theme_option( 'front_page_woocommerce_anchor_text' );
if ( ( ! empty( $chinchilla_anchor_icon ) || ! empty( $chinchilla_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_woocommerce"'
									. ( ! empty( $chinchilla_anchor_icon ) ? ' icon="' . esc_attr( $chinchilla_anchor_icon ) . '"' : '' )
									. ( ! empty( $chinchilla_anchor_text ) ? ' title="' . esc_attr( $chinchilla_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_woocommerce_inner
	<?php
	if ( chinchilla_get_theme_option( 'front_page_woocommerce_fullheight' ) ) {
		echo ' chinchilla-full-height sc_layouts_flex sc_layouts_columns_middle';
	}
	?>
			"
			<?php
			$chinchilla_css      = '';
			$chinchilla_bg_mask  = chinchilla_get_theme_option( 'front_page_woocommerce_bg_mask' );
			$chinchilla_bg_color_type = chinchilla_get_theme_option( 'front_page_woocommerce_bg_color_type' );
			if ( 'custom' == $chinchilla_bg_color_type ) {
				$chinchilla_bg_color = chinchilla_get_theme_option( 'front_page_woocommerce_bg_color' );
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
		<div class="front_page_section_content_wrap front_page_section_woocommerce_content_wrap content_wrap woocommerce">
			<?php
			// Content wrap with title and description
			$chinchilla_caption     = chinchilla_get_theme_option( 'front_page_woocommerce_caption' );
			$chinchilla_description = chinchilla_get_theme_option( 'front_page_woocommerce_description' );
			if ( ! empty( $chinchilla_caption ) || ! empty( $chinchilla_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				// Caption
				if ( ! empty( $chinchilla_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					?>
					<h2 class="front_page_section_caption front_page_section_woocommerce_caption front_page_block_<?php echo ! empty( $chinchilla_caption ) ? 'filled' : 'empty'; ?>">
					<?php
						echo wp_kses( $chinchilla_caption, 'chinchilla_kses_content' );
					?>
					</h2>
					<?php
				}

				// Description (text)
				if ( ! empty( $chinchilla_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					?>
					<div class="front_page_section_description front_page_section_woocommerce_description front_page_block_<?php echo ! empty( $chinchilla_description ) ? 'filled' : 'empty'; ?>">
					<?php
						echo wp_kses( wpautop( $chinchilla_description ), 'chinchilla_kses_content' );
					?>
					</div>
					<?php
				}
			}

			// Content (widgets)
			?>
			<div class="front_page_section_output front_page_section_woocommerce_output list_products shop_mode_thumbs">
				<?php
				$chinchilla_woocommerce_sc = chinchilla_get_theme_option( 'front_page_woocommerce_products' );
				if ( 'products' == $chinchilla_woocommerce_sc ) {
					$chinchilla_woocommerce_sc_ids      = chinchilla_get_theme_option( 'front_page_woocommerce_products_per_page' );
					$chinchilla_woocommerce_sc_per_page = count( explode( ',', $chinchilla_woocommerce_sc_ids ) );
				} else {
					$chinchilla_woocommerce_sc_per_page = max( 1, (int) chinchilla_get_theme_option( 'front_page_woocommerce_products_per_page' ) );
				}
				$chinchilla_woocommerce_sc_columns = max( 1, min( $chinchilla_woocommerce_sc_per_page, (int) chinchilla_get_theme_option( 'front_page_woocommerce_products_columns' ) ) );
				echo do_shortcode(
					"[{$chinchilla_woocommerce_sc}"
									. ( 'products' == $chinchilla_woocommerce_sc
											? ' ids="' . esc_attr( $chinchilla_woocommerce_sc_ids ) . '"'
											: '' )
									. ( 'product_category' == $chinchilla_woocommerce_sc
											? ' category="' . esc_attr( chinchilla_get_theme_option( 'front_page_woocommerce_products_categories' ) ) . '"'
											: '' )
									. ( 'best_selling_products' != $chinchilla_woocommerce_sc
											? ' orderby="' . esc_attr( chinchilla_get_theme_option( 'front_page_woocommerce_products_orderby' ) ) . '"'
												. ' order="' . esc_attr( chinchilla_get_theme_option( 'front_page_woocommerce_products_order' ) ) . '"'
											: '' )
									. ' per_page="' . esc_attr( $chinchilla_woocommerce_sc_per_page ) . '"'
									. ' columns="' . esc_attr( $chinchilla_woocommerce_sc_columns ) . '"'
					. ']'
				);
				?>
			</div>
		</div>
	</div>
</div>

<?php
/**
 * The Footer: widgets area, logo, footer menu and socials
 *
 * @package CHINCHILLA
 * @since CHINCHILLA 1.0
 */

							do_action( 'chinchilla_action_page_content_end_text' );
							
							// Widgets area below the content
							chinchilla_create_widgets_area( 'widgets_below_content' );
						
							do_action( 'chinchilla_action_page_content_end' );
							?>
						</div>
						<?php

						// Show main sidebar
						get_sidebar();
						?>
					</div>
					<?php

					do_action( 'chinchilla_action_after_content_wrap' );

					// Widgets area below the page and related posts below the page
					$chinchilla_body_style = chinchilla_get_theme_option( 'body_style' );
					$chinchilla_widgets_name = chinchilla_get_theme_option( 'widgets_below_page' );
					$chinchilla_show_widgets = ! chinchilla_is_off( $chinchilla_widgets_name ) && is_active_sidebar( $chinchilla_widgets_name );
					$chinchilla_show_related = chinchilla_is_single() && chinchilla_get_theme_option( 'related_position' ) == 'below_page';
					if ( $chinchilla_show_widgets || $chinchilla_show_related ) {
						if ( 'fullscreen' != $chinchilla_body_style ) {
							?>
							<div class="content_wrap">
							<?php
						}
						// Show related posts before footer
						if ( $chinchilla_show_related ) {
							do_action( 'chinchilla_action_related_posts' );
						}

						// Widgets area below page content
						if ( $chinchilla_show_widgets ) {
							chinchilla_create_widgets_area( 'widgets_below_page' );
						}
						if ( 'fullscreen' != $chinchilla_body_style ) {
							?>
							</div>
							<?php
						}
					}
					do_action( 'chinchilla_action_page_content_wrap_end' );
					?>
			</div>
			<?php
			do_action( 'chinchilla_action_after_page_content_wrap' );

			// Don't display the footer elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ( ! chinchilla_is_singular( 'post' ) && ! chinchilla_is_singular( 'attachment' ) ) || ! in_array ( chinchilla_get_value_gp( 'action' ), array( 'full_post_loading', 'prev_post_loading' ) ) ) {
				
				// Skip link anchor to fast access to the footer from keyboard
				?>
				<a id="footer_skip_link_anchor" class="chinchilla_skip_link_anchor" href="#"></a>
				<?php

				do_action( 'chinchilla_action_before_footer' );

				// Footer
				$chinchilla_footer_type = chinchilla_get_theme_option( 'footer_type' );
				if ( 'custom' == $chinchilla_footer_type && ! chinchilla_is_layouts_available() ) {
					$chinchilla_footer_type = 'default';
				}
				get_template_part( apply_filters( 'chinchilla_filter_get_template_part', "templates/footer-" . sanitize_file_name( $chinchilla_footer_type ) ) );

				do_action( 'chinchilla_action_after_footer' );

			}
			?>

			<?php do_action( 'chinchilla_action_page_wrap_end' ); ?>

		</div>

		<?php do_action( 'chinchilla_action_after_page_wrap' ); ?>

	</div>

	<?php do_action( 'chinchilla_action_after_body' ); ?>

	<?php wp_footer(); ?>

</body>
</html>
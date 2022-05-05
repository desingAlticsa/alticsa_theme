<?php
/**
 * The template to display single post
 *
 * @package CHINCHILLA
 * @since CHINCHILLA 1.0
 */

// Full post loading
$full_post_loading          = chinchilla_get_value_gp( 'action' ) == 'full_post_loading';

// Prev post loading
$prev_post_loading          = chinchilla_get_value_gp( 'action' ) == 'prev_post_loading';
$prev_post_loading_type     = chinchilla_get_theme_option( 'posts_navigation_scroll_which_block' );

// Position of the related posts
$chinchilla_related_position   = chinchilla_get_theme_option( 'related_position' );

// Type of the prev/next post navigation
$chinchilla_posts_navigation   = chinchilla_get_theme_option( 'posts_navigation' );
$chinchilla_prev_post          = false;
$chinchilla_prev_post_same_cat = chinchilla_get_theme_option( 'posts_navigation_scroll_same_cat' );

// Rewrite style of the single post if current post loading via AJAX and featured image and title is not in the content
if ( ( $full_post_loading 
		|| 
		( $prev_post_loading && 'article' == $prev_post_loading_type )
	) 
	&& 
	! in_array( chinchilla_get_theme_option( 'single_style' ), array( 'style-6' ) )
) {
	chinchilla_storage_set_array( 'options_meta', 'single_style', 'style-6' );
}

do_action( 'chinchilla_action_prev_post_loading', $prev_post_loading, $prev_post_loading_type );

get_header();

while ( have_posts() ) {

	the_post();

	// Type of the prev/next post navigation
	if ( 'scroll' == $chinchilla_posts_navigation ) {
		$chinchilla_prev_post = get_previous_post( $chinchilla_prev_post_same_cat );  // Get post from same category
		if ( ! $chinchilla_prev_post && $chinchilla_prev_post_same_cat ) {
			$chinchilla_prev_post = get_previous_post( false );                    // Get post from any category
		}
		if ( ! $chinchilla_prev_post ) {
			$chinchilla_posts_navigation = 'links';
		}
	}

	// Override some theme options to display featured image, title and post meta in the dynamic loaded posts
	if ( $full_post_loading || ( $prev_post_loading && $chinchilla_prev_post ) ) {
		chinchilla_sc_layouts_showed( 'featured', false );
		chinchilla_sc_layouts_showed( 'title', false );
		chinchilla_sc_layouts_showed( 'postmeta', false );
	}

	// If related posts should be inside the content
	if ( strpos( $chinchilla_related_position, 'inside' ) === 0 ) {
		ob_start();
	}

	// Display post's content
	get_template_part( apply_filters( 'chinchilla_filter_get_template_part', 'templates/content', 'single-' . chinchilla_get_theme_option( 'single_style' ) ), 'single-' . chinchilla_get_theme_option( 'single_style' ) );

	// If related posts should be inside the content
	if ( strpos( $chinchilla_related_position, 'inside' ) === 0 ) {
		$chinchilla_content = ob_get_contents();
		ob_end_clean();

		ob_start();
		do_action( 'chinchilla_action_related_posts' );
		$chinchilla_related_content = ob_get_contents();
		ob_end_clean();

		if ( ! empty( $chinchilla_related_content ) ) {
			$chinchilla_related_position_inside = max( 0, min( 9, chinchilla_get_theme_option( 'related_position_inside' ) ) );
			if ( 0 == $chinchilla_related_position_inside ) {
				$chinchilla_related_position_inside = mt_rand( 1, 9 );
			}

			$chinchilla_p_number         = 0;
			$chinchilla_related_inserted = false;
			$chinchilla_in_block         = false;
			$chinchilla_content_start    = strpos( $chinchilla_content, '<div class="post_content' );
			$chinchilla_content_end      = strrpos( $chinchilla_content, '</div>' );

			for ( $i = max( 0, $chinchilla_content_start ); $i < min( strlen( $chinchilla_content ) - 3, $chinchilla_content_end ); $i++ ) {
				if ( $chinchilla_content[ $i ] != '<' ) {
					continue;
				}
				if ( $chinchilla_in_block ) {
					if ( strtolower( substr( $chinchilla_content, $i + 1, 12 ) ) == '/blockquote>' ) {
						$chinchilla_in_block = false;
						$i += 12;
					}
					continue;
				} else if ( strtolower( substr( $chinchilla_content, $i + 1, 10 ) ) == 'blockquote' && in_array( $chinchilla_content[ $i + 11 ], array( '>', ' ' ) ) ) {
					$chinchilla_in_block = true;
					$i += 11;
					continue;
				} else if ( 'p' == $chinchilla_content[ $i + 1 ] && in_array( $chinchilla_content[ $i + 2 ], array( '>', ' ' ) ) ) {
					$chinchilla_p_number++;
					if ( $chinchilla_related_position_inside == $chinchilla_p_number ) {
						$chinchilla_related_inserted = true;
						$chinchilla_content = ( $i > 0 ? substr( $chinchilla_content, 0, $i ) : '' )
											. $chinchilla_related_content
											. substr( $chinchilla_content, $i );
					}
				}
			}
			if ( ! $chinchilla_related_inserted ) {
				if ( $chinchilla_content_end > 0 ) {
					$chinchilla_content = substr( $chinchilla_content, 0, $chinchilla_content_end ) . $chinchilla_related_content . substr( $chinchilla_content, $chinchilla_content_end );
				} else {
					$chinchilla_content .= $chinchilla_related_content;
				}
			}
		}

		chinchilla_show_layout( $chinchilla_content );
	}

	// Comments
	do_action( 'chinchilla_action_before_comments' );
	comments_template();
	do_action( 'chinchilla_action_after_comments' );

	// Related posts
	if ( 'below_content' == $chinchilla_related_position
		&& ( 'scroll' != $chinchilla_posts_navigation || chinchilla_get_theme_option( 'posts_navigation_scroll_hide_related' ) == 0 )
		&& ( ! $full_post_loading || chinchilla_get_theme_option( 'open_full_post_hide_related' ) == 0 )
	) {
		do_action( 'chinchilla_action_related_posts' );
	}

	// Post navigation: type 'scroll'
	if ( 'scroll' == $chinchilla_posts_navigation && ! $full_post_loading ) {
		?>
		<div class="nav-links-single-scroll"
			data-post-id="<?php echo esc_attr( get_the_ID( $chinchilla_prev_post ) ); ?>"
			data-post-link="<?php echo esc_attr( get_permalink( $chinchilla_prev_post ) ); ?>"
			data-post-title="<?php the_title_attribute( array( 'post' => $chinchilla_prev_post ) ); ?>"
			<?php do_action( 'chinchilla_action_nav_links_single_scroll_data', $chinchilla_prev_post ); ?>
		></div>
		<?php
	}
}

get_footer();

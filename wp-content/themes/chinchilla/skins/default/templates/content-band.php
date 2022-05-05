<?php
/**
 * 'Band' template to display the content
 *
 * Used for index/archive/search.
 *
 * @package CHINCHILLA
 * @since CHINCHILLA 1.71.0
 */

$chinchilla_template_args = get_query_var( 'chinchilla_template_args' );

$chinchilla_columns       = 1;

$chinchilla_expanded      = ! chinchilla_sidebar_present() && chinchilla_get_theme_option( 'expand_content' ) == 'expand';

$chinchilla_post_format   = get_post_format();
$chinchilla_post_format   = empty( $chinchilla_post_format ) ? 'standard' : str_replace( 'post-format-', '', $chinchilla_post_format );

if ( is_array( $chinchilla_template_args ) ) {
	$chinchilla_columns    = empty( $chinchilla_template_args['columns'] ) ? 1 : max( 1, $chinchilla_template_args['columns'] );
	$chinchilla_blog_style = array( $chinchilla_template_args['type'], $chinchilla_columns );
	if ( ! empty( $chinchilla_template_args['slider'] ) ) {
		?><div class="slider-slide swiper-slide">
		<?php
	} elseif ( $chinchilla_columns > 1 ) {
	    $chinchilla_columns_class = chinchilla_get_column_class( 1, $chinchilla_columns, ! empty( $chinchilla_template_args['columns_tablet']) ? $chinchilla_template_args['columns_tablet'] : '', ! empty($chinchilla_template_args['columns_mobile']) ? $chinchilla_template_args['columns_mobile'] : '' );
				?><div class="<?php echo esc_attr( $chinchilla_columns_class ); ?>"><?php
	}
}
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class( 'post_item post_item_container post_layout_band post_format_' . esc_attr( $chinchilla_post_format ) );
	chinchilla_add_blog_animation( $chinchilla_template_args );
	?>
>
	<?php

	// Sticky label
	if ( is_sticky() && ! is_paged() ) {
		?>
		<span class="post_label label_sticky"></span>
		<?php
	}

	// Featured image
	$chinchilla_hover      = ! empty( $chinchilla_template_args['hover'] ) && ! chinchilla_is_inherit( $chinchilla_template_args['hover'] )
							? $chinchilla_template_args['hover']
							: chinchilla_get_theme_option( 'image_hover' );
	$chinchilla_components = ! empty( $chinchilla_template_args['meta_parts'] )
							? ( is_array( $chinchilla_template_args['meta_parts'] )
								? $chinchilla_template_args['meta_parts']
								: array_map( 'trim', explode( ',', $chinchilla_template_args['meta_parts'] ) )
								)
							: chinchilla_array_get_keys_by_value( chinchilla_get_theme_option( 'meta_parts' ) );
	chinchilla_show_post_featured( apply_filters( 'chinchilla_filter_args_featured',
		array(
			'no_links'   => ! empty( $chinchilla_template_args['no_links'] ),
			'hover'      => $chinchilla_hover,
			'meta_parts' => $chinchilla_components,
			'thumb_bg'   => true,
			'thumb_ratio'   => '1:1',
			'thumb_size' => ! empty( $chinchilla_template_args['thumb_size'] )
								? $chinchilla_template_args['thumb_size']
								: chinchilla_get_thumb_size( 
								in_array( $chinchilla_post_format, array( 'gallery', 'audio', 'video' ) )
									? ( strpos( chinchilla_get_theme_option( 'body_style' ), 'full' ) !== false
										? 'full'
										: ( $chinchilla_expanded 
											? 'big' 
											: 'medium-square'
											)
										)
									: 'masonry-big'
								)
		),
		'content-band',
		$chinchilla_template_args
	) );

	?><div class="post_content_wrap"><?php

		// Title and post meta
		$chinchilla_show_title = get_the_title() != '';
		$chinchilla_show_meta  = count( $chinchilla_components ) > 0 && ! in_array( $chinchilla_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );
		if ( $chinchilla_show_title ) {
			?>
			<div class="post_header entry-header">
				<?php
				// Categories
				if ( apply_filters( 'chinchilla_filter_show_blog_categories', $chinchilla_show_meta && in_array( 'categories', $chinchilla_components ), array( 'categories' ), 'band' ) ) {
					do_action( 'chinchilla_action_before_post_category' );
					?>
					<div class="post_category">
						<?php
						chinchilla_show_post_meta( apply_filters(
															'chinchilla_filter_post_meta_args',
															array(
																'components' => 'categories',
																'seo'        => false,
																'echo'       => true,
																'cat_sep'    => false,
																),
															'hover_' . $chinchilla_hover, 1
															)
											);
						?>
					</div>
					<?php
					$chinchilla_components = chinchilla_array_delete_by_value( $chinchilla_components, 'categories' );
					do_action( 'chinchilla_action_after_post_category' );
				}
				// Post title
				if ( apply_filters( 'chinchilla_filter_show_blog_title', true, 'band' ) ) {
					do_action( 'chinchilla_action_before_post_title' );
					if ( empty( $chinchilla_template_args['no_links'] ) ) {
						the_title( sprintf( '<h4 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' );
					} else {
						the_title( '<h4 class="post_title entry-title">', '</h4>' );
					}
					do_action( 'chinchilla_action_after_post_title' );
				}
				?>
			</div><!-- .post_header -->
			<?php
		}

		// Post content
		if ( ! isset( $chinchilla_template_args['excerpt_length'] ) && ! in_array( $chinchilla_post_format, array( 'gallery', 'audio', 'video' ) ) ) {
			$chinchilla_template_args['excerpt_length'] = 13;
		}
		if ( apply_filters( 'chinchilla_filter_show_blog_excerpt', empty( $chinchilla_template_args['hide_excerpt'] ) && chinchilla_get_theme_option( 'excerpt_length' ) > 0, 'band' ) ) {
			?>
			<div class="post_content entry-content">
				<?php
				// Post content area
				chinchilla_show_post_content( $chinchilla_template_args, '<div class="post_content_inner">', '</div>' );
				?>
			</div><!-- .entry-content -->
			<?php
		}
		// Post meta
		if ( apply_filters( 'chinchilla_filter_show_blog_meta', $chinchilla_show_meta, $chinchilla_components, 'band' ) ) {
			if ( count( $chinchilla_components ) > 0 ) {
				do_action( 'chinchilla_action_before_post_meta' );
				chinchilla_show_post_meta(
					apply_filters(
						'chinchilla_filter_post_meta_args', array(
							'components' => join( ',', $chinchilla_components ),
							'seo'        => false,
							'echo'       => true,
						), 'band', 1
					)
				);
				do_action( 'chinchilla_action_after_post_meta' );
			}
		}
		// More button
		if ( apply_filters( 'chinchilla_filter_show_blog_readmore', ! $chinchilla_show_title || ! empty( $chinchilla_template_args['more_button'] ), 'band' ) ) {
			if ( empty( $chinchilla_template_args['no_links'] ) ) {
				do_action( 'chinchilla_action_before_post_readmore' );
				chinchilla_show_post_more_link( $chinchilla_template_args, '<p>', '</p>' );
				do_action( 'chinchilla_action_after_post_readmore' );
			}
		}
		?>
	</div>
</article>
<?php

if ( is_array( $chinchilla_template_args ) ) {
	if ( ! empty( $chinchilla_template_args['slider'] ) || $chinchilla_columns > 1 ) {
		?>
		</div>
		<?php
	}
}

<?php
/**
 * The default template to display the content
 *
 * Used for index/archive/search.
 *
 * @package CHINCHILLA
 * @since CHINCHILLA 1.0
 */

$chinchilla_template_args = get_query_var( 'chinchilla_template_args' );
$chinchilla_columns = 1;
if ( is_array( $chinchilla_template_args ) ) {
	$chinchilla_columns    = empty( $chinchilla_template_args['columns'] ) ? 1 : max( 1, $chinchilla_template_args['columns'] );
	$chinchilla_blog_style = array( $chinchilla_template_args['type'], $chinchilla_columns );
	if ( ! empty( $chinchilla_template_args['slider'] ) ) {
		?><div class="slider-slide swiper-slide">
		<?php
	} elseif ( $chinchilla_columns > 1 ) {
	    $chinchilla_columns_class = chinchilla_get_column_class( 1, $chinchilla_columns, ! empty( $chinchilla_template_args['columns_tablet']) ? $chinchilla_template_args['columns_tablet'] : '', ! empty($chinchilla_template_args['columns_mobile']) ? $chinchilla_template_args['columns_mobile'] : '' );
		?>
		<div class="<?php echo esc_attr( $chinchilla_columns_class ); ?>">
		<?php
	}
}
$chinchilla_expanded    = ! chinchilla_sidebar_present() && chinchilla_get_theme_option( 'expand_content' ) == 'expand';
$chinchilla_post_format = get_post_format();
$chinchilla_post_format = empty( $chinchilla_post_format ) ? 'standard' : str_replace( 'post-format-', '', $chinchilla_post_format );
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class( 'post_item post_item_container post_layout_excerpt post_format_' . esc_attr( $chinchilla_post_format ) );
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
			'thumb_size' => ! empty( $chinchilla_template_args['thumb_size'] )
							? $chinchilla_template_args['thumb_size']
							: chinchilla_get_thumb_size( strpos( chinchilla_get_theme_option( 'body_style' ), 'full' ) !== false
								? 'full'
								: ( $chinchilla_expanded 
									? 'huge' 
									: 'big' 
									)
								),
		),
		'content-excerpt',
		$chinchilla_template_args
	) );

	// Title and post meta
	$chinchilla_show_title = get_the_title() != '';
	$chinchilla_show_meta  = count( $chinchilla_components ) > 0 && ! in_array( $chinchilla_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );

	if ( $chinchilla_show_title ) {
		?>
		<div class="post_header entry-header">
			<?php
			// Post title
			if ( apply_filters( 'chinchilla_filter_show_blog_title', true, 'excerpt' ) ) {
				do_action( 'chinchilla_action_before_post_title' );
				if ( empty( $chinchilla_template_args['no_links'] ) ) {
					the_title( sprintf( '<h3 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' );
				} else {
					the_title( '<h3 class="post_title entry-title">', '</h3>' );
				}
				do_action( 'chinchilla_action_after_post_title' );
			}
			?>
		</div><!-- .post_header -->
		<?php
	}

	// Post content
	if ( apply_filters( 'chinchilla_filter_show_blog_excerpt', empty( $chinchilla_template_args['hide_excerpt'] ) && chinchilla_get_theme_option( 'excerpt_length' ) > 0, 'excerpt' ) ) {
		?>
		<div class="post_content entry-content">
			<?php

			// Post meta
			if ( apply_filters( 'chinchilla_filter_show_blog_meta', $chinchilla_show_meta, $chinchilla_components, 'excerpt' ) ) {
				if ( count( $chinchilla_components ) > 0 ) {
					do_action( 'chinchilla_action_before_post_meta' );
					chinchilla_show_post_meta(
						apply_filters(
							'chinchilla_filter_post_meta_args', array(
								'components' => join( ',', $chinchilla_components ),
								'seo'        => false,
								'echo'       => true,
							), 'excerpt', 1
						)
					);
					do_action( 'chinchilla_action_after_post_meta' );
				}
			}

			if ( chinchilla_get_theme_option( 'blog_content' ) == 'fullpost' ) {
				// Post content area
				?>
				<div class="post_content_inner">
					<?php
					do_action( 'chinchilla_action_before_full_post_content' );
					the_content( '' );
					do_action( 'chinchilla_action_after_full_post_content' );
					?>
				</div>
				<?php
				// Inner pages
				wp_link_pages(
					array(
						'before'      => '<div class="page_links"><span class="page_links_title">' . esc_html__( 'Pages:', 'chinchilla' ) . '</span>',
						'after'       => '</div>',
						'link_before' => '<span>',
						'link_after'  => '</span>',
						'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'chinchilla' ) . ' </span>%',
						'separator'   => '<span class="screen-reader-text">, </span>',
					)
				);
			} else {
				// Post content area
				chinchilla_show_post_content( $chinchilla_template_args, '<div class="post_content_inner">', '</div>' );
			}

			// More button
			if ( apply_filters( 'chinchilla_filter_show_blog_readmore',  ! isset( $chinchilla_template_args['more_button'] ) || ! empty( $chinchilla_template_args['more_button'] ), 'excerpt' ) ) {
				if ( empty( $chinchilla_template_args['no_links'] ) ) {
					do_action( 'chinchilla_action_before_post_readmore' );
					if ( chinchilla_get_theme_option( 'blog_content' ) != 'fullpost' ) {
						chinchilla_show_post_more_link( $chinchilla_template_args, '<p>', '</p>' );
					} else {
						chinchilla_show_post_comments_link( $chinchilla_template_args, '<p>', '</p>' );
					}
					do_action( 'chinchilla_action_after_post_readmore' );
				}
			}

			?>
		</div><!-- .entry-content -->
		<?php
	}
	?>
</article>
<?php

if ( is_array( $chinchilla_template_args ) ) {
	if ( ! empty( $chinchilla_template_args['slider'] ) || $chinchilla_columns > 1 ) {
		?>
		</div>
		<?php
	}
}

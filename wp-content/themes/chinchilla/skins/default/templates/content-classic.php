<?php
/**
 * The Classic template to display the content
 *
 * Used for index/archive/search.
 *
 * @package CHINCHILLA
 * @since CHINCHILLA 1.0
 */

$chinchilla_template_args = get_query_var( 'chinchilla_template_args' );

if ( is_array( $chinchilla_template_args ) ) {
	$chinchilla_columns    = empty( $chinchilla_template_args['columns'] ) ? 2 : max( 1, $chinchilla_template_args['columns'] );
	$chinchilla_blog_style = array( $chinchilla_template_args['type'], $chinchilla_columns );
    $chinchilla_columns_class = chinchilla_get_column_class( 1, $chinchilla_columns, ! empty( $chinchilla_template_args['columns_tablet']) ? $chinchilla_template_args['columns_tablet'] : '', ! empty($chinchilla_template_args['columns_mobile']) ? $chinchilla_template_args['columns_mobile'] : '' );
} else {
	$chinchilla_blog_style = explode( '_', chinchilla_get_theme_option( 'blog_style' ) );
	$chinchilla_columns    = empty( $chinchilla_blog_style[1] ) ? 2 : max( 1, $chinchilla_blog_style[1] );
    $chinchilla_columns_class = chinchilla_get_column_class( 1, $chinchilla_columns );
}
$chinchilla_expanded   = ! chinchilla_sidebar_present() && chinchilla_get_theme_option( 'expand_content' ) == 'expand';

$chinchilla_post_format = get_post_format();
$chinchilla_post_format = empty( $chinchilla_post_format ) ? 'standard' : str_replace( 'post-format-', '', $chinchilla_post_format );

?><div class="<?php
	if ( ! empty( $chinchilla_template_args['slider'] ) ) {
		echo ' slider-slide swiper-slide';
	} else {
		echo ( chinchilla_is_blog_style_use_masonry( $chinchilla_blog_style[0] ) ? 'masonry_item masonry_item-1_' . esc_attr( $chinchilla_columns ) : esc_attr( $chinchilla_columns_class ) );
	}
?>"><article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
		'post_item post_item_container post_format_' . esc_attr( $chinchilla_post_format )
				. ' post_layout_classic post_layout_classic_' . esc_attr( $chinchilla_columns )
				. ' post_layout_' . esc_attr( $chinchilla_blog_style[0] )
				. ' post_layout_' . esc_attr( $chinchilla_blog_style[0] ) . '_' . esc_attr( $chinchilla_columns )
	);
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
								: explode( ',', $chinchilla_template_args['meta_parts'] )
								)
							: chinchilla_array_get_keys_by_value( chinchilla_get_theme_option( 'meta_parts' ) );

	chinchilla_show_post_featured( apply_filters( 'chinchilla_filter_args_featured',
		array(
			'thumb_size' => ! empty( $chinchilla_template_args['thumb_size'] )
				? $chinchilla_template_args['thumb_size']
				: chinchilla_get_thumb_size(
				'classic' == $chinchilla_blog_style[0]
						? ( strpos( chinchilla_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $chinchilla_columns > 2 ? 'big' : 'huge' )
								: ( $chinchilla_columns > 2
									? ( $chinchilla_expanded ? 'square' : 'square' )
									: ($chinchilla_columns > 1 ? 'square' : ( $chinchilla_expanded ? 'huge' : 'big' ))
									)
							)
						: ( strpos( chinchilla_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $chinchilla_columns > 2 ? 'masonry-big' : 'full' )
								: ($chinchilla_columns === 1 ? ( $chinchilla_expanded ? 'huge' : 'big' ) : ( $chinchilla_columns <= 2 && $chinchilla_expanded ? 'masonry-big' : 'masonry' ))
							)
			),
			'hover'      => $chinchilla_hover,
			'meta_parts' => $chinchilla_components,
			'no_links'   => ! empty( $chinchilla_template_args['no_links'] ),
        ),
        'content-classic',
        $chinchilla_template_args
    ) );

	// Title and post meta
	$chinchilla_show_title = get_the_title() != '';
	$chinchilla_show_meta  = count( $chinchilla_components ) > 0 && ! in_array( $chinchilla_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );

	if ( $chinchilla_show_title ) {
		?>
		<div class="post_header entry-header">
			<?php

			// Post meta
			if ( apply_filters( 'chinchilla_filter_show_blog_meta', $chinchilla_show_meta, $chinchilla_components, 'classic' ) ) {
				if ( count( $chinchilla_components ) > 0 ) {
					do_action( 'chinchilla_action_before_post_meta' );
					chinchilla_show_post_meta(
						apply_filters(
							'chinchilla_filter_post_meta_args', array(
							'components' => join( ',', $chinchilla_components ),
							'seo'        => false,
							'echo'       => true,
						), $chinchilla_blog_style[0], $chinchilla_columns
						)
					);
					do_action( 'chinchilla_action_after_post_meta' );
				}
			}

			// Post title
			if ( apply_filters( 'chinchilla_filter_show_blog_title', true, 'classic' ) ) {
				do_action( 'chinchilla_action_before_post_title' );
				if ( empty( $chinchilla_template_args['no_links'] ) ) {
					the_title( sprintf( '<h4 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' );
				} else {
					the_title( '<h4 class="post_title entry-title">', '</h4>' );
				}
				do_action( 'chinchilla_action_after_post_title' );
			}

			if( !in_array( $chinchilla_post_format, array( 'quote', 'aside', 'link', 'status' ) ) ) {
				// More button
				if ( apply_filters( 'chinchilla_filter_show_blog_readmore', ! $chinchilla_show_title || ! empty( $chinchilla_template_args['more_button'] ), 'classic' ) ) {
					if ( empty( $chinchilla_template_args['no_links'] ) ) {
						do_action( 'chinchilla_action_before_post_readmore' );
						chinchilla_show_post_more_link( $chinchilla_template_args, '<div class="more-wrap">', '</div>' );
						do_action( 'chinchilla_action_after_post_readmore' );
					}
				}
			}
			?>
		</div><!-- .entry-header -->
		<?php
	}

	// Post content
	if( in_array( $chinchilla_post_format, array( 'quote', 'aside', 'link', 'status' ) ) ) {
		ob_start();
		if (apply_filters('chinchilla_filter_show_blog_excerpt', empty($chinchilla_template_args['hide_excerpt']) && chinchilla_get_theme_option('excerpt_length') > 0, 'classic')) {
			chinchilla_show_post_content($chinchilla_template_args, '<div class="post_content_inner">', '</div>');
		}
		// More button
		if(! empty( $chinchilla_template_args['more_button'] )) {
			if ( empty( $chinchilla_template_args['no_links'] ) ) {
				do_action( 'chinchilla_action_before_post_readmore' );
				chinchilla_show_post_more_link( $chinchilla_template_args, '<div class="more-wrap">', '</div>' );
				do_action( 'chinchilla_action_after_post_readmore' );
			}
		}
		$chinchilla_content = ob_get_contents();
		ob_end_clean();
		chinchilla_show_layout($chinchilla_content, '<div class="post_content entry-content">', '</div><!-- .entry-content -->');
	}
	?>

</article></div><?php
// Need opening PHP-tag above, because <div> is a inline-block element (used as column)!

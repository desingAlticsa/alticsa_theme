<?php
/**
 * The custom template to display the content
 *
 * Used for index/archive/search.
 *
 * @package CHINCHILLA
 * @since CHINCHILLA 1.0.50
 */

$chinchilla_template_args = get_query_var( 'chinchilla_template_args' );
if ( is_array( $chinchilla_template_args ) ) {
	$chinchilla_columns    = empty( $chinchilla_template_args['columns'] ) ? 2 : max( 1, $chinchilla_template_args['columns'] );
	$chinchilla_blog_style = array( $chinchilla_template_args['type'], $chinchilla_columns );
} else {
	$chinchilla_blog_style = explode( '_', chinchilla_get_theme_option( 'blog_style' ) );
	$chinchilla_columns    = empty( $chinchilla_blog_style[1] ) ? 2 : max( 1, $chinchilla_blog_style[1] );
}
$chinchilla_blog_id       = chinchilla_get_custom_blog_id( join( '_', $chinchilla_blog_style ) );
$chinchilla_blog_style[0] = str_replace( 'blog-custom-', '', $chinchilla_blog_style[0] );
$chinchilla_expanded      = ! chinchilla_sidebar_present() && chinchilla_get_theme_option( 'expand_content' ) == 'expand';
$chinchilla_components    = ! empty( $chinchilla_template_args['meta_parts'] )
							? ( is_array( $chinchilla_template_args['meta_parts'] )
								? join( ',', $chinchilla_template_args['meta_parts'] )
								: $chinchilla_template_args['meta_parts']
								)
							: chinchilla_array_get_keys_by_value( chinchilla_get_theme_option( 'meta_parts' ) );
$chinchilla_post_format   = get_post_format();
$chinchilla_post_format   = empty( $chinchilla_post_format ) ? 'standard' : str_replace( 'post-format-', '', $chinchilla_post_format );

$chinchilla_blog_meta     = chinchilla_get_custom_layout_meta( $chinchilla_blog_id );
$chinchilla_custom_style  = ! empty( $chinchilla_blog_meta['scripts_required'] ) ? $chinchilla_blog_meta['scripts_required'] : 'none';

if ( ! empty( $chinchilla_template_args['slider'] ) || $chinchilla_columns > 1 || ! chinchilla_is_off( $chinchilla_custom_style ) ) {
	?><div class="
		<?php
		if ( ! empty( $chinchilla_template_args['slider'] ) ) {
			echo 'slider-slide swiper-slide';
		} else {
			echo esc_attr( ( chinchilla_is_off( $chinchilla_custom_style ) ? 'column' : sprintf( '%1$s_item %1$s_item', $chinchilla_custom_style ) ) . "-1_{$chinchilla_columns}" );
		}
		?>
	">
	<?php
}
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
			'post_item post_item_container post_format_' . esc_attr( $chinchilla_post_format )
					. ' post_layout_custom post_layout_custom_' . esc_attr( $chinchilla_columns )
					. ' post_layout_' . esc_attr( $chinchilla_blog_style[0] )
					. ' post_layout_' . esc_attr( $chinchilla_blog_style[0] ) . '_' . esc_attr( $chinchilla_columns )
					. ( ! chinchilla_is_off( $chinchilla_custom_style )
						? ' post_layout_' . esc_attr( $chinchilla_custom_style )
							. ' post_layout_' . esc_attr( $chinchilla_custom_style ) . '_' . esc_attr( $chinchilla_columns )
						: ''
						)
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
	// Custom layout
	do_action( 'chinchilla_action_show_layout', $chinchilla_blog_id, get_the_ID() );
	?>
</article><?php
if ( ! empty( $chinchilla_template_args['slider'] ) || $chinchilla_columns > 1 || ! chinchilla_is_off( $chinchilla_custom_style ) ) {
	?></div><?php
	// Need opening PHP-tag above just after </div>, because <div> is a inline-block element (used as column)!
}

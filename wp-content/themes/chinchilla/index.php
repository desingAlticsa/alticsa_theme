<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: //codex.wordpress.org/Template_Hierarchy
 *
 * @package CHINCHILLA
 * @since CHINCHILLA 1.0
 */

$chinchilla_template = apply_filters( 'chinchilla_filter_get_template_part', chinchilla_blog_archive_get_template() );

if ( ! empty( $chinchilla_template ) && 'index' != $chinchilla_template ) {

	get_template_part( $chinchilla_template );

} else {

	chinchilla_storage_set( 'blog_archive', true );

	get_header();

	if ( have_posts() ) {

		// Query params
		$chinchilla_stickies   = is_home()
								|| ( in_array( chinchilla_get_theme_option( 'post_type' ), array( '', 'post' ) )
									&& (int) chinchilla_get_theme_option( 'parent_cat' ) == 0
									)
										? get_option( 'sticky_posts' )
										: false;
		$chinchilla_post_type  = chinchilla_get_theme_option( 'post_type' );
		$chinchilla_args       = array(
								'blog_style'     => chinchilla_get_theme_option( 'blog_style' ),
								'post_type'      => $chinchilla_post_type,
								'taxonomy'       => chinchilla_get_post_type_taxonomy( $chinchilla_post_type ),
								'parent_cat'     => chinchilla_get_theme_option( 'parent_cat' ),
								'posts_per_page' => chinchilla_get_theme_option( 'posts_per_page' ),
								'sticky'         => chinchilla_get_theme_option( 'sticky_style' ) == 'columns'
															&& is_array( $chinchilla_stickies )
															&& count( $chinchilla_stickies ) > 0
															&& get_query_var( 'paged' ) < 1
								);

		chinchilla_blog_archive_start();

		do_action( 'chinchilla_action_blog_archive_start' );

		if ( is_author() ) {
			do_action( 'chinchilla_action_before_page_author' );
			get_template_part( apply_filters( 'chinchilla_filter_get_template_part', 'templates/author-page' ) );
			do_action( 'chinchilla_action_after_page_author' );
		}

		if ( chinchilla_get_theme_option( 'show_filters' ) ) {
			do_action( 'chinchilla_action_before_page_filters' );
			chinchilla_show_filters( $chinchilla_args );
			do_action( 'chinchilla_action_after_page_filters' );
		} else {
			do_action( 'chinchilla_action_before_page_posts' );
			chinchilla_show_posts( array_merge( $chinchilla_args, array( 'cat' => $chinchilla_args['parent_cat'] ) ) );
			do_action( 'chinchilla_action_after_page_posts' );
		}

		do_action( 'chinchilla_action_blog_archive_end' );

		chinchilla_blog_archive_end();

	} else {

		if ( is_search() ) {
			get_template_part( apply_filters( 'chinchilla_filter_get_template_part', 'templates/content', 'none-search' ), 'none-search' );
		} else {
			get_template_part( apply_filters( 'chinchilla_filter_get_template_part', 'templates/content', 'none-archive' ), 'none-archive' );
		}
	}

	get_footer();
}

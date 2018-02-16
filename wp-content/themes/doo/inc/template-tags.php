<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package doo
 */

if ( ! function_exists( 'doo_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function doo_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>';
		
		echo '<span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.

		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'doo' ) );
			if ( $categories_list ) {
				echo '<span class="cat-links">' . $categories_list. '</span>';
			}
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'doo' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				)
			);
			echo '</span>';
		}
		
		edit_post_link( esc_html__( 'Edit', 'doo' ), '<span class="edit-link">', '</span>' );
	}
endif;

if ( ! function_exists( 'doo_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function doo_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list();
			if ( $tags_list ) {
				echo  '<span class="tags-links">' .  $tags_list . '</span>';
			}
		}
	}
endif;

function doo_posts_navigation() {
	the_posts_navigation(array(
		'prev_text' => '<i class="fa fa-caret-left" aria-hidden="true"></i> '.esc_html__('Older posts','doo'),
		'next_text'  => esc_html__('Newer posts','doo').' <i class="fa fa-caret-right" aria-hidden="true"></i>'		   
	));
}

function doo_post_navigation(){
	the_post_navigation( array(
        'prev_text' => '<i class="fa fa-caret-left" aria-hidden="true"></i> %title',
        'next_text' => '%title <i class="fa fa-caret-right" aria-hidden="true"></i>'
	) );
}

function doo_comments_navigation(){
	the_comments_navigation(array(
		'prev_text' => '<i class="fa fa-caret-left" aria-hidden="true"></i> '.esc_html__( 'Older comments' ,'doo'),
		'next_text' => esc_html__( 'Newer comments' ,'doo').' <i class="fa fa-caret-right" aria-hidden="true"></i>'
	));
}

function doo_posts_pagination(){
	the_posts_pagination(array(
		'prev_text' => '<i class="fa fa-caret-left" aria-hidden="true"></i>',
		'next_text' => '<i class="fa fa-caret-right" aria-hidden="true"></i>'
	));
}
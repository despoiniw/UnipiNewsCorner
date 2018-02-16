<?php
/**
 * The template for displaying posts in the Image post format
 *
 * @since 3.0.0
 */
global $mb_content_area;
$class = bavotasan_article_class();
?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( $class ); ?>>

	    <?php get_template_part( 'template-parts/content', 'header' ); ?>

	    <div class="entry-content">
	        <?php
			if( has_post_thumbnail() && ( ! is_single() || 'sidebar' == $mb_content_area ) ) {
				echo '<a href="' . get_permalink() . '">';
				the_post_thumbnail( 'large', array( 'class' => 'alignnone' ) );
				echo '</a>';
			} else {
				the_content( __( 'Read more &rarr;', 'magazine-basic' ) );
			}
			?>
	    </div><!-- .entry-content -->

	    <?php get_template_part( 'template-parts/content', 'footer' ); ?>

	</article>
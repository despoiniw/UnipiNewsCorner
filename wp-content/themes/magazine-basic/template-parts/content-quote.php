<?php
/**
 * The template for displaying posts in the Quote post format
 *
 * @since 3.0.0
 */
$class = bavotasan_article_class();
?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( $class ); ?>>
	    <i class="fa fa-quote-left quote"></i>

	    <div class="entry-content">
		    <?php the_content( __( 'Read more &rarr;', 'magazine-basic' ) ); ?>
	    </div><!-- .entry-content -->

	    <?php get_template_part( 'template-parts/content', 'footer' ); ?>

	</article>
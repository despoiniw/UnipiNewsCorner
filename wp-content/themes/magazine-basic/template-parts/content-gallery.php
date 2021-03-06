<?php
/**
 * The template for displaying posts in the Gallery post format
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
			if ( is_single() && 'sidebar' != $mb_content_area ) {
				the_content( __( 'Read more &rarr;', 'magazine-basic' ) );
			} else {
				$images = bavotasan_get_gallery_images();
				if ( has_post_thumbnail() ) {
					$image_img_tag = wp_get_attachment_image( get_post_thumbnail_id(), 'large', false, array( 'class' => 'img-thumbnail' ) );
				}

				if ( $images ) {
					$total_images = count( $images );
					$image = array_shift( $images );
					if ( isset( $image ) ) {
						$image_img_tag = ( isset( $image_img_tag ) ) ? $image_img_tag : wp_get_attachment_image( $image, 'large', false, array( 'class' => 'img-thumbnail' ) );
					}
				}

				if ( isset( $image_img_tag ) ) {
					?>
					<a class="gallery-thumb alignnone" href="<?php the_permalink(); ?>"><?php echo $image_img_tag; ?></a>
					<?php
				}

				if ( $total_images ) {
				?>
					<p class="gallery-text"><em><?php printf( _n( 'This gallery contains <a %1$s>%2$s photo &rarr;</a>', 'This gallery contains <a %1$s>%2$s photos &rarr;</a>', $total_images, 'magazine-basic' ), 'href="' . get_permalink() . '" title="' . sprintf( esc_attr__( 'Permalink to %s', 'magazine-basic' ), the_title_attribute( 'echo=0' ) ) . '" rel="bookmark"',
						number_format_i18n( $total_images ) ); ?></em></p>
					<?php
				}
			}
			?>
	    </div><!-- .entry-content -->

	    <?php get_template_part( 'template-parts/content', 'footer' ); ?>

	</article>
<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package doo
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer">
		<div class="site-info">
			<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'doo' ) ); ?>"><?php
				/* translators: %s: CMS name, i.e. WordPress. */
				printf( esc_html__( 'Proudly powered by %s', 'doo' ), 'WordPress' );
			?></a>
			<span class="sep"> | </span>
			<?php
				/* translators: 1: Theme name, 2: Theme author. */
				printf( esc_html__( 'Theme: %1$s by %2$s.', 'doo' ), 'Doo', '<a href="http://themevs.com/">ThemeVS</a>' );
			?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
    
    <button id="back_top" aria-label="<?php esc_attr_e( 'Top', 'doo' ); ?>" value="<?php echo esc_attr_x( 'Top', 'top button', 'doo' ); ?>"><i class="fa fa-angle-up" aria-hidden="true"></i><span class="screen-reader-text"><?php echo esc_html_x( 'Top', 'top button', 'doo' ); ?></span></button>
    </div>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
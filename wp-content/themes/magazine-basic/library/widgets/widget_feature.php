<?php
/**
 * Functionality for Featured Post widget
 *
 * @since Magazine Basic 3.0
 */
class MB_FeaturedPosts_Widget extends WP_Widget {
	function __construct() {
		parent::__construct(
			'mb_featured_posts', // Base ID
			__( 'Featured Posts', 'magazine-basic' ), // Name
			array( 'description' => __( 'Display featured posts from a category', 'magazine-basic' ), ) // Args
		);
	}

	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		$title = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );
		$sticky = get_option( 'sticky_posts' );
		$number = empty( $instance['number'] ) ? 1 : (int) $instance['number'];
		$cat = empty( $instance['category'] ) ? 0 : (int) $instance['category'];

		if ( is_single() ) {
			array_push( $sticky, get_the_ID() );
		}

		echo $before_widget;
	    if ( ! empty( $title ) ) {
	    	echo $before_title . $title . $after_title;
		} else {
			echo '<br />';
		}

		$featuredPosts = new WP_Query( array(
			'posts_per_page' => $number,
			'cat' => $cat,
			'post__not_in' => $sticky,
			'no_found_rows' => true
		) );

	    while ( $featuredPosts->have_posts() ) : $featuredPosts->the_post();
	    	global $mb_content_area, $more;
	    	$mb_content_area = 'sidebar';

	    	get_template_part( 'template-parts/content', get_post_format() );
		endwhile;

		wp_reset_postdata();

		echo $after_widget;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'category' => '0', 'number' => '1' ) );
		$title = strip_tags( $instance['title'] );
		$category = strip_tags( $instance['category'] );
		$number = strip_tags( $instance['number'] );
		$selectname = $this->get_field_name( 'category' );
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'magazine-basic' ); ?>: <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Category', 'magazine-basic' ); ?>: <?php wp_dropdown_categories( 'show_option_all=' . __( 'All', 'magazine-basic' ) . ' &hide_empty=0&name=' . $selectname . '&selected=' . $category ); ?></label></p>
		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of Posts', 'magazine-basic' ); ?>:</label> <input size="3" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" /></p>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['category'] = (int) $new_instance['category'];
		$instance['number'] = (int) $new_instance['number'];
		return $instance;
	}

}
register_widget( 'MB_FeaturedPosts_Widget' );
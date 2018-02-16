 <?php
	
$args = array(
    'flex-width'    => true,
    'width'         => 980,
    'flex-height'   => true,
    'height'        => 200,
    'default-image' => get_template_directory_uri() . '/images/headers/banner-image.jpeg',
);

add_theme_support( 'custom-header', $args );



function qoddy_enqueue_styles() {

	$parent_style = 'doo-style'; // Replace this with parent style handle.
	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'qoddy-style', get_stylesheet_uri(), array( $parent_style ) );
}

add_action( 'wp_enqueue_scripts', 'qoddy_enqueue_styles' );

add_action('wp_enqueue_scripts', 'qoddy_styles', PHP_INT_MAX);
function qoddy_styles()
{
    wp_enqueue_style('bootstrap', get_stylesheet_directory_uri().'/assets/bootstrap/css/bootstrap.min.css', false, NULL, 'all');
}



<?php
/**
 * doo Theme Customizer
 *
 * @package doo
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function doo_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'        => '.site-title a',
			'render_callback' => 'doo_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'        => '.site-description',
			'render_callback' => 'doo_customize_partial_blogdescription',
		) );
	}

	$wp_customize->add_panel( 'theme_options' ,array(
		'title'       => esc_html__( 'Theme Options', 'doo' ),
		'description' => ''
	));
	//----------------------------------------------------------------------------------
	// Section: Colors
	//----------------------------------------------------------------------------------
	$wp_customize->add_section( 'colors_general' , array(
   		'title'      => esc_html__('Colors', 'doo'),
        'panel'       => 'theme_options',
   		'priority'   => 1
	));
    $wp_customize->add_setting( 'theme_color', array(
        'default'        => '#c51e3a',
        'sanitize_callback'    => 'sanitize_hex_color_no_hash',
        'sanitize_js_callback' => 'maybe_hash_hex_color'
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'theme_color', array(
        'label'        => esc_html__( 'Theme Color', 'doo' ),
        'section'    => 'colors_general'
    )));
	
	//----------------------------------------------------------------------------------
	// Section: General Settings
	//----------------------------------------------------------------------------------
	$wp_customize->add_section( 'general' , array(
   		'title'       => esc_html__('General Settings', 'doo'),
        'panel'       => 'theme_options',
   		'priority'    => 2
	));
	$wp_customize->add_setting('home_layout',array(
		'default'     => false,
		'sanitize_callback' => 'doo_sanitize_checkbox'
	));
	$wp_customize->add_control(new WP_Customize_Control($wp_customize,'home_layout',array(
		'label'          => esc_html__( 'Disable Sidebar on Home Page, Archive Page, Single Post', 'doo' ),
		'section'        => 'general',
		'settings'       => 'home_layout',
		'type'           => 'checkbox'
	)));
	$wp_customize->add_setting('blog_pagination',array(
		'default'     => 'pagination',
		'sanitize_callback' => 'doo_sanitize_blog_pagination'
	));
	$wp_customize->add_control(new WP_Customize_Control($wp_customize,'blog_pagination',array(
		'label'          => esc_html__('Blog Pagination or Navigation', 'doo'),
		'section'        => 'general',
		'settings'       => 'blog_pagination',
		'type'           => 'radio',
		'choices'        => array(
			'pagination'   => esc_html__('Pagination', 'doo'),
			'navigation'   => esc_html__('Navigation', 'doo')
		)
	)));
	
	//----------------------------------------------------------------------------------
	// Section: Page Settings
	//----------------------------------------------------------------------------------
	$wp_customize->add_section( 'page' , array(
   		'title'      => esc_html__('Page Settings', 'doo'),
        'panel'       => 'theme_options',
   		'priority'   => 3
	));
	$wp_customize->add_setting('page_comments',array(
		'default'     => false,
		'sanitize_callback' => 'doo_sanitize_checkbox'
	));
	$wp_customize->add_control(new WP_Customize_Control($wp_customize,'page_comments',array(
		'label'      => esc_html__('Hide Comments', 'doo'),
		'section'    => 'page',
		'settings'   => 'page_comments',
		'type'		 => 'checkbox'
	)));
	$wp_customize->add_setting('page_layout',array(
		'default'    => 'right_sidebar',
		'sanitize_callback' => 'doo_sanitize_checkbox'
	));
	$wp_customize->add_control(new WP_Customize_Control($wp_customize,'page_layout',array(
		'label'      => esc_html__( 'Disable Sidebar on Single Page', 'doo' ),
		'section'    => 'page',
		'settings'   => 'page_layout',
		'type'       => 'checkbox'
	)));
}
add_action( 'customize_register', 'doo_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function doo_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function doo_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

function doo_sanitize_checkbox( $input ){
    if ( $input == 1 || $input == 'true' || $input === true ) {
        return 1;
    } else {
        return 0;
    }
}
function doo_sanitize_number( $number, $setting ) {
    $number = absint( $number );
    return ( $number ? $number : $setting->default );
}

function doo_sanitize_blog_pagination( $input ) {
	if ( ! in_array( $input, array( 'pagination', 'navigation' ) ) ) {
		$input = 'pagination';
	}
	return $input;
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function doo_customize_preview_js() {
	wp_enqueue_script( 'doo-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'doo_customize_preview_js' );
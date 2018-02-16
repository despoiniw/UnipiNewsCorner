<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package doo
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function doo_body_classes( $classes ) {
	// Adds a class of hfeed to non-doogular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	return $classes;
}
add_filter( 'body_class', 'doo_body_classes' );

/**
 * Add a pingback url auto-discovery header for doogularly identifiable articles.
 */
function doo_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'doo_pingback_header' );

function doo_sidebar_select() {	
	if(is_page()){
		$page_layout = get_theme_mod( 'page_layout', 0);
		if($page_layout){
		
		}else{
			get_sidebar();
		}
	}else{
		$home_layout = get_theme_mod( 'home_layout', 0);
		if($home_layout){
		
		}else{
			get_sidebar();
		}
	}
}

function doo_body_class( $classes ) {
	if(is_page()){
		$page_layout = get_theme_mod( 'page_layout', 0);
		if($page_layout){
			$classes[] = 'no-sidebar-full-width';
		}else{
	
		}
	}else{
		$home_layout = get_theme_mod( 'home_layout', 0);
		if($home_layout){
			$classes[] = 'no-sidebar-full-width';
		}else{

		}
	}
	return $classes;
}
add_filter( 'body_class', 'doo_body_class' );

function doo_get_custom_style(){
    $css = '';
    $primary_color = esc_attr( get_theme_mod( 'theme_color' ) );
    if ( $primary_color ) {
        $primary_color = '#'.$primary_color;
$css .= '
blockquote {border-left: 4px solid '.$primary_color.';}	
a {color: '.$primary_color.';}
a:hover,a:focus,a:active {color: '.$primary_color.';}
.site-header{border-top: 3px solid '.$primary_color.';}
.site-title a:hover,.site-title a:focus,.site-title a:active{color:'.$primary_color.';}
.main-navigation div >ul >li >a:hover,.main-navigation div >ul >li >a:focus{color:'.$primary_color.';}
.main-navigation div >ul >li ul li a:hover,.main-navigation div >ul >li ul li a:focus{color:'.$primary_color.';}
.menu-toggle{color:'.$primary_color.';}
.entry-title a:hover,.entry-title a:focus{color:'.$primary_color.';}	
.sticky .entry-title,.sticky .entry-title a{color:'.$primary_color.';}
.widget a:hover,.widget a:focus{color:'.$primary_color.';}
.comment-form .logged-in-as a:hover,.comment-form .logged-in-as a:focus{color:'.$primary_color.';}
.pagination .nav-links a:hover,.pagination .nav-links a:focus{background:'.$primary_color.';}
.pagination .nav-links .current{background:'.$primary_color.';}
.site-info a:hover,.site-info a:focus{color:'.$primary_color.';}
#back_top {background:'.$primary_color.';}
';
    }
    return $css;
}
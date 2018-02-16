<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package qoddy
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'qoddy' ); ?></a>

	<header id="masthead" class="site-header">
	<div class="container">
	<div class="row">
	<div class="col-md-9 col-xs-12">
		<div class="site-branding">
			<?php
			the_custom_logo();
			if ( is_front_page() && is_home() ) : ?>
				<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
			<?php else : ?>
				<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
			<?php
			endif;

			$description = get_bloginfo( 'description', 'display' );
			if ( $description || is_customize_preview() ) : ?>
				<p class="site-description"><?php echo $description; /* WPCS: xss ok. */ ?></p>
			<?php
			endif; ?>
            
            <div class="clear"></div>
		</div><!-- .site-branding -->
	</div><!--.col-->

	<div class="col-md-3 col-xs-12">
		<?php get_search_form();  ?>

	</div><!--.col-->
	</div><!--.row-->

	</div><!--container-->
	</header><!-- #masthead -->
    
    <div class="container">
    <nav id="site-navigation" class="main-navigation">
        <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" value="<?php echo esc_attr_x( 'Menu', 'primary menu', 'qoddy' ); ?>"><i class="fa fa-bars" aria-hidden="true"></i><span class="screen-reader-text"><?php echo esc_html_x( 'Menu', 'primary menu', 'qoddy' ); ?></span></button>
        <?php
            wp_nav_menu( array(
                'theme_location' => 'menu-1',
                'menu_id'        => 'primary-menu',
            ) );
        ?>
        <div class="clear"></div>
    </nav><!-- #site-navigation -->
    </div><!--container-->
   
<?php if ( is_home() || is_front_page() ): ?>
  <div class="text-center"><img src="<?php header_image(); ?>" height="<?php echo esc_attr(get_custom_header()->height); ?>" width="<?php echo esc_attr(get_custom_header()->width); ?>" alt="" /></div>
<?php endif; ?>
   
<div class="container">
	<div id="content" class="site-content">

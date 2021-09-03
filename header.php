<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WP_Bootstrap_Starter
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="distribution" content="global" />
	<!--<meta http-equiv="X-UA-Compatible" content="IE=edge">-->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php
    if (preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false)) 
    {
        echo '<body style = "display:none;"></body>';
        echo "<script type='text/javascript'>alert('Sorry, Internet Explorer is not supported in this webpage');</script>";
    }
?>

<?php 
    // WordPress 5.2 wp_body_open implementation
    if ( function_exists( 'wp_body_open' ) ) {
        wp_body_open();
    } else {
        do_action( 'wp_body_open' );
    }
	
	// --- Show checks ---
	$post_id = $wp_query->post->ID;
	$show = radio_station_get_show($post_id );
?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'wp-bootstrap-starter' ); ?></a>
    <?php if(!is_page_template( 'blank-page.php' ) && !is_page_template( 'blank-page-with-container.php' )): ?>
	<header id="masthead" class="site-header navbar-static-top navbar-dark" role="banner">
        <div class="container">
            <nav class="navbar navbar-expand-lg p-0"> 
                <div class="navbar-brand flex-shrink-0 ml-1 mr-1">
                    <?php if ( get_theme_mod( 'wp_bootstrap_starter_logo' ) ): ?>
                        <a href="<?php echo esc_url( home_url( '/' )); ?>">
                            <img class="img-fluid" src="<?php echo esc_url(get_theme_mod( 'wp_bootstrap_starter_logo' )); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                        </a>
                    <?php else : ?>
                        <a class="site-title" href="<?php echo esc_url( home_url( '/' )); ?>"><?php esc_url(bloginfo('name')); ?></a>
                    <?php endif; ?>

                </div>
				<button class="navbar-toggler ml-auto" type="button" data-toggle="collapse" data-target="#main-nav" aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<?php
                wp_nav_menu(array(
                'theme_location'    => 'primary',
                'container'       => 'div',
                'container_id'    => 'main-nav',
                'container_class' => 'collapse navbar-collapse',
                'menu_id'         => false,
                'menu_class'      => 'navbar-nav mt-2 mt-lg-0',
                'depth'           => 3,
                'fallback_cb'     => 'wp_bootstrap_navwalker::fallback',
                'walker'          => new wp_bootstrap_navwalker()
                ));
                ?>
				
				<!-- Desktop Player in Navbar -->
				<div class="player_desk pt-1 pb-1">
					<div class="d-none d-lg-flex flex-wrap flex-row justify-content-end align-items-center">
						<div class="player_button pr-1"><i class="fas fa-play-circle fa-3x" id="player_desk_button" onclick="togglePlay()"></i></div>
						<div class="player_art px-1"><img id="player_desk_artwork" alt="Song Artwork" class="img-fluid" src="/wp-content/uploads/2020/11/radiodepaul_favicon.png" height="50" width="50" /></div>
						<div class="player_info pl-1">
							<span id="player_desk_title">Radio DePaul</span><br />
							<span id="player_desk_artist">Chicago's College Connection</span>
						</div>
					</div>
				</div>				
            </nav>
        </div>
		<!-- Mobile Player in Fixed Bottom -->
		<div class="fixed-bottom player_mob d-lg-none rdp-black py-2">
			<div class="d-flex flex-row">
				<div class="player_art align-self-start ml-2"><img id="player_mob_artwork" alt="Song Artwork" class="img-fluid" src="/wp-content/uploads/2020/11/radiodepaul_favicon.png" /></div>
				<div class="player_info w-75 flex-fill mx-2">
					<span id="player_mob_title">Radio DePaul</span><br />
					<span id="player_mob_artist">Chicago's College Connection</span>
				</div>
				<div class="player_button align-self-end mr-2"><i class="fas fa-play-circle fa-3x" id="player_mob_button" onclick="togglePlay()"></i></div>
			</div>
		</div>
		<!-- Audio Stream -->
		<audio src="https://radiodepaul.streamguys1.com/live">&nbsp;</audio>
	</header><!-- #masthead -->
    <?php if(is_front_page() && !get_theme_mod( 'header_banner_visibility' )): ?>
        <div id="page-sub-header" <?php if(has_header_image()) { ?>style="background-image: url('<?php header_image(); ?>');" <?php } ?>>
            <div class="container">
                <h1>
                    <?php
                    if(get_theme_mod( 'header_banner_title_setting' )){
                        echo esc_attr( get_theme_mod( 'header_banner_title_setting' ) );
                    }else{
                        echo 'WordPress + Bootstrap';
                    }
                    ?>
                </h1>
                <p>
                    <?php
                    if(get_theme_mod( 'header_banner_tagline_setting' )){
                        echo esc_attr( get_theme_mod( 'header_banner_tagline_setting' ) );
                }else{
                        echo esc_html__('To customize the contents of this header banner and other elements of your site, go to Dashboard > Appearance > Customize','wp-bootstrap-starter');
                    }
                    ?>
                </p>
                <a href="#content" class="page-scroller"><i class="fa fa-fw fa-angle-down"></i></a>
            </div>
        </div>
    <?php endif; ?>
	<div id="content" class="site-content">
		<?php 
		if(($show->post_type) == 'show'):
			get_template_part( 'template-parts/show', 'header' );
		endif; 
		?>
		<div class="container">
			<div class="row">
                <?php endif; ?>
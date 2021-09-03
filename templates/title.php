<?php
/* 
 * Template Name: With Title
 * Template to include title
 * Author : Matthew Mola
 * Date: 11/12/2020
*/ 
 ?>

<?php get_header(); ?>

<div id="primary" class="content-area container">
<main id="main" class="site-main" role="main">
<?php
 
// Start the loop.
while ( have_posts() ) : the_post();
{	
?>
	<header class="entry-header">
		<?php the_title( '<h1 class="text-uppercase">', '</h1>' ); ?>
	</header><!-- .entry-header -->
	<?php
	get_template_part( 'template-parts/content', 'page' );	
}
// End of the loop.
endwhile;
?>
</main><!-- .site-main -->
 
<?php get_sidebar( 'content-bottom' ); ?>
 
</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
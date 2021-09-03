<?php
/*
 * Template Name: Newsletter
 * Mailchimp 
 * Author : Matthew Mola
 * Date: 11/29/2020
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
		<?php $output .= the_title( '<h1 class="text-uppercase">', '</h1>' ); ?>
	</header><!-- .entry-header -->
	<?php
	get_template_part( 'template-parts/content', 'page' );	
	
	// --- get all the required info ---
	$newsletter_rss = "https://us4.campaign-archive.com/feed?u=0ab60cabbfd37d80f6ca0e064&id=fc1ba3af76";
	$feeds = simplexml_load_file($newsletter_rss);
	$items = $feeds->channel->item;

	//Episodes Loop
	foreach ($items as $item) 
	{
		$title =  html_entity_decode(strip_tags($item->title));	
		$date = $item->pubDate;	
		$link = $item->link;
		$icon = '<i class="my-auto mx-auto far fa-play-circle fa-3x"></i>';	
		
		$post_image = wp_get_attachment_image_src( '648', $avatar_size )[0];
		$post_image = '<img class="img-fluid" alt="Radio DePaul Newsletter Logo" src="'. $post_image .'">' . "\n";
	
		// --- Show Row ---
		$classes = array( 'row', 'd-flex' );
		$classlist = implode( ' ' , $classes );
		$string .= "\n\n";
		$string .= '<div class="' . esc_attr( $classlist ) . '">' . "\n";

		// --- Show Image Box ---
		$string .= '<div class="show-image w-75 col-md-4 col-12">' . "\n";
		$string .= $post_image;
		$string .= '</div>' . "\n";	

		// --- Show Information ---
		$string .= '<div class="show-info col-md-8 col-12 align-self-center">' . "\n";
		
		// --- Category Title ---
		$string .= '<div class="category-title text-uppercase">' . "\n";
		$string .= $category_title;
		$string .= '</div>' . "\n";

		// --- Get Title ---
		$string .= '<div class="post-title">' . "\n";
		$string .= $title;
		$string .= '</div>' . "\n";	
		
		// --- Get Preview ---
		$string .= '<div class="post-preview">' . "\n";
		$string .= $date;
		$string .= '</div>' . "\n";
		
		// --- Read More ---
		$string .= '<div class="read-more">' . "\n";
		$string .= '<a href="' . esc_url( $link ) . '" target="_blank">Read More</a>' . "\n";
		$string .= '</div>' . "\n";
		
		// --- Close Category Box and Post Row ---
		$string .= '</div>' . "\n";
		$string .= '</div>' . "\n";	
	}
	
	$output .= $string;
	
	echo $output;
}
// End of the loop.
endwhile;
?>
</main><!-- .site-main -->
<?php get_sidebar( 'content-bottom' ); ?>
</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
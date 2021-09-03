<?php
/* 
 * Template Name: Awards
 * Template for retrieving awards
 * Author : Matthew Mola
 * Date: 11/18/2020
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
		<?php echo the_title( '<h1 class="text-uppercase">', '</h1>' ); ?>
	</header><!-- .entry-header -->
	<?php
	// --- get all the required info ---
	$content = get_the_content();
	$split = explode("<p>end</p>", $content);
	
	// --- Create Year Loop ---
	foreach ($split as $year)
	{
		$dom = new DOMDocument();
		$dom->loadHTML(mb_convert_encoding($year, 'HTML-ENTITIES', 'UTF-8'));		
		$img = $dom->getElementsByTagName('img')[0];
		$heading = $dom->getElementsByTagName('h3')[0];
		$paragraphs = $dom->getElementsByTagName('p');
		
		if($img)
		{
			$img_url = $img->getAttribute('src');
			$img_alt = $img->getAttribute('alt');
			$element .= '<img class="img-fluid" src="'. $img_url .'" alt="'. $img_alt .'" />' . "\n";
		}
		
		//var_dump($heading->textContent);
		$element .= '<h3>' . $heading->textContent . '</h3>' . "\n";
		$element .= '<div class="row d-flex justify-content-between">' . "\n";
		
		// --- Create Award Loop ---
		foreach ($paragraphs as $award)
		{
			//var_dump($award->textContent);
			if(strpos($award->textContent, " : ") !== false)
			{
				$pieces = explode(" : ", $award->textContent);
			}
			
			$element .= '<div class="col-md-6 col-12 d-flex">' . "\n";			
			$element .= '<div class="flex-fill rdp-black text-white pl-2 pr-2 mb-2">';
			$element .= '<span class="award-year">' . $pieces[0] . '</span><br>' . "\n";
			$element .= '<span class="award-name">' . $pieces[1] . '</span>' . "\n";
			//$element .= '<br><br>';
			$element .= '</div>' . "\n";	
			$element .= '</div>' . "\n";
		}
		// --- End of Award Loop ---
		$element .= '</div>' . "\n";	
	}
	// --- End of Years Loop ---	
	echo $element;	
}
// End of the loop.
endwhile;
?>
</main><!-- .site-main -->
 
<?php get_sidebar( 'content-bottom' ); ?>
 
</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
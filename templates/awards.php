<?php
/* 
 * Template Name: Awards
 * Template for retrieving awards
 * Author : Matthew Mola
 * Date: 6/13/2021
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
	$regex = '/^<.*>[0-9]+<.*>$/m';
	//Check for year and split the content into an array
	$awards = preg_split($regex, $content);
	//Get header image
	$image = strip_tags($awards[0], "<img>");
	unset($awards[0]);
	$awards = array_values($awards);
	//Check for year and put that into an array
	preg_match_all($regex, $content, $award_years, PREG_PATTERN_ORDER);
	$award_years = $award_years[0];

	//Echo header image
	echo $image;
	
	// --- Create Year Loop ---
	foreach ($award_years as $i => $award_year)
	{
		//Get awards per year index
		$dom = new DOMDocument();
		$dom->loadHTML(mb_convert_encoding($awards[$i], 'HTML-ENTITIES', 'UTF-8'));
		$paragraphs = $dom->getElementsByTagName('p');

		$element .= $award_year . "\n";
		$element .= '<div class="row d-flex justify-content-between">' . "\n";
		
		// --- Create Award Loop ---
		foreach ($paragraphs as $award)
		{
			if(strpos($award->textContent, " : ") !== false)
			{
				$pieces = explode(" : ", $award->textContent);
				$element .= '<div class="col-md-6 col-12 d-flex">' . "\n";			
				$element .= '<div class="flex-fill rdp-black text-white pl-2 pr-2 mb-2">';
				$element .= '<span class="award-year">' . $pieces[0] . '</span><br>' . "\n";
				$element .= '<span class="award-name">' . $pieces[1] . '</span>' . "\n";
				//$element .= '<br><br>';
				$element .= '</div>' . "\n";	
				$element .= '</div>' . "\n";
			}
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
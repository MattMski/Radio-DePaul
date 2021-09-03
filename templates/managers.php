<?php
/* 
 * Template Name: Managers
 * Template for retrieving managers
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
	//get_template_part( 'template-parts/content', 'page' );	

	// --- get all the required info ---
	$content = get_the_content();
	$regex = '/^<.*[0-9]+-[0-9]+.*>$/m';
	//Check for year-year and split the content into an array
	$manager_people = preg_split($regex, $content);
	unset($manager_people[0]);
	$manager_people = array_values($manager_people);
	//Check for year-year and put that into an array
	preg_match_all($regex, $content, $manager_years, PREG_PATTERN_ORDER);
	$manager_years = $manager_years[0];

	// --- Create Year Loop ---
	foreach ($manager_years as $i => $manager_year)
	{
		// Create Dropdown Loop
		$classes = array( 'dropdown-item' );
		$aria_expanded = 'false';
		//Manager years without heading
		$manager_year_wh = strip_tags($manager_year);
		
		if ( $i == 0 ) 
		{
			$classes[] = 'active';
			$aria_expanded = 'true';
		}
		$yeartotext = substr($manager_year_wh, 2, 2);
		$yeartotext = (new NumberFormatter("en", NumberFormatter::SPELLOUT))->format($yeartotext);
		$yid = strtolower( $yeartotext ) . "-managers";
		$classlist  = implode( ' ', $classes );

		// --- Dropdown ---	
		if( 0 == $i) {
			$dropdown_output = '<div class="dropdown">' ."\n";
			$dropdown_output .= '<button class="btn btn-rdp dropdown-toggle" type="button" id="dropdownCategory" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' ."\n";
			$dropdown_output .= 'Year' ."\n";
			$dropdown_output .= '</button>' ."\n";
			$dropdown_output .= '<div class="dropdown-menu" aria-labelledby="dropdownCategory">' ."\n";
		}

		$dropdown_output .= '<a class="' . esc_attr( $classlist ) . '" data-toggle="collapse" href="#' . strtolower( $yid ) . '" data-target="#' . strtolower( $yid ) . '" aria-expanded="' . $aria_expanded . '" aria-controls="' . strtolower( $yid ) . '"';
		$dropdown_output .= '>' . esc_html( $manager_year_wh ) . '</a>' . "\n";
		
		if ( $manager_year == end($manager_years) )
		{
			$dropdown_output .= '</div></div><br>' . "\n";
		}
		// --- End of Dropdown Loop --	
		
		//Get manager people per year index
		$dom = new DOMDocument();
		$dom->loadHTML(mb_convert_encoding($manager_people[$i], 'HTML-ENTITIES', 'UTF-8'));
		$paragraphs = $dom->getElementsByTagName('p');
		
		//Collapse by default and show current year
		$classes = array( 'collapse'  );
		if($i == 0) { $classes[] = 'show'; }
		$classlist = implode( ' ' , $classes );
		
		// --- Year Boxes Open ---
		$panels .= "\n";
		$panels .= '<div class="' . esc_attr( $classlist ) . '" id="' . $yid . '" data-parent="#panels">' . "\n";	
		
		$panels .= $manager_year . "\n";
		$panels .= '<div class="row">' . "\n";	
		
		// --- Create User Loop ---
		foreach ($paragraphs as $user)
		{
			if(strpos($user->textContent, " : ") !== false)
			{
				$pieces = explode(" : ", $user->textContent);
				
				$args= array(
				  'search' => $pieces[0], // or login or nicename in this example
				  'search_fields' => array('user_login','user_nicename','display_name')
				);
				$get_userid = (new WP_User_Query($args))->results[0]->ID;
				$user_id = $get_userid;
				$user_info = get_userdata($user_id);
				$user_display_name = $user_info->display_name;
				if (! $user_display_name) { $user_display_name = $pieces[0]; } 
				//$avatar = get_avatar_url($user_id);
				$avatar = get_wpupa_url($user_id, ['size' => 'medium']);
				$station_position = $pieces[1];
				//$station_position = get_the_author_meta('rdp_role', $user_id);
				$panels .= "<div class='col-xs-12 col-md-4'>" . "\n";
				//$string .= "<img class='img-fluid' src='" . esc_html( get_avatar_url($user,  array("size"=>500)) )  . "'>\n";   
				//$string .= '<div class="manager-avatar img-fluid">' . get_avatar($user_id, '500', '', '', $args = array( 'class' => 'img-fluid rdp_managers')) . '</div>'  . "\n";
				$panels .= '<div class="manager-avatar"><img class="img-fluid" alt="' . $user_display_name . "'s Picture" . '" src="' . $avatar . '"></div>' . "\n";			
				$panels .= '<div class="manager-box">' . "\n";				
				$panels .= "<div class='manager-name'>" . esc_html( $user_display_name  )  . "</div>" . "\n";
				$panels .= "<div class='manager-position'>" . $station_position . "</div>" . "\n"; 
				$panels .= "</div>" . "\n";
				$panels .= "</div>" . "\n";
			}
		}
		// --- End of User Loop ---
		$panels .= '</div>' . "\n";
		
		// --- Year Boxes Close ---
		$panels .= '</div>' . "\n";
	}
	// --- End of Years Loop ---
	
	// --- Create Panels Div View ---	
	$output .= '<div id="panels">'. "\n";
	$output .= $dropdown_output;
	$output .= "\n\n";
	$output .= $panels;
	
	// --- Panels Div View Close ---
	$output .= '</div>';
	
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
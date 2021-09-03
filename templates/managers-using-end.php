<?php
/* 
 * Template Name: Managers-end
 * Template for retrieving managers
 * Author : Matthew Mola
 * Date: 11/13/2020
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
	
	$args = array(
		'role'    => 'Manager',
		'orderby' => 'display_name',
		'order'   => 'ASC'
	);
	
	/*
	GM
	Program Director
	On-Air Director
	Music Director 
	Production Director
	Promotions Director
	Social Media Director
	News Directors (2)
	Senior Podcast Producer
	The Radio DePaul Producers (2)
	Red Light Recordings Producers (2)
	Street Team Coordinator
	Morale and Mediating Director
	Faculty Advisor
	
	*/
	
	$positions = array(
		0 => 'General Manager', 
		1 => 'Program Director', 
		2 => 'On-Air Director', 
		3 => 'Music Director', 
		4 => 'Production Director', 
		5 => 'Promotions Director', 
		6 => 'Social Media Director', 
		7 => 'News Director',  
		8 => 'Senior Podcast Producer', 
		9 => 'The Radio DePaul Podcast Producer', 
		10 => 'Red Light Recordings Producer', 
		11 => 'Street Team Coordinator', 
		12 => 'Morale and Mediating Director', 
		13 => 'Faculty Advisor', 
		14 => 'Web Developer', 	
	);
	
	$users = get_users( $args ); 
	
	//Create Users Array
	$managers = array();
	foreach ( $users as $i => $user )
	{
		$user_id = $user->ID;
		$station_position = trim(get_the_author_meta('rdp_role', $user_id));
		$keyval = array('ID' => $user->ID, 'name' => $user->display_name, 'position' => $station_position );	
		array_push($managers,$keyval);
	}

	//Position Sort Users Array
	// the key order of each id
	$orderIdKeys  = array_flip($positions);
	usort($managers, function ($u1, $u2)  use ($orderIdKeys) {

		// compare the keys of the ids in the $order array
		return $orderIdKeys[$u1['position']] >= $orderIdKeys[$u2['position']] ?  1 : -1;
	});
	//print_r($user_array);
	
	//$user->roles
	$string = "<div class='row'>" . "\n";
	foreach ( $managers as $user ) {
		$user_id = $user['ID'];
		//$user_id = $user->ID;
		$user_display_name = $user['name'];
		//$user_display_name = $user->display_name;
		//$avatar = get_avatar_url($user_id);
		$avatar = get_wpupa_url($user_id, ['size' => 'medium']);
		$station_position = $user['position'];
		//$station_position = get_the_author_meta('rdp_role', $user_id);
		$string .= "<div class='col-xs-12 col-md-4'>" . "\n";
		//$string .= "<img class='img-fluid' src='" . esc_html( get_avatar_url($user,  array("size"=>500)) )  . "'>\n";   
		//$string .= '<div class="manager-avatar img-fluid">' . get_avatar($user_id, '500', '', '', $args = array( 'class' => 'img-fluid rdp_managers')) . '</div>'  . "\n";
		$string .= '<div class="manager-avatar"><img class="img-fluid" alt="' . $user_display_name . "'s Picture" . '" src="' . $avatar . '"></div>' . "\n";			
		$string .= '<div class="manager-box">' . "\n";				
		$string .= "<div class='manager-name'>" . esc_html( $user_display_name  )  . "</div>" . "\n";
		$string .= "<div class='manager-position'>" . $station_position . "</div>" . "\n"; 
		$string .= "</div>" . "\n";
		$string .= "</div>" . "\n"; 
	}
	$string .= "</div>" . "\n"; 
	echo $string;	
}
// End of the loop.
endwhile;
?>
</main><!-- .site-main -->
 
<?php get_sidebar( 'content-bottom' ); ?>
 
</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
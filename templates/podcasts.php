<?php
/* 
 * Template Name: Podcasts
 * Template for retrieving podcasts
 * Author : Matthew Mola
 * Date: 10/31/2020
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
	// --- get all the required info ---
	get_template_part( 'template-parts/content', 'page' );	

	// --- Podcast Search Query ---
	$args = array(
		'post_type'  => 'show',
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'     => 'show_active',
				'value'   => 'on',
			),
			array(
				'key'     => 'podcast',
				'value'   => 'true',
			),
		),		
		'orderby'    => 'title',
		'order'      => 'ASC',
	);

	// --- get all the required info ---
	$podcasts = get_posts( $args );

	// --- filter show avatar size ---
	$avatar_size = apply_filters( 'radio_station_schedule_show_avatar_size', 'medium' );

	// --- Create Podcast Loop ---
	foreach ( $podcasts as $podcast ) 
	{		
		// --- get all the required info ---
		$podcast_id = $podcast->ID;	
		$title = get_the_title( $podcast_id );
		$podcast_avatar = radio_station_get_show_avatar( $podcast_id, $avatar_size, ["class" => "img-fluid"] );
		$podcast_link = get_permalink( $podcast_id );
		$host_array = get_post_meta( $podcast_id, 'show_user_list', true );	
		//var_dump( $show['name'] );
		//var_dump( $show );	
		//$title = $show->post_title;
		//$title = $titles[$i]['post_title'];
		//var_dump($title);
		
		//High Resolution Avatar Fix
		if(stripos($show_avatar, "\"1\" height=\"1\"") !== false)
		{
			$show_avatar = '<img width="500" height="500" src="' . radio_station_get_show_avatar_url( $show_id ) . '" class="img-fluid" alt=""/>';
		}	
		
		// --- Get Title ---
		if ( $podcast_link ) {
			$podcast_title = '<a href="' . esc_url( $podcast_link ) . '">' . esc_html( $title ) . '</a>' . "\n";
		} else {
			$podcast_title = esc_html( $title );
		}

		// --- Get Podcast Image ---
		if ( ! $podcast_avatar ) {
			$rdp_logo = wp_get_attachment_image_src( '300', $avatar_size )[0];
			$podcast_avatar = '<img class="img-fluid" alt="Radio DePaul Logo" src="'. $rdp_logo .'">' . "\n";
		}
		
		// --- Get Podcast Hosts ---
		if ( $host_array ) {

			$hosts = '';
			if ( $host_array && is_array( $host_array ) && ( count( $host_array ) > 0 ) ) {

				$count = 0;
				$host_count = count( $host_array );

				foreach ( $host_array as $host ) {
					$count ++;
					$user_info = get_userdata( $host );
					$hosts .= $user_info->display_name;
					

					if ( ( ( 1 === $count ) && ( 2 === $host_count ) )
						 || ( ( $host_count > 2 ) && ( ( $host_count - 1 ) === $count ) ) ) {
						$hosts .= esc_html( __( ', ', 'radio-station' ) ) . ' ';
					} elseif ( ( $count < $host_count ) && ( $host_count > 2 ) ) {
						$hosts .= ', ';
					}
				}		
				
			}
		}

		// --- Classes ---
		$classes = array( 'show-row', 'd-flex' );
		
		// --- Show Row ---
		$classlist = implode( ' ' , $classes );
		$panels .= "\n\n";
		$panels .= '<div class="' . esc_attr( $classlist ) . '">' . "\n";

		// --- Show Image Box ---
		$panels .= '<div class="show-image col-md-2 col-4 align-self-center ml-1 mr-1">' . "\n";
		if ( $podcast_link ) {
			$panels .= '<a href="' . esc_url( $podcast_link ) . '">' . $podcast_avatar . '</a>' . "\n";
		} else {
			$panels .= $podcast_avatar;
		}
		$panels .= '</div>' . "\n";
		
		// --- Show Information Box ---
		$panels .= '<div class="show-info col-md-10 col-8 align-self-center">' . "\n";

		// --- Show Title ---
		$panels .= '<div class="show-title">' . "\n";
		$panels .= $podcast_title;
		$panels .= '</div>' . "\n";
		
		// --- Show Host ---
		if ( $hosts ) {
			$panels .= '<div class="show-host">';
			$panels .= $hosts;
			$panels .= '</div>' . "\n";
		}
		
		// --- Show Information Box Close ---
		$panels .= '</div>' . "\n";	
		
		// --- Show Row Close ---
		$panels .= '</div>' . "\n";			
	}
	// --- End of Podcast Loop ---
	$output .= $panels;
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
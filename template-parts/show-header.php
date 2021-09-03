<?php
/* 
 * Template Name: Show Header
 * Template for displaying show header (title, avatar, featured image) in header.php
 * Author : Matthew Mola
 * Date: 11/16/2020
*/ 

// --- Show checks ---
$post_id = $wp_query->post->ID;
$avatar_id = get_post_meta( $post_id, 'show_avatar', true );
$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
$avatar_default = '300'; //Radio DePaul Logo
//$thumbnail_default = '320'; //Radio DePaul Background
$thumbnail_default = '484'; //Radio DePaul Background
?>


<div class="container-fluid">
	<div class="row">
	
	<?php	
	// --- Show Thumbnail ---
	if ( ! $thumbnail_id ) {
		$thumbnail_id = $thumbnail_default;		
	}
	$size = apply_filters( 'radio_station_show_header_size', 'full', $post_id );
	$thumbnail_src = wp_get_attachment_image_src( $thumbnail_id, $size );
	$thumbnail_url = $thumbnail_src[0];
	?>
	<div class="show_header" style="background-image: linear-gradient( rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5) ), url('<?php echo esc_url($thumbnail_url); ?>');">
	
	<div class="show_title">
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-title -->
	</div>
	
	<div class="show_avatar">
	<?php
	// --- Show Avatar ---
	if ( !  $avatar_id ) {
		$avatar_id = $avatar_default;		
	}
	$size = apply_filters( 'radio_station_show_avatar_size', 'medium', $post_id, 'show-page' );
	$avatar_src = wp_get_attachment_image_src( $avatar_id, $size );
	$avatar_url = $avatar_src[0];
	/* <img src="https://place-hold.it/2000x400/?text=2000x400&fontsize=33" alt="placeholder 960 "/> */
	?>
	<div class="mx-auto d-block text-center">
	<img class="avatar-image img-fluid mx-auto d-block" alt="<?php echo the_title() . " Show Avatar"; ?>" src="<?php echo esc_url($avatar_url); ?>">
	</div>
	<br>
	</div><!-- .show_avatar -->
	</div><!-- .show_header -->
	
	</div><!-- .row -->
</div><!-- .container-fluid -->

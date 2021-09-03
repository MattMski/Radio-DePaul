<?php
/* 
 * Template Name: News 
 * Template for retrieving posts
 * Author : Matthew Mola
 * Date: 3/7/2021
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
	$category_data = get_categories();	
	array_unshift($category_data, 'All');
	array_push($category_data, 'Newsletter');
	
	// --- Newsletter Stuff ---
	$rss = 'https://us4.campaign-archive.com/feed?u=0ab60cabbfd37d80f6ca0e064&id=fc1ba3af76';
	$rss = simplexml_load_file($rss, null, LIBXML_NOCDATA);	

	//Create Custom Array to handle overflow
	$newsletter = array();
	foreach($rss->channel->item as $item)
	{
		$keyval = array('category' => 'Newsletter', 'post_title' => trim($item->title), 'post_link' => trim($item->link), 'post_date' => trim($item->pubDate) );	
		array_push($newsletter,$keyval);
	}
	
	//Convert Newsletter to object
	$jnewsletter = json_decode(json_encode($newsletter), FALSE);
	
	//Merge All Posts and Newsletter as Objects
	$args = array(
	'posts_per_page'   => -1,
	'orderby'          => 'date',
	'order'            => 'DESC',
	);
	$allposts = get_posts($args);
	$merged = array_merge($allposts,$jnewsletter);
	array_multisort(array_map('strtotime',array_column($merged,'post_date')), SORT_DESC, $merged);
	
	// --- Create Category Posts Loop ---
	foreach ( $category_data as $category ) 
	{
		// Change variables for All Posts
		if($category == 'All')
		{
			$ctype = $category;
			$category_list[] = $category;
			//$category_posts = $allposts;
			$category_posts = $merged;
		}
		else if($category == 'Newsletter')
		{			
			$ctype = $category;
			$category_list[] = $category;
			$category_posts = $newsletter;
		}
		else
		{
			$ctype = $category->slug;
			$category_list[] = $category->name;
			$args = array(
			'posts_per_page'   => -1,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'category' 		   => $category->term_id,
			);
			$category_posts = get_posts($args);
		}
	
		foreach ($category_posts as $i => $post) 
		{			
			date_default_timezone_set('America/Chicago');
			//If Newsletter Posts, run specific things
			if($category == 'Newsletter')
			{
				$category_title = $category;
				$post_title = $post['post_title'];	
				$date = $post['post_date'];
				$date = date("M j, Y g:i A", strtotime($date));
				$post_preview = $date;	
				$post_link = $post['post_link'];				
				$post_image = wp_get_attachment_image_src( '648', 'medium' )[0];
				$post_image = '<img class="img-fluid" alt="Radio DePaul Newsletter Logo" src="'. $post_image .'">' . "\n";
			}
			//If All Posts, run specific things
			elseif($category == 'All')
			{
				//Check if it's a Newsletter post by checking if custom category is set
				if(isset($post->category))
				{
					$category_title = $post->category;
					$post_title = $post->post_title;	
					$date = $post->post_date;
					$date = date("M j, Y g:i A", strtotime($date));
					$post_preview = $date;
					$post_link = $post->post_link;
					$post_image = wp_get_attachment_image_src( '648', 'medium' )[0];
					$post_image = '<img class="img-fluid" alt="Radio DePaul Newsletter Logo" src="'. $post_image .'">' . "\n";
				}
				//If false, it's a Wordpress post
				else
				{
					$post_ID = $post->ID;
					$post_title = $post->post_title;
					$post_content = $post->post_content;
					$date = $post->post_date;
					$date = date("M j, Y g:i A", strtotime($date));
					$dom = new DOMDocument();
					$dom->loadHTML(mb_convert_encoding($post_content, 'HTML-ENTITIES', 'UTF-8'));
					$post_preview =  $date . '<br>' . $dom->getElementsByTagName('p')[0]->textContent;
					//var_dump($post_preview);
					//if(strpos($post_preview, ".") !== false) { $post_preview = substr($post_preview, 0, strpos($post_preview, ".")); }
					$post_image = get_the_post_thumbnail($post_ID , 'medium', ["class" => "img-fluid"] );
					$post_link = get_permalink($post_ID);
					
					$multi_categories = get_the_category($post_ID);
					$count = 0;
					$cat_count = count ($multi_categories);
					
					$category_title = '';
					foreach($multi_categories as $mcategory)
					{
						$count ++;
						$category_title .= $mcategory->name;

						if ( ( ( 1 === $count ) && ( 2 === $cat_count ) )
							 || ( ( $cat_count > 2 ) && ( ( $cat_count - 1 ) === $count ) ) ) {
							$category_title .= esc_html( __( ', ' ) ) . ' ';
						} elseif ( ( $count < $cat_count ) && ( $cat_count > 2 ) ) {
							$category_title .= ', ';
						}
					}
				}
			}
			//Then it's just a regular category of posts
			else
			{
				$category_title = $category->name;
				$post_ID = $post->ID;
				$post_title = $post->post_title;
				$post_content = $post->post_content;
				$date = $post->post_date;
				$date = date("M j, Y g:i A", strtotime($date));
				$dom = new DOMDocument();
				$dom->loadHTML(mb_convert_encoding($post_content, 'HTML-ENTITIES', 'UTF-8'));
				$post_preview =  $date . '<br>' . $dom->getElementsByTagName('p')[0]->textContent;
				//var_dump($post_preview);
				//if(strpos($post_preview, ".") !== false) { $post_preview = substr($post_preview, 0, strpos($post_preview, ".")); }
				$post_image = get_the_post_thumbnail($post_ID , 'medium', ["class" => "img-fluid"] );
				$post_link = get_permalink($post_ID);
			}	

			// --- Create Category Boxes ---	
			$cid = strtolower( $ctype ) . "-posts";
			$classes = array( 'collapse'  );
			if($category == 'All') { $classes[] = 'show'; }
			$classlist = implode( ' ' , $classes );
			if ( 0 == $i ) {
				$panels .= "\n";
				$panels .= '<div class="' . esc_attr( $classlist ) . '" id="' . $cid . '" data-parent="#panels">';
			}		
		
			// --- Show Row ---
			$classes = array( 'post-row', 'row', $ctype);
			//if($category != 'All') { $classes[] = 'd-flex'; }
			$classlist = implode( ' ' , $classes );
			$panels .= "\n\n";
			$panels .= '<div class="' . esc_attr( $classlist ) . '">' . "\n";

			
			// --- Get Show Image ---
			if ( ! $post_image ) {
				$rdp_logo = wp_get_attachment_image_src( '206', 'medium' )[0];				
				$post_image = '<img class="img-fluid" alt="Radio DePaul News Logo" src="'. $rdp_logo .'">' . "\n";
			}

			// --- Show Image Box ---
			$panels .= '<div class="show-image w-75 col-md-4 col-12">' . "\n";
			$panels .= $post_image;
			$panels .= '</div>' . "\n";	

			// --- Show Information ---
			$panels .= '<div class="show-info col-md-8 col-12 align-self-center">' . "\n";
			
			// --- Category Title ---
			$panels .= '<div class="category-title text-uppercase">' . "\n";
			$panels .= $category_title;
			$panels .= '</div>' . "\n";

			// --- Get Title ---
			$panels .= '<div class="post-title">' . "\n";
			$panels .= $post_title;
			$panels .= '</div>' . "\n";	
			
			// --- Get Preview ---
			$panels .= '<div class="post-preview">' . "\n";
			$panels .= $post_preview;
			$panels .= '</div>' . "\n";
			
			// --- Read More ---
			$panels .= '<div class="read-more">' . "\n";
			$panels .= '<a href="' . esc_url( $post_link ) . '">Read More</a>' . "\n";
			$panels .= '</div>' . "\n";
			
			// --- Close Category Box and Post Row ---
			$panels .= '</div>' . "\n";
			$panels .= '</div>' . "\n";	
			
			// --- End of Category Post ---
			if ( !next ( $category_posts ) )
			{	
				// --- Add Load More Button ---
				$loadmore = strtolower( $ctype ) . "-load-more";
				$classes = array( 'btn' , 'btn-outline-secondary' , 'btn-sm' , 'active' , $loadmore );
				$classlist = implode( ' ' , $classes );
				$panels .= '<button class="' . esc_attr( $classlist ) . '">Load More</button>';
				
				// --- Category Boxes Close ---
				$panels .= '</div>' . "\n";
			}
		}		
	}
	// --- End of Category Posts Loop --

	// Create Dropdown Loop
	foreach ($category_list as $ct => $category_button ) 
	{
		$classes = array( 'dropdown-item' );
		$aria_expanded = 'false';
		$category_title = $category_button;
		if ( $category_button == "All" ) 
		{
			$classes[] = 'active';
			$aria_expanded = 'true';
		}
		else
		{
			$classes[] = '';
			$aria_expanded = 'false';
		}
		$cid = strtolower( $category_button ) . "-posts";
		$classlist  = implode( ' ', $classes );

		// --- Dropdown ---	
		if( 0 == $ct) {
			$dropdown_output = '<div class="dropdown">' ."\n";
			$dropdown_output .= '<button class="btn btn-rdp dropdown-toggle" type="button" id="dropdownCategory" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' ."\n";
			$dropdown_output .= 'Category' ."\n";
			$dropdown_output .= '</button>' ."\n";
			$dropdown_output .= '<div class="dropdown-menu" aria-labelledby="dropdownCategory">' ."\n";
		}

		$dropdown_output .= '<a class="' . esc_attr( $classlist ) . '" data-toggle="collapse" href="#' . strtolower( $cid ) . '" data-target="#' . strtolower( $cid ) . '" aria-expanded="' . $aria_expanded . '" aria-controls="' . strtolower( $cid ) . '"';
		$dropdown_output .= '>' . esc_html( $category_title ) . '</a>' . "\n";
		
		if ( $category_button == end($category_list) )
		{
			$dropdown_output .= '</div></div><br>' . "\n";
		}
	}
	// --- End of Dropdown Loop --
	
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
<?php
/*
Plugin Name: Different Number of Posts on First Page
Description: Simple plugin that makes a differet number of posts on the first page only
Version: 0.0.0
Author: Hoday
Text Domain: hoday
License: GPLv2 or later
*/

$posts_per_page_first_page = 3;	



function pre_get_posts_callback_offset_posts_per_page( $query ) {
	global $posts_per_page_first_page;	
	$posts_per_page = get_option('posts_per_page');

  if ($query->is_home() && $query->is_main_query() && !is_admin()) {
    if (!$query->is_paged()) {
			// for first page
      $query->set('posts_per_page', $posts_per_page_first_page);
    } else {
			// for subsequent pages
      $offset = $posts_per_page_first_page + ( ($query->query_vars['paged']-2) * $posts_per_page );
      $query->set('offset', $offset);
			$query->set('posts_per_page', $posts_per_page);
    }
		//print_r($query);
  }
}
add_action('pre_get_posts','pre_get_posts_callback_offset_posts_per_page');

function found_posts_callback_homepage_offset_pagination( $found_posts, $query ) {
	global $posts_per_page_first_page;	
	$posts_per_page = get_option('posts_per_page');

	//echo "found_posts: ".$found_posts.".";
	
	if( $query->is_home() && $query->is_main_query() ) {
		if (!$query->is_paged()) {
			// for first page
			if ($found_posts <= $posts_per_page_first_page) {
				// should be only one page
				//$found_posts = $found_posts;
			} else {
				$number_of_pages = ceil(($found_posts - $posts_per_page_first_page) / $posts_per_page) + 1;
				$found_posts = $number_of_pages * $posts_per_page_first_page;
			}
		} else {
			// for subsequent pages
			$number_of_pages = ceil(($found_posts - $posts_per_page_first_page) / $posts_per_page) + 1;
			$found_posts = $number_of_pages * $posts_per_page;
		}
	}
	//echo "found_posts: ".$found_posts.".";

	return $found_posts;
}
add_filter( 'found_posts', 'found_posts_callback_homepage_offset_pagination', 10, 2 );
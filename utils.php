<?php

// Generates static pages for a post types archive
function brg_ss_save_archive ($post_type) {
	// save the archive
	$archive_url = get_post_type_archive_link($post_type);
	
	// bail if the post type has no archive or no longer exists
	if ($archive_url == false) {
		error_log('have no archive for ' . $post_type . ', skipping...');
		return;
	}

	// get the number of pages to save
	$max_posts = wp_count_posts($post_type);
	$number_pages = ceil($max_posts->publish / get_option('posts_per_page'));

	// loop over each page and save it
	for ($page_number = 1; $page_number <= $number_pages; $page_number++) {
		$base_url = $archive_url;
		if ($page_number > 1) {
			$base_url .= 'page/' . $page_number;
		}
		$archive_cleaned_permalink = str_replace(get_site_url() . '/', '', $base_url);
		brg_ss_remove_htaccess_rule($archive_cleaned_permalink);
		
		$contents = file_get_contents($base_url);
		$contents = preg_replace('/\s+/', ' ', $contents);
		$filename = 'archive-html-file-' . $post_type . '-' . $page_number . '.html';
		
		if ($contents && $contents !== '') {
			$full_path = brg_ss_save_page_contents($contents, $filename);
			brg_ss_update_htaccess_files($archive_cleaned_permalink, $filename);
		}
	}
}

// Generates and saves the static page for a single post by ID
function brg_ss_save_single_post ($post_ID) {
	// bail if post isn't published
	$post = get_post($post_ID);
	if ($post->post_status != 'publish') {
		return;
	}

	// save the post itself
	$post_permalink = get_permalink($post_ID);
	$cleaned_permalink = str_replace(get_site_url() . '/', '', $post_permalink);

	brg_ss_remove_htaccess_rule($cleaned_permalink);
	
	$contents = file_get_contents($post_permalink);
	$contents = preg_replace('/\s+/', ' ', $contents);
	$filename = 'post-html-file-' . $post_ID . '.html';

	if ($contents && $contents !== '') {
		$full_path = brg_ss_save_page_contents($contents, $filename);
		brg_ss_update_htaccess_files($cleaned_permalink, $filename);
	}
}

// Loops through all posts of a type and generates a static page for each
function brg_ss_save_all_posts ($post_type) {
	$all_posts = get_posts(array(
		'post_type' => $post_type,
		'posts_per_page' => -1
	));
	foreach ($all_posts as $post) {
		brg_ss_save_single_post($post->ID);
	}
}
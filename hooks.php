<?php

add_action('save_post', function ( $post_ID ) {
	$post_type = get_post_type($post_ID);
	// skip for revisions
	if ($post_type == 'revision') {
		return;
	}

	// save the archive
	$archive_url = get_post_type_archive_link($post_type);
	$max_posts = wp_count_posts($post_type);
	$number_pages = ceil($max_posts->publish / get_option('posts_per_page'));
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
});

add_action('activated_plugin', function ($plugin) {
	if (str_contains($plugin, 'static-genny')) {
		$slashed_home = trailingslashit(get_option('home'));
		$base = parse_url($slashed_home, PHP_URL_PATH);

		$uploads_dir = wp_upload_dir()['basedir'];
		$path_url = explode('wp-content', $uploads_dir)[0] . '.htaccess';
		$in_brg_plugin = false;

		$contents_full = file_get_contents($path_url);
		$new_contents = <<<HTML
# BEGIN BRG SS
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase $base
</IfModule>
# END BRG SS

$contents_full
HTML;
		file_put_contents($path_url, $new_contents);
	}
});

add_action('deactivated_plugin', function ($plugin) {
	if (str_contains($plugin, 'static-genny')) {
		$uploads_dir = wp_upload_dir()['basedir'];
		$path_url = explode('wp-content', $uploads_dir)[0] . '.htaccess';
		$in_brg_plugin = false;

		$contents_full = file_get_contents($path_url);
		$contents = explode("\n", $contents_full);
		$new_contents = array_filter($contents, function ($line) use (&$in_brg_plugin) {
			$final_line = false;
			if (str_contains($line, '# BEGIN BRG SS')) {
				$in_brg_plugin = true;
			} else if (str_contains($line, '# END BRG SS')) {
				$final_line = true;
				$in_brg_plugin = false;
			}

			return !$in_brg_plugin && !$final_line;
		});

		file_put_contents($path_url, implode("\n", $new_contents));
	}
});
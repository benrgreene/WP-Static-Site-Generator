<?php

function brg_ss_update_htaccess_files ($cleaned_permalink, $filename) {
	$uploads_dir = wp_upload_dir()['basedir'];
	$path_url = explode('wp-content', $uploads_dir)[0] . '.htaccess';

	$slashed_home = trailingslashit(get_option('home'));
	$base = parse_url($slashed_home, PHP_URL_PATH);

	$contents_full = file_get_contents($path_url);
	$contents = explode("\n", $contents_full);
	
	$in_brg_config = false;
	$after_base_set = false;
	$saved_file_update = false;
	foreach ($contents as $line_index => $line) {
		if (str_contains($line, '# BEGIN BRG SS')) {
			$in_brg_config = true;
		}
		if (str_contains($line, 'RewriteBase') && $in_brg_config == true) {
			$after_base_set = true;
		}
		if (str_contains($line, '# END BRG SS')) {
			$in_brg_config = false;
		}
		if ($in_brg_config == true && $after_base_set == true && $saved_file_update == false) {
			$condition = '';
			if ($cleaned_permalink == '' || !$cleaned_permalink) {
				$condition = "\nRewriteCond %{QUERY_STRING} !s";
			}
			$contents[$line_index] = $line . $condition . "\nRewriteRule ^" . $cleaned_permalink . "$ " . $base . "wp-content/uploads/static-pages/" . $filename . " [L]";
			$saved_file_update = true;
		}
	}

	file_put_contents($path_url, implode("\n", $contents));
}

function brg_ss_remove_htaccess_rule ($cleaned_permalink) {
	// handle if the permalink is for the front page
	if ($cleaned_permalink == '') {
		$cleaned_permalink = '^$';
	}

	$uploads_dir = wp_upload_dir()['basedir'];
	$path_url = explode('wp-content', $uploads_dir)[0] . '.htaccess';

	$contents_full = file_get_contents($path_url);
	$contents = explode("\n", $contents_full);
	$permalink_to_check = $cleaned_permalink . '$';
	$new_contents = array_filter($contents, function ($line) use($permalink_to_check) {
		return !str_contains($line, $permalink_to_check);
	});

	file_put_contents($path_url, implode("\n", $new_contents));
}
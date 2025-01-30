<?php

// On customizer save, need to clear out old saved pages
add_action('customize_save_after', function () {
	brg_ss_deactivate_plugin('static-genny');
	brg_ss_activate_plugin('static-genny');

	// resave all posts as static pages
	$static_post_types = get_option('static-post-types', array());
	foreach ($static_post_types as $post_type) {
		brg_ss_save_archive($post_type);
		brg_ss_save_all_posts($post_type);
	}
});

// On post save, save updated version of the page & the archive of its post type
add_action('save_post', function ($post_ID) {
	$post_type = get_post_type($post_ID);
	
	// bail if post isn't published
	$post = get_post($post_ID);
	if ($post->post_status != 'publish') {
		return;
	}

	// skip for revisions
	if ($post_type == 'revision' || $post_type == 'customize_changeset') {
		return;
	}

	brg_ss_save_archive($post_type);
	brg_ss_save_single_post($post_ID);
});

// On plugin activation, add base htaccess contents
add_action('activated_plugin', 'brg_ss_activate_plugin');
function brg_ss_activate_plugin ($plugin) {
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
}

// on plugin deactivication, remove all static page rules
add_action('deactivated_plugin', 'brg_ss_deactivate_plugin');
function brg_ss_deactivate_plugin ($plugin) {
	if (str_contains($plugin, 'static-genny')) {
		$uploads_dir = wp_upload_dir()['basedir'];
		$path_url = explode('wp-content', $uploads_dir)[0] . '.htaccess';
		$in_brg_plugin = false;

		$contents_full = file_get_contents($path_url);
		$contents = explode("\n", $contents_full);
		// just need to delete all content between static site comment rules
		$new_contents = array_filter($contents, function ($line) use (&$in_brg_plugin) {
			if (str_contains($line, '# BEGIN BRG SS')) {
				$in_brg_plugin = true;
			} else if (str_contains($line, '# END BRG SS')) {
				$in_brg_plugin = false;
				return false;
			}

			return !$in_brg_plugin;
		});

		file_put_contents($path_url, implode("\n", $new_contents));
	}
}
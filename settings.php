<?php

add_action('admin_menu' , function () {
	add_submenu_page(
		'options-general.php',
		'Static Generator',
		'Static Generator',
		'administrator',
		'static-settings-page',
		'brg_ss_setup_settings_page'
	);
});

// Register 
add_action('admin_init', function () {
	register_setting('brg-ss-settings-fields', 'static-post-types');
});


function brg_ss_setup_settings_page () {
	include 'templates/settings-page.php';
}

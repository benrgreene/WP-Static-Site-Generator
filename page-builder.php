<?php

function brg_ss_save_page_contents ($contents, $filename) {
	add_filter('upload_dir', 'brg_ss_set_page_uploads_path');
	wp_delete_file(wp_upload_dir()['basedir'] . '/static-pages/' . $filename);
	$response = wp_upload_bits($filename, null, $contents);
	remove_filter('upload_dir', 'brg_ss_set_page_uploads_path');
	return wp_upload_dir()['basedir'] . '/static-pages/' . $filename;
}

function brg_ss_set_page_uploads_path ($arr) {
	$_filter_upload_dir = '/static-pages';	
	$arr['path'] = $arr['basedir'] . $_filter_upload_dir;
	$arr['url'] = $arr['baseurl'] . $_filter_upload_dir;
	$arr['subdir'] = $_filter_upload_dir;
	return $arr;
}
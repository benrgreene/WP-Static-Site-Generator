<?php
	$selected_post_types = get_option('static-post-types', array());
	$post_types = get_post_types(array(
		'public' => true
	));
?>

<div class="wrap">

	<h1>Static Site Generation Settings</h1>

	<form method="post" action="options.php"> 
		<?php settings_fields('brg-ss-settings-fields'); ?>
		<?php do_settings_sections('brg-ss-settings-fields'); ?>

		<div class="options-table">
			<div class="option">
				<h2>Post Types</h2>
				<p>Select the post types to save as static pages</p>
				<ul>
					<?php foreach ($post_types as $type) { ?>
						<li>
							<input id="type-<?= $type ?>" 
								   type="checkbox" 
								   name="static-post-types[]" 
								   value="<?= $type ?>" 
								   <?= in_array($type, $selected_post_types) ? 'checked' : '' ?>/>
							<label for="type-<?= $type ?>"><?= $type ?></label>
						</li>
					<?php } ?>
				</ul>
				
			</div>
		</div>

		<?php submit_button(); ?>
	</form>

</div>
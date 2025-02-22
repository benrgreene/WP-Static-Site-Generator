# WP Static Site Generator

This plugin was made to help turn WordPress frontends into static sites, by way of building HTML pages and then serving them instead of rendering through WordPress each page visit.

The basic premise is that on posts being saved, the plugin will fetch an updated version of the page and ssave it to a folder in the wp-content directory, and add a rule to the .htaccess file. When that pages URL is requested, the HTML file in the uploads directory is served instead of routing through WordPress and rendering the page on the server.

On the customizer being saved, the plugin will clean all rules and rebuild all pages to ensure that any options in the customizer take effect for all page & posts, and not just ones updated in the future.

To set which post types become static, You can go to "Settings" -> "Static Generator"

## Search

This plugin can conflict with the search page, which usually is routed through the homepage. To resolve, it loads the theme search template for any URL that includes the search parameter. 

It is recommended that for custom themes, you send search forms to a URL that isn't the same as your homepage (or any existing page), else the page might be cached and loaded statically.

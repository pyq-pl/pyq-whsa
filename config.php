<?php
	
	/*PLUGIN - NAZWA*/
	define('PYQ_PLUGIN_NAME','pyq-whsa');

	// Define path and URL to the ACF plugin.
	$plugin_dir = ABSPATH . 'wp-content/plugins/'.PYQ_PLUGIN_NAME;
	$page_url = get_site_url() . '/wp-content/plugins/'.PYQ_PLUGIN_NAME;
	$wordpress_page_url = get_site_url(null, '/wp-content/themes/', 'https');

	define( 'MY_ACF_PATH', $plugin_dir . '/includes/acf/' );
	define( 'MY_ACF_URL', $page_url . '/includes/acf/' );

?>
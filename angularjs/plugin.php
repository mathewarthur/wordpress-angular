<?php
/**
 * Plugin Name: AngularJS
 * Plugin URI: http://mathewarthur.com
 * Description: Load WordPress content client-side using AngularJS directives and WP REST API V2.
 * Version: 1.0
 * Author: Mathew Arthur
 * Author URI: http://mathewarthur.com
 * License: GPL2
 */
require_once('includes/metaBox.php');
require_once('includes/contentFilter.php');
require_once('includes/shortcodes.php');
register_theme_directory( dirname( __FILE__ ) . '/theme' );
define('WordPressAngularJS', '2.0');
class WordPressAngularJS {
	function __init(){
		global $wpdb;		
		add_action( 'wp_enqueue_scripts', array( $this, 'angularScripts' ) );
		add_filter( 'rest_api_init', array( $this, 'post_add_tax_register' ), 10, 3 );
	}
	function angularScripts() {
		wp_enqueue_script('angular-core', plugin_dir_url( __FILE__ ).'js/angular.min.js', array('jquery'), null, false);
		wp_enqueue_script('angular-sanitize', plugin_dir_url( __FILE__ ).'js/angular-sanitize.min.js', array('jquery'), null, false);
		wp_enqueue_script('html-janitor', plugin_dir_url( __FILE__ ).'js/html-janitor.js', array('jquery'), null, false);
		wp_enqueue_script('angular-app', plugin_dir_url( __FILE__ ).'js/angular-app.php', array('html-janitor'), null, false);
		wp_enqueue_script('angular-factories', plugin_dir_url( __FILE__ ).'js/angular-factories.js', array('angular-app'), null, false);
		wp_enqueue_script('angular-post-directives', plugin_dir_url( __FILE__ ).'js/angular-posts-directives.php', array('angular-factories'), null, false);
		wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js', null, null, false);
		wp_enqueue_script('angular-fitvids', plugin_dir_url( __FILE__ ).'js/angular-fitvids.js', null, null, false);
		wp_enqueue_script('angular-compile', plugin_dir_url( __FILE__ ).'js/angular-compile.js', null, null, false);
		wp_enqueue_script('angular-vertilize', plugin_dir_url( __FILE__ ).'js/angular-vertilize.min.js', null, null, false);
		$template_directory = array();
		foreach (new DirectoryIterator(dirname( __FILE__ )."/theme/partials") as $fileInfo) {
  		  if($fileInfo->isDot()) continue;
    		$template_type = explode('-', $fileInfo->getFilename());
    		$template_var = str_replace(".html","",$template_type[0]."_".$template_type[1]);
    		$template_directory[$template_var] = plugin_dir_url( __FILE__ ).'theme/partials/'.$fileInfo->getFilename();
    	}
		$angularjs_for_wp_localize = array( 
			'site' => get_bloginfo('wpurl'), 
			'nonce' => wp_create_nonce( 'wp_rest' ), 
			'template_directory' => $template_directory 
		);
		if( function_exists( 'json_url' ) ) {
			$angularjs_for_wp_localize['base'] = json_url();
		}
		if( function_exists( 'rest_get_url_prefix' ) ) {
			$angularjs_for_wp_localize['base'] = get_bloginfo( 'wpurl') . '/' . rest_get_url_prefix() . '/wp/v2';
		}
		wp_localize_script( 
			'angular-core', 
			'wpAngularVars', 
			$angularjs_for_wp_localize 
		);
	}
	function post_add_tax_register() {
		$post_types = get_post_types( array( 'public' => true, 'exclude_from_search' => false ), 'names' );
		foreach( $post_types as $cpt ) {
			if( $cpt === 'attachment' ) { continue; }
			register_api_field( $cpt,
				'post_taxonomies',
				array(
					'update_callback' => array( $this, 'post_add_tax' ),
					'schema' 		  => null,
				)
			);
		}
	}
	function post_add_tax( $value, $object, $field_name ) {
		//var_dump( $value );
		foreach( $value as $term => $tax ){
			wp_set_post_terms( $object->ID, array( intval( $term ) ), $tax, true );
	    }
	}
}
function angularjs_plugin_dep() {
    if ( ! defined( 'REST_API_VERSION' ) ) {
        add_action( 'admin_notices', 'angular_wpapi_error' );
    }
}
function angular_wpapi_error(){
	echo '<div class="error"><p><strong>JSON REST API</strong> must be installed and activated for the <strong>AngularJS for WP</strong> plugin to work properly - <a href="https://wordpress.org/plugins/rest-api/" target="_blank">Install Plugin</a></p></div>';
}
add_action( 'admin_init', 'angularjs_plugin_dep', 99 );
$wpNG = new WordPressAngularJS();
add_action( 'init', array( $wpNG, '__init' ), 1000 );
?>
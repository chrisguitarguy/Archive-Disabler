<?php
/*
Plugin Name: Archive Disabler
Plugin URI: https://github.com/chrisguitarguy/Archive-Disabler
Description: Disable custom post type, taxonomy, date, author and other archives on your WordPress blog.
Version: 1.0
Author: Christopher Davis
Author URI: http://christopherdavis.me
Text Domain: cd-archive-disabler
Domain Path: /lang
License: GPL2
*/

define( 'CD_AD_PATH', plugin_dir_path( __FILE__ ) );
define( 'CD_AD_NAME', plugin_basename( __FILE__ ) );

if( is_admin() )
{
    require_once( CD_AD_PATH . 'inc/options.php' );
}
else
{
    require_once( CD_AD_PATH . 'inc/front.php' );
}

register_activation_hook( __FILE__, 'cd_ad_activation' );
/**
 * Activation hook
 * 
 * @since 1.0
 * @uses add_option Inserts the `cd_ad_options` option into the database
 */
function cd_ad_activation()
{
    add_option( 'cd_ad_options', array() );
}

register_deactivation_hook( __FILE__, 'cd_ad_deactivation' );
/**
 * Deactivation hook
 * 
 * @since 1.0
 * @uses delete_option Removes the `cd_ad_options` option from the data base
 */
function cd_ad_deactivation()
{
    delete_option( 'cd_ad_options' );
}


// testing code so we have a post type archive to work with
add_action( 'init', 'cdad_tester' );
function cdad_tester()
{
    register_post_type(
        'blah',
        array( 'public' => true, 'has_archive' => true, 'label' => 'blah', 'labels' => array( 'singular_name' => 'one blah' ) )
    );
}

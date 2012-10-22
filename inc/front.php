<?php
add_action( 'template_redirect', 'cd_ad_catch_disabled' );
/**
 * Catches whether on not a given archive is disabled
 * 
 * @since 1.0
 * @uses is_* To find which archive we're on
 */
function cd_ad_catch_disabled()
{
    $opts = get_option( 'cd_ad_options', array() );
    $bail = false;
    if( is_date() )
    {
        if( isset( $opts['date'] ) && 'on' == $opts['date'] ) 
            $bail = true;
    }
    elseif( is_author() )
    {
        if( isset( $opts['author'] ) && 'on' == $opts['author'] )
            $bail = true;
    }
    elseif( function_exists( 'is_post_type_archive' ) && is_post_type_archive() )
    {
        $obj = get_queried_object();
        $tmp = 'posttype_' . $obj->name;
        if( isset( $opts[$tmp] ) && 'on' == $opts[$tmp] )
            $bail = true;
    }
    elseif( is_tax() || is_category() || is_tag() )
    {
        $obj = get_queried_object();
        $tmp = 'taxonomy_' . $obj->taxonomy;
        if( isset( $opts[$tmp] ) && 'on' == $opts[$tmp] )
            $bail = true;
    }
    
    if( $bail )
    {
        $action = isset( $opts['on_catch'] ) ? $opts['on_catch'] : 'redirect';
        if( 'redirect' == $action )
        {
            wp_redirect(
                apply_filters( 'cd_ad_redirect', home_url() ),
                301
            );
            exit();
        }
        else
        {
            global $wp_query;
            $wp_query->set_404();
        }
    }
}

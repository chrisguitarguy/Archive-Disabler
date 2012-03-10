<?php
class CD_AD_Admin_Options
{
    /**
     * The setting we'll be using
     * 
     * @since 1.0
     * @access protected
     */
    protected $setting = 'cd_ad_options';
    
    /**
     * The page on which our setting our settings fields will go
     * 
     * @since 1.0
     * @access proctected
     */
    protected $page = 'reading';
    
    /**
     * The section where our settings fields will live
     * 
     * @since 1.0
     * @access protected
     */
    protected $section = 'cd_ad_options';
    
    /**
     * Prefix for all ID's.
     * 
     * @since 1.0
     * @access protected
     */
    protected $prefix = 'cd_ad_';
    
    /**
     * Constructor
     * 
     * @since 1.0
     * @uses add_action To hook into various WordPress hooks
     */
    function __construct()
    {
        add_action( 'admin_init', array( &$this, 'setting' ) );
    }
    
    /**
     * Registers the `cd_ad_options` option and sets up the settings fields
     * 
     * @since 1.0
     * @uses register_setting To register the setting
     * @uses add_settings_section To add a new section to the `reading` page
     * @uses add_settings_field To add option fields to our new setting section
     */
    function setting()
    {
        register_setting(
            $this->page,
            $this->setting,
            array( &$this, 'clean_setting' )
        );
        
        add_settings_section(
            $this->section,
            __( 'Archive Disabler Options', 'cd-archive-disabler' ),
            array( &$this, 'section_cb' ),
            'reading'
        );
        
        $this->add_field( 'date', __( 'Date Archives', 'cd-archive-disabler' ) );
        $this->add_field( 'author', __( 'Author Archives', 'cd-archive-disabler' ) );
        $this->add_field( 'taxonomies', __( 'Taxonomy Archives', 'cd-archive-disabler' ) );
        if( $this->types() )
        {
            $this->add_field( 'post_types', __( 'Post Type Archives', 'cd-archive-disabler' ) );
        }
        
    }
    
    /**
     * Callback function for our section
     * 
     * @since 1.0
     */
    function section_cb()
    {
        ?>
        <p class="description">
            <?php _e( 'Choose which archives you would like to disable.', 'cd-archive-disabler' ); ?>
        </p>
        <?php
    }
    
    /**
     * Callback for the date option
     * 
     * @since 1.0
     */
    function date_field_cb( $args )
    {
        echo $this->checkbox( 
            'date', 
            $args, 
            __( 'Disable Date Archives', 'cd-archive-disabler' ) 
        );
    }
    
    /**
     * Callback for author archive option
     *
     * @since 1.0
     */
    function author_field_cb( $args )
    {
        echo $this->checkbox(
            'author',
            $args, 
            __( 'Disable Author Archives', 'cd-archive-disabler' )
        );
    }
    
    /**
     * Callback for taxonomy archives option
     * 
     * @since 1.0
     */
    function taxonomies_field_cb()
    {
        echo $this->list_display( $this->taxonomies() );
    }
    
    function post_types_field_cb()
    {
        echo $this->list_display( $this->types() );
    }
    
    /**
     * Settings sanitization callback function
     * 
     * @since 1.0
     * @uses esc_* To sanitize the data
     */
    function clean_setting( $in )
    {
        $out = array();
        foreach( $this->to_check() as $i )
        {
            $out[$i] = isset( $in[$i] ) && $in[$i] ? 'on' : 'off';
        }
        return $out;
    }
    
    /**
     * Utility method to fetch all public post types that have archives
     * 
     * @since 1.0
     * @access protected
     * @uses get_post_types To fetch the post types we want
     * @return array The post type as the key and the label as the value
     */
    protected function types()
    {
        $types = get_post_types( 
            array(
                'public'        => true,
                'has_archive'   => true
            ),
            'object'
        );
        
        $rv = array();
        if( ! empty( $types ) )
        {
            foreach( $types as $t => $obj )
            {
                $rv[$t] = isset( $obj->labels->singular_name ) ? $obj->labels->singular_name : $obj->label;
            }
        }
        return $rv;
    }
    
    /**
     * Utility method to fetch our taxonomies.
     * 
     * @since 1.0
     * @access protected
     * @uses get_taxonomies To fetch a list of all taxonomies
     * @return array With taxonomy slugs as the keys and a label as the value
     */
    function taxonomies()
    {
        $taxes = get_taxonomies(
            array( 'public' => true ),
            'objects'
        );
        
        $rv = array();
        if( ! empty( $taxes ) )
        {
            foreach( $taxes as $t => $obj )
            {
                $rv[$t] = isset( $obj->labels->singular_name ) ? $obj->labels->singular_name : $obj->label;
            }
        }
        
        return $rv;
    }
    
    /**
     * <lazy>Wrapper callback to echo out lists of options
     *
     * @since 1.0
     * @access protected
     * @return string A list of checkboxes
     */
    protected function list_display( $items )
    {
        $opts = get_option( $this->setting, array() );
        $rv = '';
        foreach( $items as $t => $label )
        {
            $rv .= $this->checkbox( 
                $t, 
                isset( $opts[$t] ) ? $opts[$t] : 'off', 
                sprintf( __( 'Disable %s Archives', 'cd-archive-disabler' ), $label )
            );
            $rv .= '<br />';
        }
        return $rv;
    }
    
    /**
     * <lazy>Wrapper for add_settings_section so I have to type less</lazy>
     * 
     * @since 1.0
     * @access protected
     */
    protected function add_field( $id, $title )
    {
        $opts = get_option( $this->setting, array() );
        $args = isset( $opts[$id] ) ? $opts[$id] : false;
        add_settings_field(
            $this->prefix . $id,
            $title,
            array( &$this, $id . '_field_cb' ),
            $this->page,
            $this->section,
            $args
        );
    }
    
    /**
     * <lazy>Wrapper to create a nice checkbox field</lazy>
     * 
     * @since 1.0
     * @access protected
     */
    protected function checkbox( $id, $val, $label )
    {
        $box = sprintf(
            '<input type="checkbox" name="%1$s[%2$s]" id="%1$s[%2$s]" %3$s />',
            esc_attr( $this->setting ),
            esc_attr( $id ),
            checked( 'on', $val, false)
        );
        
        $rv = sprintf(
            '<label for="%1$s[%2$s]">%3$s %4$s</label>',
            esc_attr( $this->setting ),
            esc_attr( $id ),
            $box,
            ' ' . $label
        );
        
        return $rv;
    }
    
    /**
     * Get the fields we need to check in the settings sanitization callback
     * 
     * @since 1.0
     * @access protected
     */
    protected function to_check()
    {
        return array_merge(
            array( 'author', 'date' ),
            array_keys( $this->types() ),
            array_keys( $this->taxonomies() )
        );
    }
} // end class

new CD_AD_Admin_Options();

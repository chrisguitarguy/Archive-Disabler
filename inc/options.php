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
        add_action( 'load-options-reading.php', array( &$this, 'load' ) );
        add_filter( 'plugin_action_links_' . CD_AD_NAME, array( &$this, 'actions' ) );
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
        $this->add_field( 'on_catch', __( 'Disabled archives should...', 'cd-archive-disabler' ) );
        
    }
    
    /**
     * Callback function for our section
     * 
     * @since 1.0
     */
    function section_cb()
    {
        ?>
        <p class="description" id="archive-disabler">
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
    
    
    /**
     * Callback for post type archives options
     * 
     * @since 1.0
     */
    function post_types_field_cb()
    {
        echo $this->list_display( $this->types() );
    }
    
    /**
     * Callback for on_catch option
     * 
     * @since 1.0
     */
    function on_catch_field_cb( $args )
    {
        $actions = array(
            'redirect'  => __( 'Redirect to the home page', 'cd-archive-disabler' ),
            'error'     => __( 'Throw a 404 Not Found Error', 'cd-archive-disabler' )
        );
        foreach( $actions as $action => $label ):
        ?>
        <label for="<?php echo $this->setting; ?>[on_catch]">
            <input type="radio" name="<?php echo $this->setting; ?>[on_catch]" id="<?php echo $this->setting; ?>[on_catch]" value="<?php echo $action; ?>" <?php checked( $args, $action ); ?> />
            <?php echo esc_html( $label ); ?>
        </label>
        <br />
        <?php
        endforeach;
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
        $out['on_catch'] = isset( $in['on_catch'] ) && 'redirect' == $in['on_catch'] ? 'redirect' : 'error';
        return $out;
    }
    
    /**
     * Fired when `options-reading.php` loads.  Just adds an action to
     * `admin_head
     * 
     * @since 1.0
     * @uses add_action To hook into `admin_head`
     */
    function load()
    {
        add_action( 'admin_head', array( &$this, 'add_help' ) );
    }
    
    /**
     * Fired on `admin_head` ong the `options-reading.php` page. This 
     * function adds a some things to the help drop down about Archive 
     * Disabler.  It's hook into `admin_head` so it doesn't bump the 
     * default help down.
     * 
     * @since 1.0
     * @uses get_current_screen To fetch the WP_Screen object and add a tab
     */
    function add_help()
    {
        get_current_screen()->add_help_tab( array( 
            'id'       => 'cd-ad-help-tab',
            'title'    => __( 'Archive Disabler', 'cd-archive-disabler' ),
            'callback' => array( &$this, 'help_cb' )
        ) );
    }
    
    /**
     * Help tab callback.  Displays the content for the Archive Disabler
     * help tab.
     * 
     * @since 1.0
     */
    function help_cb()
    {
        echo '<p>';
        esc_html_e( 
            'Archive Disabler allows you to "disable" the various WordPress archive pages. ' .
            'To do so, check the archives you wish to have disabled below.',
            'cd-archive-disabler'
        );
        echo '</p><p>';
        esc_html_e(
            'Depending on your options, the disabled archives will redirect to the home page or simply 404. ' .
            'Redirecting archives, especially if they were previously enabled, is recommended.',
            'cd-archive-disabler'
        );
        echo '</p><p>';
        printf(
            __( 'Learn more about the author of Archive Disabler, %s.  Problems? Report bugs here: %s', 'cd-archive-disabler' ),
            '<a href="http://christopherdavis.me" target="_blank">Christopher Davis</a>',
            '<a href="https://github.com/chrisguitarguy/Archive-Disabler/issues" target="_blank">https://github.com/chrisguitarguy/Archive-Disabler/issues</a>'
        );
        echo '</p>';
    }
    
    /**
     * Plugin action links filter. Adds a "settings" link to the plugin
     * actions
     * 
     * @since 1.0
     */
    function actions( $actions )
    {
        $link = admin_url( 'options-reading.php#archive-disabler' );
        $actions['settings'] = sprintf(
            '<a href="%s">%s</a>',
            esc_url( $link ),
            esc_html__( 'Settings', 'cd-archive-disabler'  )
        );
        return $actions;
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
                $rv['posttype_' . $t] = isset( $obj->labels->singular_name ) ? $obj->labels->singular_name : $obj->label;
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
    protected function taxonomies()
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
                $rv['taxonomy_' . $t] = isset( $obj->labels->singular_name ) ? $obj->labels->singular_name : $obj->label;
            }
        }
        
        return $rv;
    }
    
    /**
     * Wrapper callback to echo out lists of options
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
                sprintf( __( 'Disable %s Archives', 'cd-archive-disabler' ), esc_html( $label ) )
            );
            $rv .= '<br />';
        }
        return $rv;
    }
    
    /**
     * Wrapper for add_settings_section so I have to type less
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

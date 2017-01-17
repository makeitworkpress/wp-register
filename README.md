# wp-register
Makes registering custom post types, taxonomies, sidebars, menus and widgets more easy. 

## Usage
Include the WP_Register class in your plugin, theme or child theme file. Require it in your file or use an autoloader. 

### Build your array with registrations
You can add the types you want to register through an multidimensional array. Please follow the format as specified below.

    $registrations = array(
        'postTypes' => array(
            array(
                'name'      => 'beer', 
                'plural'    => __('Beers', 'textdomain'), 
                'singular'  => __('Beer', 'textdomain'), 
                'args'      => array('public' => true) // Contains the arguments as they are supported by register_post_type. (optional)
            )
        ),
        'taxonomies' => array(
            array(
                'name'      => 'type', 
                'object'    => 'beer', 
                'plural'    => __('Types', 'textdomain'), 
                'singular'  => __('Type', 'textdomain'),
                'args'      => array('hierarchical' => true) // Contains the arguments as they are supported by register_post_type. (optional)
            )
        ),
        'sidebars' => array(
            array(
                'id'            => 'custom-sidebar', 
                'name'          => __('Custom Sidebar', 'textdomain'), 
                'description'   => __('Description for this sidebar', 'textdomain'),
                'before_widget' => '<section id="%1$s" class="widget %2$s">', // The opening element for the widget (optional)
                'after_widget'  => '</section>',  // The closing element for the widget (optional)
                'before_title'  => '<h3 class="widget-title">',  // The opening title tag for the widget (optional)
                'after_title'   => '</h3>' // The closing title tag for the widget (optional)
            )
        ),
        'widgets' => array(
            'WidgetClass' // The name of your class
        ),
        'menus' => array(
            'menu-location'     => __('Custom Menu Location', 'textdomain'),
            'another-location'  => __('AnotherCustom Menu Location', 'textdomain')
        ) 
    );
    

### Create instance
Create a new instance of the WP_Register class with your registration array and textdomain string as arguments.

    $optimize = new Classes\WP_Register\MT_WP_Register($registrations, 'textdomain');

# wp-register
Makes registering custom image sizes, menus, post types, taxonomies, sidebars and widgets easy. 

WP Register is maintained by [Make it WorkPress](https://makeitwork.press/scripts/wp-register/).

## Usage
Include the WP_Register class in your plugin, theme or child theme file. Require it in your file, use an autoloader or include it using composer. You can read more about autoloading in [the readme of wp-autoload](https://github.com/makeitworkpress/wp-autoload).

### Build your array with registrations
You can add the types you want to register through an multidimensional array. Please follow the format as specified in the example below.

```php
$registrations = array(
    'imageSizes' => array(
        array(
            'name'   => 'fhd',
            'height' => 1080,
            'width'  => 1920,
            'crop'   => true
        )
    ),
    'menus' => array(
        'menu-location'     => __('Custom Menu Location', 'textdomain'),
        'another-location'  => __('AnotherCustom Menu Location', 'textdomain')
    ),     
    'postTypes' => array(
        array(
            'name'      => 'beer', 
            'plural'    => __('Beers', 'textdomain'), 
            'singular'  => __('Beer', 'textdomain'), 
            'args'      => array('public' => true) // Contains the arguments as they are supported by register_post_type. (optional)
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
    'taxonomies' => array(
        array(
            'name'      => 'type', 
            'object'    => 'beer', 
            'plural'    => __('Types', 'textdomain'), 
            'singular'  => __('Type', 'textdomain'),
            'args'      => array('hierarchical' => true) // Contains the arguments as they are supported by register_post_type. (optional)
        )
    ),
    'widgets' => array(
        'WidgetClass' // The name of your custom widget class
    ),
);
```
    
### Create instance
Create a new instance of the WP_Register class with your registration array and textdomain string as arguments in the following manner.

```php
$register = new MakeitWorkPress\WP_Register\Register( $registrations, 'textdomain' );
```

The Register class accepts two arguments, namely an ``array $registrations`` and ``string $textdomain``.

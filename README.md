# wp-register
WordPress has many registration tasks. WP Register makes registering custom image sizes, menus, post types, taxonomies, sidebars and widgets easy. 

WP Register is maintained by [Make it WorkPress](https://makeitwork.press/scripts/wp-register/).

## Usage
Include the WP_Register class in your plugin, theme or child theme file. Require it in your file, use an autoloader or include it using composer. You can read more about autoloading in [the readme of wp-autoload](https://github.com/makeitworkpress/wp-autoload).

### Build your array with registrations
You can add the types you want to register through an multidimensional array. Please follow the format as specified in the example below. The comments will give additional information on what each value does.

```php
$registrations = [
    'imageSizes' => [
        [
            'name'   => 'fhd',
            'height' => 1080,
            'width'  => 1920,
            'crop'   => true
        ]
    ],
    'menus' => [
        'menu-location'     => __('Custom Menu Location', 'textdomain'),
        'another-location'  => __('AnotherCustom Menu Location', 'textdomain')
    ],     
    'postTypes' => [
        [
            'name'          => 'beer', 
            'plural'        => __('Beers', 'textdomain'), 
            'singular'      => __('Beer', 'textdomain'), 
            'args'          => ['public' => true]       // Contains the arguments as they are supported by register_post_type. (optional)
            'taxonomies'    => ['category']             // Connects existing taxonomies to this post type. Should be an array. (optional)
            'slug'          => 'slug'                   // Sets a custom slug, fastforward for the rewrite slug setting in arguments
            'icon'          => 'dashicon-beer'          // Sets a custom wp-admin menu icon, fastforward for the menu_icon setting in arguments
        ]
    ],
    'sidebars' => [
        [
            'id'            => 'custom-sidebar', 
            'name'          => __('Custom Sidebar', 'textdomain'), 
            'description'   => __('Description for this sidebar', 'textdomain'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">', // The opening element for the widget (optional)
            'after_widget'  => '</section>',  // The closing element for the widget (optional)
            'before_title'  => '<h3 class="widget-title">',  // The opening title tag for the widget (optional)
            'after_title'   => '</h3>' // The closing title tag for the widget (optional)
        ]
    ],    
    'taxonomies' => [
        [
            'name'      => 'type', 
            'object'    => 'beer', 
            'plural'    => __('Types', 'textdomain'), 
            'singular'  => __('Type', 'textdomain'),
            'args'      => ['hierarchical' => true] // Contains the arguments as they are supported by register_post_type. (optional)
       ]
    ],
    'widgets' => [
        'WidgetClass' // The name of your custom widget class, namespeced if using autoload
    ],
];
```
    
### Create instance
Create a new instance of the WP_Register class with your registration array and textdomain string as arguments in the following manner.

```php
$register = new MakeitWorkPress\WP_Register\Register( $registrations, 'textdomain' );
```

The Register class accepts two arguments, namely an ``array $registrations`` and ``string $textdomain``.

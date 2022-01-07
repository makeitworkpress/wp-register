<?php
/**
 * Class wrapper for registering post types, taxonomies, sidebars, widget and menus.
 *
 * @author Michiel Tramper https://www.makeitworkpress.com
 */
namespace MakeitWorkPress\WP_Register;
use WP_Error as WP_Error;

defined( 'ABSPATH' ) or die( 'Go eat veggies!' );

class Register {
    
    /**
     * Holds the array with types to register
     *
     * @var array
     * @access public
     */
    public $register;
    
    /**
     * Holds the string with the language domain.
     *
     * @var string
     * @access public
     */
    public $textdomain;    
    
    /**
     * Set the initial state of the class
     *
     * @param array     $register   The array with objects to be registered, supports post_types, taxonomies, menus, sidebars, widgets, blocks and image_sizes
     * @param string    $domain     The language domain for the current plugin or theme
     */
    public function __construct(array $register = [], string $textdomain = '') {
        $this->register     = $register;
        $this->textdomain   = '';
        
        // Execute our register methods
        foreach( $register as $key => $values ) {
            
            if( is_array($values) && method_exists($this, $key) ) {
                $this->$key();
            }
            
        }

    }

    /**
     * Register::postTypes is deprecated.
     * This code maintains backwards compatibility.
     */
    private function postTypes(): void {
        $this->post_types();
    }
    
    /**
     * Registers the post types
     */
    private function post_types(): void {
        
        add_action( 'init', function() {

            // Backwards compatibility with the old setup
            $this->register['post_types'] = isset($this->register['postTypes']) ? $this->register['postTypes'] : $this->register['post_types']; 
            
            foreach( $this->register['post_types'] as $post_type ) {
                
                if( ! isset($post_type['name']) ) {
                    continue;
                }
                
                $defaults = [
                    'labels' => [
                        'name'                  => sprintf( __('%s', $this->textdomain), $post_type['plural'] ),
                        'singular_name'         => sprintf( __('%s', $this->textdomain), $post_type['singular'] ),
                        'menu_name'             => sprintf( __('%s', $this->textdomain), $post_type['plural'] ),
                        'all_items'             => sprintf( __('%s', $this->textdomain), $post_type['plural'] ),
                        'add_new'               => __('Add New', $this->textdomain),
                        'add_new_item'          => sprintf( __('Add New %s', $this->textdomain), $post_type['singular'] ),
                        'edit_item'             => sprintf( __('Edit %s', $this->textdomain), $post_type['singular'] ),
                        'new_item'              => sprintf( __('New %s', $this->textdomain), $post_type['singular'] ),
                        'view_item'             => sprintf( __('View %s', $this->textdomain), $post_type['singular'] ),
                        'search_items'          => sprintf( __('Search %s', $this->textdomain), $post_type['plural'] ),
                        'not_found'             => sprintf( __('No %s found', $this->textdomain), $post_type['plural'] ),
                        'not_found_in_trash'    => sprintf( __('No %s found in Trash', $this->textdomain), $post_type['plural'] ),
                        'parent_item_colon'     => sprintf( __('Parent %s:', $this->textdomain), $post_type['singular'] ),                    
                    ],
                    'public' => true,
                ];
                
                // Fastforwards icon setting
                if( isset($post_type['icon']) && $post_type['icon'] ) {
                    $post_type['args']['menu_icon'] = $post_type['icon'];
                } 
                
                // Fastforwards slug setting
                if( isset($post_type['slug']) && $post_type['slug'] ) {
                    $post_type['args']['rewrite']['slug'] = $post_type['slug'];
                }                  
                
                // Merge defaults and arguments
                $post_type['args'] = wp_parse_args( isset($post_type['args']) ? $post_type['args'] : [], $defaults );
                
                register_post_type($post_type['name'], $post_type['args']);

                // Adds existing taxonomies to this post type
                if( isset($post_type['taxonomies']) && is_array($post_type['taxonomies']) ) {
                    foreach( $post_type['taxonomies'] as $taxonomy ) {
                        if( ! taxonomy_exists($taxonomy) ) {
                            continue;
                        }
                        register_taxonomy_for_object_type($taxonomy, $post_type['name']);
                    }
                }
                
            }
            
        } );
        
    }
    
    /**
     * Register custom taxonomies
     */
    private function taxonomies(): void {
        
        add_action( 'init', function() {
            
            foreach( $this->register['taxonomies'] as $taxonomy ) {
                
                // We should have a name and object
                if( ! isset($taxonomy['name']) || ! isset($taxonomy['object']) ) {
                    continue;
                } 
                
                $defaults = [
                    'labels' => [
                        'name'                          => sprintf( __('%s', $this->textdomain), $taxonomy['plural'] ),
                        'singular_name'                 => sprintf( __('%s', $this->textdomain), $taxonomy['singular'] ),
                        'menu_name'                     => sprintf( __('%s', $this->textdomain), $taxonomy['plural'] ),
                        'all_items'                     => sprintf( __('All %s', $this->textdomain), $taxonomy['plural'] ),
                        'edit_item'                     => sprintf( __('Edit %s', $this->textdomain), $taxonomy['singular'] ),
                        'view_item'                     => sprintf( __('View %s', $this->textdomain), $taxonomy['singular'] ),
                        'update_item'                   => sprintf( __('Update %s', $this->textdomain), $taxonomy['singular'] ),
                        'add_new_item'                  => sprintf( __('Add New %s', $this->textdomain), $taxonomy['singular'] ),
                        'new_item_name'                 => sprintf( __('New %s Name', $this->textdomain), $taxonomy['singular'] ),
                        'parent_item'                   => sprintf( __('Parent %s', $this->textdomain), $taxonomy['plural'] ),
                        'parent_item_colon'             => sprintf( __('Parent %s:', $this->textdomain), $taxonomy['plural'] ),
                        'search_items'                  => sprintf( __('Search %s', $this->textdomain), $taxonomy['plural'] ),
                        'popular_items'                 => sprintf( __('Popular %s', $this->textdomain), $taxonomy['plural'] ),
                        'separate_items_with_commas'    => sprintf( __('Seperate %s with commas', $this->textdomain), $taxonomy['plural'] ),
                        'add_or_remove_items'           => sprintf( __('Add or remove %s', $this->textdomain), $taxonomy['plural'] ),
                        'choose_from_most_used'         => sprintf( __('Choose from most used %s', $this->textdomain), $taxonomy['plural'] ),
                        'not_found'                     => sprintf( __('No %s found', $this->textdomain), $taxonomy['plural'] )                   
                    ],
                    'hierarchical' => true
                ];
                
                // Set the slug
                if( isset($taxonomy['slug']) ) {
                    $taxonomy['args']['rewrite'] = ['slug' => $taxonomy['slug']];
                } 
                    
                // Merge defaults and arguments
                $taxonomy['args'] = wp_parse_args( isset($taxonomy['args']) ? $taxonomy['args'] : [], $defaults );                    
                
                register_taxonomy( $taxonomy['name'], $taxonomy['object'], $taxonomy['args'] );
                
            }
            
        } );
        
    }
    
    /**
     * Register sidebars
     */
    private function sidebars(): void {
        
        add_action( 'widgets_init', function() {
            
            foreach( $this->register['sidebars'] as $sidebar ) {

                // Default attributes for the sidebars
                $defaults = [
                    'before_widget' => '<section id="%1$s" class="widget %2$s">',
                    'after_widget'  => '</section>',
                    'before_title'  => '<h3 class="widget-title">',
                    'after_title'   => '</h3>'
                ];

                $sidebar = wp_parse_args( $sidebar, $defaults );

                register_sidebar( $sidebar );

            }
            
        } );
            
    }

    /**
     * Register::imageSizes is deprecated.
     * This code maintains backwards compatibility.
     */
    private function imageSizes(): void {
        $this->image_sizes();
    }    
    
    /**
     * Registers custom image sizes
     * If themes hook early on after_setup_theme, this function can still be executed
     */
    private function image_sizes(): void {
        
        add_action( 'after_setup_theme', function() {

            // Maintains backwards compatibility with older camelCase set-up
            $this->register['image_sizes'] = isset($this->register['imageSizes']) ? $this->register['imageSizes'] : $this->register['image_sizes'];
            
            foreach( $this->register['image_sizes'] as $image_size ) {
                add_image_size( $image_size['name'], $image_size['width'], $image_size['height'], $image_size['crop'] );
            }
            
        }, 20 );
        
    }
    
    /**
     * Registers custom widgets
     */
    private function widgets(): void {
        
        add_action( 'widgets_init', function() {
            
            foreach( $this->register['widgets'] as $widget ) {
                
                if( ! class_exists($widget) ) {
                    continue;
                }
                
                register_widget( $widget );
            }
            
        } );
    }
    
    /**
     * Register menus
     */
    private function menus(): void {
        
        add_action( 'after_setup_theme', function() {
            register_nav_menus( $this->register['menus'] );
        }, 20 );
        
    }

    /**
     * Register gutenberg blocks
     */
    private function blocks(): void {

        foreach( $this->register['blocks'] as $block ) {

            // Type should be set
            if( ! isset($block['type']) || ! $block['type'] ) {
                continue;
            }

            // Argument should be set
            if( ! isset($block['args']) || ! is_array($block['args']) ) {
                continue;
            }            

            register_block_type($block['type'], $block['args']);

        }
 
    }
    
}
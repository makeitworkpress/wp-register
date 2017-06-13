<?php
/**
 * Class wrapper for registering post types, taxonomies, sidebars, widget and menus.
 *
 * @author Michiel Tramper - https://michieltramper.com & https://www.makeitworkpress.com
 */
namespace WP_Register;
use WP_Error as WP_Error;

defined( 'ABSPATH' ) or die( 'Go eat veggies!' );

class Register {
    
    /**
     * Holds the array with types to register
     *
     * @access private
     */
    public $register;
    
    /**
     * Holds the string with the language domain.
     *
     * @access private
     */
    public $textdomain;    
    
    /**
     * Set the initial state of the class
     *
     * @param array     $register   The array with objects to be registered
     * @param string    $domain     The language domain for the current plugin or theme
     */
    public function __construct(Array $register = array(), $textdomain = '') {
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
     * Registers the post types
     */
    private function postTypes() {
        
        $object = $this;
        
        add_action( 'init', function() use($object) {
            
            foreach( $object->register['postTypes'] as $type ) {
                
                if( ! isset($type['name']) ) {
                    continue;
                }
                
                $defaults = array(
                    'labels' => array(
                        'name'                  => sprintf( __('%s', $object->textdomain), $type['plural'] ),
                        'singular_name'         => sprintf( __('%s', $object->textdomain), $type['singular'] ),
                        'menu_name'             => sprintf( __('%s', $object->textdomain), $type['plural'] ),
                        'all_items'             => sprintf( __('%s', $object->textdomain), $type['plural'] ),
                        'add_new'               => __('Add New', $object->textdomain),
                        'add_new_item'          => sprintf( __('Add New %s', $object->textdomain), $type['singular'] ),
                        'edit_item'             => sprintf( __('Edit %s', $object->textdomain), $type['singular'] ),
                        'new_item'              => sprintf( __('New %s', $object->textdomain), $type['singular'] ),
                        'view_item'             => sprintf( __('View %s', $object->textdomain), $type['singular'] ),
                        'search_items'          => sprintf( __('Search %s', $object->textdomain), $type['plural'] ),
                        'not_found'             => sprintf( __('No %s found', $object->textdomain), $type['plural'] ),
                        'not_found_in_trash'    => sprintf( __('No %s found in Trash', $object->textdomain), $type['plural'] ),
                        'parent_item_colon'     => sprintf( __('Parent %s:', $object->textdomain), $type['singular'] ),                    
                    ),
                    'public' => true,
                );
                
                // Fastforwards in setting the slug
                if( isset($type['slug']) ) {
                    $type['args']['rewrite'] = array('slug' => $type['slug']);
                }                
                
                // Merge defaults and arguments
                $type['args'] = wp_parse_args( isset($type['args']) ? $type['args'] : array(), $defaults );
                
                register_post_type($type['name'], $type['args']);
                
            }
            
        } );
        
    }
    
    /**
     * Register custom taxonomies
     */
    private function taxonomies() {
        
        $object = $this;
        
        add_action( 'init', function() use($object) {
            
            foreach( $object->register['taxonomies'] as $taxonomy ) {
                
                // We should have a name and object
                if( ! isset($taxonomy['name']) || ! isset($taxonomy['object']) ) {
                    continue;
                } 
                
                $defaults = array(
                    'labels' => array(
                        'name'                          => sprintf( __('%s', $object->textdomain), $taxonomy['plural'] ),
                        'singular_name'                 => sprintf( __('%s', $object->textdomain), $taxonomy['singular'] ),
                        'menu_name'                     => sprintf( __('%s', $object->textdomain), $taxonomy['plural'] ),
                        'all_items'                     => sprintf( __('All %s', $object->textdomain), $taxonomy['plural'] ),
                        'edit_item'                     => sprintf( __('Edit %s', $object->textdomain), $taxonomy['singular'] ),
                        'view_item'                     => sprintf( __('View %s', $object->textdomain), $taxonomy['singular'] ),
                        'update_item'                   => sprintf( __('Update %s', $object->textdomain), $taxonomy['singular'] ),
                        'add_new_item'                  => sprintf( __('Add New %s', $object->textdomain), $taxonomy['singular'] ),
                        'new_item_name'                 => sprintf( __('New %s Name', $object->textdomain), $taxonomy['singular'] ),
                        'parent_item'                   => sprintf( __('Parent %s', $object->textdomain), $taxonomy['plural'] ),
                        'parent_item_colon'             => sprintf( __('Parent %s:', $object->textdomain), $taxonomy['plural'] ),
                        'search_items'                  => sprintf( __('Search %s', $object->textdomain), $taxonomy['plural'] ),
                        'popular_items'                 => sprintf( __('Popular %s', $object->textdomain), $taxonomy['plural'] ),
                        'separate_items_with_commas'    => sprintf( __('Seperate %s with commas', $object->textdomain), $taxonomy['plural'] ),
                        'add_or_remove_items'           => sprintf( __('Add or remove %s', $object->textdomain), $taxonomy['plural'] ),
                        'choose_from_most_used'         => sprintf( __('Choose from most used %s', $object->textdomain), $taxonomy['plural'] ),
                        'not_found'                     => sprintf( __('No %s found', $object->textdomain), $taxonomy['plural'] )                   
                    ),
                    'hierarchical' => true
                );
                
                // Set the slug
                if( isset($taxonomy['slug']) ) {
                    $taxonomy['args']['rewrite'] = array('slug' => $taxonomy['slug']);
                } 
                    
                // Merge defaults and arguments
                $taxonomy['args'] = wp_parse_args( isset($taxonomy['args']) ? $taxonomy['args'] : array(), $defaults );                    
                
                register_taxonomy( $taxonomy['name'], $taxonomy['object'], $taxonomy['args'] );
                
            }
            
        } );
        
    }
    
    /**
     * Register sidebars
     */
    private function sidebars() {
        
        $object = $this;
        
        add_action( 'widgets_init', function() use($object) {
            
            foreach( $object->register['sidebars'] as $sidebar ) {

                // Default attributes for the sidebars
                $defaults = array(
                    'before_widget' => '<section id="%1$s" class="widget %2$s">',
                    'after_widget'  => '</section>',
                    'before_title'  => '<h3 class="widget-title">',
                    'after_title'   => '</h3>'
                );

                $sidebar = wp_parse_args( $sidebar, $defaults );

                register_sidebar( $sidebar );

            }
            
        } );
            
    }
    
    /**
     * Registers custom image sizes
     * If themes hook early on after_setup_theme, this function can still be executed
     */
    private function imageSizes() {
        
        $object = $this;
        
        add_action( 'after_setup_theme', function() use($object) {
            
            foreach( $object->register['imageSizes'] as $imageSize ) {
                add_image_size( $imageSize['name'], $imageSize['width'], $imageSize['height'], $imageSize['crop'] );
            }
            
        }, 20 );
        
    }
    
    /**
     * Registers custom widgets
     */
    private function widgets() {
        
        $object = $this;
        
        add_action( 'widgets_init', function() use($object) {
            
            foreach( $object->register['widgets'] as $widget ) {
                
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
    private function menus() {
        
        $object = $this;
        
        add_action( 'after_setup_theme', function() use($object) {
            register_nav_menus( $object->register['menus'] );
        }, 20 );
        
    }
    
}
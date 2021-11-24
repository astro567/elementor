<?php

defined( "ABSPATH" ) || exit;

class BenjyPostType {

    private static $instance;

    private $plural;

    private $singular;

    private function __construct(){

        $this->plural = "Customs";
        $this->singular = "Custom";
    }

    public static function get_instance(){
        if( !isset( self::$instance ) ){
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function set_post_type( $slug, $args = [] ){
        $parsed_args = wp_parse_args($args, $this->get_post_type_data());
        register_post_type( $slug, $parsed_args );
        add_theme_support( 'post-formats', array( 'gallery', 'video' ) );
        add_post_type_support( $slug, 'post-formats', array( 'gallery', 'video' ) );
    }

    public function get_default_data(){
        $args = array(
            'label'              => "",
            'labels'             => $this->get_product_labels(),
            'description'        => "",
            'public'             => true,
            'show_in_rest'              => true,
            'rest_controller_class'     => 'WP_REST_Posts_Controller',
            'query_var'          => true,
            'rewrite'            => [],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'          => "",
            'supports'           => [ 'title', 'editor' ],
            'taxonomies'         => [],
        );
    }

    private function get_product_labels(){
        return [
            'name'                  => _x( $this->plural, 'Post type general name', 'twentytwentychild' ),
            'singular_name'         => _x( $this->singular, 'Post type singular name', 'twentytwentychild' ),
            'menu_name'             => _x( $this->plural, 'Admin Menu text', 'twentytwentychild' ),
            'name_admin_bar'        => _x( $this->singular, 'Add New on Toolbar', 'twentytwentychild' ),
            'add_new_item'          => __( 'Add New ' . $this->singular, 'twentytwentychild' ),
            'new_item'              => __( 'New ' . $this->singular, 'twentytwentychild' ),
            'edit_item'             => __( 'Edit ' . $this->singular, 'twentytwentychild' ),
            'view_item'             => __( 'View ' . $this->singular, 'twentytwentychild' ),
            'all_items'             => __( 'All ' . $this->plural, 'twentytwentychild' ),
            'search_items'          => __( 'Search ' . $this->plural, 'twentytwentychild' ),
            'parent_item_colon'     => __( 'Parent ' . $this->plural . ':', 'twentytwentychild' ),
            'not_found'             => __( 'No ' . strtolower( $this->plural ) . ' found.', 'twentytwentychild' ),
            'not_found_in_trash'    => __( 'No ' . strtolower( $this->plural ) . ' found in Trash.', 'twentytwentychild' ),
            'featured_image'        => _x( $this->singular . ' Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'twentytwentychild' ),
            'archives'              => _x( $this->singular . ' archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'twentytwentychild' ),
            'insert_into_item'      => _x( 'Insert into ' . strtolower( $this->singular ), 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'twentytwentychild' ),
            'uploaded_to_this_item' => _x( 'Uploaded to this ' . strtolower( $this->singular ), 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'twentytwentychild' ),
            'filter_items_list'     => _x( 'Filter ' . strtolower( $this->plural ) . ' list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'twentytwentychild' ),
            'items_list_navigation' => _x( $this->plural . ' list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'twentytwentychild' ),
            'items_list'            => _x( $this->plural . ' list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'twentytwentychild' ),
        ];
    }
}
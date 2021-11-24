<?php

defined( "ABSPATH" ) || exit();

class BenjyProduct extends BenjyCustomFields {

    private static $instance;

    private $main_image;

    private $image_gallery;

    private $title;

    private $description;

    private $price;

    private $sale_price;

    private $is_on_sale;

    private $youtube_video;

    private $category;

    private $slug = "product";

    private function __construct(){
    }

    public function get_post_type_data(){
        $slug = $this->get_slug();
        $name = ucfirst($slug);
        return array(
            'label'              => _x( $name, 'Post type singular name', 'twentytwentychild' ),
            'labels'             => $this->get_labels_data(),
            'description'        => _x( $name . ' post type is made to create ' . $slug . 's', 'Post type description', 'twentytwentychild' ),
            'public'             => true,
            'show_in_rest'              => true,
            'rest_controller_class'     => 'WP_REST_Posts_Controller',
            'query_var'          => true,
            'rewrite'            => array( 'slug' => $slug ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 22,
            'menu_icon'          => "dashicons-products",
            'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
            'taxonomies'         => [ "category", "post_tag" ],
        );
    }

    public function get_labels_data(){
        $singular = ucfirst( $this->get_slug() );
        $plural = $singular . "s";
        return [
            'name'                  => _x( $plural, 'Post type general name', 'twentytwentychild' ),
            'singular_name'         => _x( $singular, 'Post type singular name', 'twentytwentychild' ),
            'menu_name'             => _x( $plural, 'Admin Menu text', 'twentytwentychild' ),
            'name_admin_bar'        => _x( $singular, 'Add New on Toolbar', 'twentytwentychild' ),
            'add_new'               => __( 'Add New', 'twentytwentychild' ),
            'add_new_item'          => __( 'Add New ' . $singular, 'twentytwentychild' ),
            'new_item'              => __( 'New ' . $singular, 'twentytwentychild' ),
            'edit_item'             => __( 'Edit ' . $singular, 'twentytwentychild' ),
            'view_item'             => __( 'View ' . $singular, 'twentytwentychild' ),
            'all_items'             => __( 'All ' . $plural, 'twentytwentychild' ),
            'search_items'          => __( 'Search ' . $plural, 'twentytwentychild' ),
            'parent_item_colon'     => __( 'Parent ' . $plural . ':', 'twentytwentychild' ),
            'not_found'             => __( 'No ' . strtolower( $plural ) . ' found.', 'twentytwentychild' ),
            'not_found_in_trash'    => __( 'No ' . strtolower( $plural ) . ' found in Trash.', 'twentytwentychild' ),
            'featured_image'        => _x( $singular . ' Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'twentytwentychild' ),
            'archives'              => _x( $singular . ' archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'twentytwentychild' ),
            'insert_into_item'      => _x( 'Insert into ' . strtolower( $singular ), 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'twentytwentychild' ),
            'uploaded_to_this_item' => _x( 'Uploaded to this ' . strtolower( $singular ), 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'twentytwentychild' ),
            'filter_items_list'     => _x( 'Filter ' . strtolower( $plural ) . ' list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'twentytwentychild' ),
            'items_list_navigation' => _x( $plural . ' list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'twentytwentychild' ),
            'items_list'            => _x( $plural . ' list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'twentytwentychild' ),
        ];
    }

    public function get_fields(){
        global $post;
        return [
            "onsale" => [
                "type" => "checkbox",
                "title" => __("Is on sale?", "twentytwentychild")
            ],
            "price" => [
                "type" => "number",
                "title" => __("Price", "twentytwentychild"),
                "attrs" => [
                    "min"   => 0,
                    "value" => esc_attr__( get_post_meta( $post->ID, $this->prefix."_price", true ), "twentytwentychild" )
                ]
            ],
            "sale_price" => [
                "type" => "number",
                "title" => __("Sale price", "twentytwentychild"),
                "attrs" => [
                    "min"   => 0,
                    "value" => esc_attr__( get_post_meta( $post->ID, $this->prefix."_sale_price", true ), "twentytwentychild" )
                ]
            ]
        ];
    }

    public function display_custom_fields(){
        global $post;
        $prefix = $this->get_prefix();

        echo "<div class='form-wrap'>";
        wp_nonce_field( $prefix . '-custom-fields', $prefix . '-custom-fields_wpnonce', false, true );
        
        $checked = "";
        foreach( $this->get_fields() as $name => $data ){
            $attrs = "";
            if( $data["type"] === "checkbox" ){
                if ( get_post_meta( $post->ID, $prefix . "_" . $name, true ) == "on" ){
                    $attrs .= " checked=checked";
                }
            }
            if( isset( $data['attrs'] ) && is_array( $data["attrs"] ) && count($data[ 'attrs' ]) > 0 ){
                foreach( $data['attrs'] as $key => $value ){
                    $attrs .= " " . $key . "=" . $value;
                }
            }
            echo "<div class='form-field form-required'>
                <label for='{$prefix}_$name' class='{$prefix}_{$data['type']}'><b>{$data['title']}</b></label>
                <input type='{$data['type']}' name='{$prefix}_$name' id='{$prefix}_$name' $attrs />
            </div>";
        }

        echo "</div>";
    }

    public static function get_instance(){
        if( !isset( self::$instance ) ){
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function get_slug(){
        return $this->slug;
    }

    public function get_prefix(){
        return $this->prefix;
    }
}

function BenjyProduct(){
    return BenjyProduct::get_instance();
}
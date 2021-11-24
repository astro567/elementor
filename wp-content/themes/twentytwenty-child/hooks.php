<?php

defined( "ABSPATH" ) || exit;

class BenjyHooks {

    private static $instance;

    private $product;

    private function __construct(){
        $this->product = BenjyProduct();
        add_action( "init", [ $this, "register_post_type" ] );
        add_action( "admin_menu", [ $this, "add_custom_fields" ] );
        add_action( "init", [ $this, "disable_admin_bar_menu" ] );
        add_filter( "the_content", [ $this, "add_prices_to_single_product" ] );
        add_filter( "the_content", [ $this, "add_related_products_to_single_product" ] );
        add_filter( "the_title", [ $this, "add_sale_tag_to_home_products" ], 10, 2 );
        add_shortcode( "product_shortcode", [ $this, "create_product_shortcode" ] );
        add_action( 'wp_head', [ $this, 'address_mobile_address_bar' ] );
    }

    public function register_post_type(){    
        $this->product->set_post_type( $this->product->get_slug(), $this->product->get_post_type_data() );
    }

    public function add_custom_fields(){
        $this->product->add_custom_fields( 'benjy-custom-fields', "Extra data", [ $this->product, 'display_custom_fields' ], "product" );
        $this->product->save( [ $this->product, "get_fields" ] );
    }

    public static function get_instance(){
        if( !isset( self::$instance ) ){
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function disable_admin_bar_menu(){
        if( wp_get_current_user()->ID === 3 ){
            show_admin_bar(false);
        }
    }

    public function add_prices_to_single_product($content){
        global $post;
        $prefix = "benjycf_";

        // Add price and sale price to single page product
        if( is_single() && $post->post_type === "product" ){
			$price = get_post_meta( $post->ID, $prefix . "price", true);
			if( $price ){
				$content .= "<p>" .
				__( "Price:", "twentytwentychild" ) . " $price NIS
				</p>";
			}
			$sale = get_post_meta( $post->ID, $prefix . "onsale", true);
			$sale_price = get_post_meta( $post->ID, $prefix . "sale_price", true);
			if( $sale === "on" && $sale_price ){
				$content .= "<p>" .
				__( "Sale Price:", "twentytwentychild" ) . " $sale_price NIS
				</p>";
			}
        }

        return $content;

    }

    public function add_related_products_to_single_product( $content ){
        global $post;
        $prefix = "benjycf_";

        if( is_single() && $post->post_type === "product" ){
			$args = [
                "post_type"         => "product",
                "post__not_in"      => [$post->ID],
                "post_status"       => "publish",
                "orderby"           => "rand",
                "posts_per_page"    => 3
            ];
            $query = new WP_Query($args);
            $related_products_title = __( "Related products", "twentytwentychild" );
            $relateds = "<div class='related-products'>
                            <h2>$related_products_title</h2>";
            foreach( $query->posts as $related_product ){
                $link = esc_attr(get_permalink($related_product->ID));
                $relateds .= "<div class='related-product'>
                    <h3><a href='$link'>{$related_product->post_title}</a></h3>
                </div>";
            }
            $relateds .= "</div>";

            $content .= $relateds;
        }
        return $content;
    }

    public function add_sale_tag_to_home_products( $title, $post_id ){
        if( is_front_page()  && get_post_type( $post_id ) === "product" ){
            $sale = get_post_meta( $post_id, "benjycf_onsale", true);
            if( $sale === "on" ){
                $t = __( "On sale!", "twentytwentychild" );
                $title = "<span class='product_tag'>$t</span>" . $title;
            }
        }
        return $title;
    }

    public function create_product_shortcode( $atts, $content, $shortcode_tag ){
        $atts = shortcode_atts( array(
            'product_id' => 0,
            'bg_color' => '#ffffff'
        ), $atts, $shortcode_tag );
        if( $atts["product_id"] === 0 ){
            return false;
        }
        $product_id = $atts["product_id"];
        $product = get_post( $product_id );
        $link = get_permalink( $product_id );
        $price = __("Price: ", "twentytwentychild") . get_post_meta( $product_id, $this->product->get_prefix()."_price", true );
        $thumb_id = get_post_thumbnail_id( $product_id );
        $thumb_url_array = wp_get_attachment_image_src($thumb_id, 'thumbnail-size', true);
        $thumb_url = $thumb_url_array[0];

        $shortcode = "<div style='background-color: {$atts['bg_color']}' class='product-shortcode-$product_id'>
            <h2><a href='$link'>{$product->post_title}</a></h2>
            <div><a href='$link'><img src='$thumb_url' /></a></div>
            <p>$price</p>
        </div>";

        echo apply_filters("benjy_shortcode_filter", $shortcode, $atts, $content, $shortcode_tag);

    }

    public function address_mobile_address_bar() {
        $color = "#008509";
        echo '<meta name="theme-color" content="'.$color.'">';
        echo '<meta name="msapplication-navbutton-color" content="'.$color.'">';
        echo '<meta name="apple-mobile-web-app-capable" content="yes">';
        echo '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">';
    }

}

function BenjyHooks(){
    return BenjyHooks::get_instance();
}

BenjyHooks();
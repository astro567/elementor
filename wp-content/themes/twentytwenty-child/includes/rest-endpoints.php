<?php

defined( "ABSPATH" ) || exit;

class Rest_Endpoints {
	protected static $instance = null;

	public static function get_instance() {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	protected function __construct() {
		add_action( 'rest_api_init', [ $this, 'init_endpoints' ] );
	}

	public function init_endpoints() {
		register_rest_route( 'wp/v2', '/(?P<category>[0-9a-zA-Z-]+)', array(
			'methods'  => 'GET',
			'callback' => [ $this, 'get_data' ],
			'permission_callback' => '__return_true'
		) );
	}

	// load class data
	public function get_data( $data ) {

		$cat = [ "category_name" => $data['category'] ];
		if( intval( $data['category'] ) !== 0 ){
			$cat = [ "category" => $data['category'] ];	
		}	
		$product_args = array(
			'posts_per_page'   => -1,
			'orderby'          => 'name',
			'order'            => 'ASC',
			'post_type'        => 'product',
			'post_status'	   => 'publish'
		);
		$args = wp_parse_args($product_args, $cat);
		$posts = get_posts($args);

		$prefix = "benjycf";
		$output = [];
		foreach($posts as $post){
			$thumb_id = get_post_thumbnail_id( $post->ID );
			$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'thumbnail-size', true);
			$thumb_url = $thumb_url_array[0];

			$output[] = [
				"title" => $post->post_title,
				"description"	=> $post->post_excerpt,
				"image"	=> $thumb_url,
				"price" => get_post_meta( $post->ID, $prefix."_price", true ),
				"sale_price" => get_post_meta( $post->ID, $prefix."_sale_price", true ),
				"sale" => get_post_meta( $post->ID, $prefix."_onsale", true ) === "on" ? __("Yes", "twentytwentychild") : __("No","twentytwentychild")
			];
		}

		return wp_send_json( $output );
	}
}

if(!is_admin()){
	Rest_Endpoints::get_instance();
}
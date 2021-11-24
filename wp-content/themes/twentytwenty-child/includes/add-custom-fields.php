<?php

defined( "ABSPATH" ) || exit;

class BenjyCustomFields extends BenjyPostType {

    private static $instance;

    protected $prefix = "benjycf";

    protected $fields;

    private function __construct(){}

    public function save( $fields ){
        add_action( 'save_post',  function($post_id, $post) use ($fields) { $this->save_custom_fields( $fields, $post_id, $post ); }, 5, 3 );
    }

    public function add_custom_fields( $name, $title = null, $callback = null, $slug = null, $place = "normal", $priority = "high" ){
        $args = [
            "name" => $name,
            "title" => $title ?? ucfirst( $name ),
            "callback" => $callback,
            "slug" => $slug ?? strtolower( $name ),
            "place" => $place,
            "priority" => $priority
        ];
        $this->add( $args );
    }

    public static function get_instance(){
        if( !isset( self::$instance ) ){
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function add( $args ){
        if ( function_exists( 'add_meta_box' ) ) {
            // echo '<pre>';
            // print_r($args);
            // echo '</pre>';
            // wp_die();
            add_meta_box( $args["name"], __($args["title"], "twentytwentychild"), [ $args["callback"][0], $args["callback"][1] ] , $args["slug"], $args["place"], $args["priority"] );
        }
    }

    

    public function save_custom_fields( $fields, $post_id, $post ){
        if ( !isset( $_POST[ $this->prefix . '-custom-fields_wpnonce' ] ) || !wp_verify_nonce( $_POST[ $this->prefix . '-custom-fields_wpnonce' ], $this->prefix . '-custom-fields' ) ){
            return $post_id;
        }
        if ( !current_user_can( 'edit_post', $post_id ) ){
            return $post_id;
        }
        $fields = $fields[0]->{$fields[1]}();
        
        // loop through fields and save the data
        foreach( $fields as $key => $data ){
            $name = $this->prefix . "_$key";
            $value = sanitize_text_field( $_POST[ $name ] );
            if( isset( $value ) ){
                $old = get_post_meta($post_id, $name, true);
                if ($value && $value != $old) {
                    update_post_meta($post_id, $name, $value);
                } elseif ('' == $value && $old) {
                    delete_post_meta($post_id, $name, $old);
                }
            }
        } // end foreach
    }

}
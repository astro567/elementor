<?php

defined( "ABSPATH" ) || exit;

class BenjyEnqueues {

    private static $instance;

    private function __construct(){
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_front' ] );
    }

    public static function get_instance(){
        if( !isset( self::$instance ) ){
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    public function enqueue_front(){
        $parenthandle = 'parent-style';
        $theme = wp_get_theme();
        wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css', array(), $theme->parent()->get('Version') );
        wp_enqueue_style( 'child-style', get_stylesheet_uri(), array( $parenthandle ), $theme->get('Version') );
    }

}

function BenjyEnqueues(){
    return BenjyEnqueues::get_instance();
}

BenjyEnqueues();
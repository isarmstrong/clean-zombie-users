<?php

add_action( 'admin_enqueue_scripts', 'zombie_load_js_and_css' );

function zombie_load_js_and_css() {
    global $hook_suffix;

//    if ( in_array( $hook_suffix, array(
//        'braaaaains.php'
//    ) ) ) {
        wp_register_style( 'clean-zombies.css', ZOMBIE_PLUGIN_URL . 'library/zombie.css', array(), ZOMBIE_VERSION );
        wp_enqueue_style( 'clean-zombies.css');

        wp_register_script( 'clean-zombies.js', ZOMBIE_PLUGIN_URL . 'library/zombie.js', array('jquery'), ZOMBIE_VERSION );
        wp_enqueue_script( 'clean-zombies.js' );
//    }
}
<?php

function braaains_options_page()
{
    global $wpdb, $tp, $roles, $thelist;
    $tp = $wpdb->prefix;

    // Get a list of user roles on this site from lowest to highest
    $roles = array_reverse(get_editable_roles());

    // Initialize the results list in case we need it
    $thelist = "";


    /**
     *  Require Plugin Files
     */
    require_once( 'enqueue.php');               // Enqueue the styles & scripts for this plugin
    require_once( 'helper-queries.php' );       // A few static queries needed by the plugin
    require_once( 'build-query.php' );          // Builds the operational logic from selected options
    require_once( 'options-view.php');          // Get the HTML that displays the options page
}

add_action('admin_menu', 'braaains_add_options_pages');
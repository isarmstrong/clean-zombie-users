<?php

/** Begin Logic **/
$result = ""; // initialize the results variable for POST feedback

if (isset($_POST['page_updated'])) { // if the post has been updated
    ?>

    <div id="message" class="updated fade"><p>
        <strong><?php echo "User Data Pruned - View Results Below"; ?></strong></p></div><?php

    // Set up which roles should be included based on selected options
    $theroles = ""; // initialize variable for SQL AND/OR string
    $role_options = array(); // initialize an array that fetches boolean options from the form
    foreach ($roles as $role) { // iterate over detected roles
        $getcheck = "include-" . strtolower($role['name']); // get the role as a value that matches the form as a variable for complex inclusion
        if(isset($_POST["{$getcheck}"])) { ${"include_" . strtolower($role['name'])} = (bool)$_POST["{$getcheck}"]; } // set a dynamic variable and check it against the submitted form
        if (isset(${"include_" . strtolower($role['name'])})) { // if the matching role is checked in the form
            $role_options[] = "$wpdb->usermeta.meta_value LIKE '%" . strtolower($role['name']) . "%' "; // generate an array of statements for inclusion
        }
    }

    // Find the first role
    if ((is_array($role_options)) or ($role_options instanceof Traversable)) : // is this an array?
        $first_value = reset($role_options); // in that case, get the value of the first element so we can use AND instead of OR

        // Determine the correct operator for each line
        foreach ($role_options as $option) {
            $operator = ($option == $first_value ? "AND " : "OR "); // first value is AND, the rest are OR
            $theroles .= $operator . $option; // write out the array as a string to be echoed into the SQL query
        }
    endif;

    // If no roles were selected, abort the process, otherwise all users will be selected (this is bad)
    if (empty($role_options)) {
        $theroles = "LEAVE";
        $result .= "No user roles were selected | ";
    }

    /** If the user has never posted anything: logic **/

    // Check selections from form and set variable
    $never_post = (bool)$_POST['never-post'];
    $never_comment = (bool)$_POST['never-comment'];

    if (empty($never_comment) && empty($never_post)) {
        $result = "No delete condition set (post/comment status) | ";
    }

    if ($never_comment) {
        $eat_spammy_brains = " AND $wpdb->comments.comment_author IS NULL";
    } else {
        if (isset($never_post)) {
            $eat_spammy_brains = ''; // don't set a default if the other option is active
        }
        if (empty($never_post)) {
            $eat_spammy_brains = " AND NOT EXISTS ({$commentquery})"; // deselect all users with comments
        }
    }
    if ($never_post) {
        $eat_quiet_brains = " AND $wpdb->posts.post_author is NULL";
    } else {
        if (isset($never_comment)) {
            $eat_quiet_brains = ''; // don't set a default if the other option is active
        }
        if (empty($never_comment)) {
            $eat_quiet_brains = " AND NOT EXISTS ({$authorquery})"; // deselect all users with posts or revisions
        }
    }

    /** Check to see if we are limiting the number of entries processed **/
    $limited = $_POST['limiter'];
    if ($limited == 'limited') {
        $chunk_size = mysql_real_escape_string($_POST['thelimit']);
        if (is_int($chunk_size)) {
            $limiter = "LIMIT 0, " . $chunk_size;
        } else {
            die("The Zombies ate your brains! (this field can only be a number)");
        }
    }
    if (empty($limiter)) {
        $limiter = "";
    }

    // Build our SQL statement for the operation
    $mainquery = "
            SELECT *
                  FROM $wpdb->users
                    LEFT JOIN $wpdb->comments ON ($wpdb->users.user_email = $wpdb->comments.comment_author_email)
                    LEFT JOIN $wpdb->posts ON ($wpdb->users.ID = $wpdb->posts.post_author)
                    LEFT JOIN $wpdb->usermeta ON ($wpdb->users.ID = $wpdb->usermeta.user_id)
                WHERE $wpdb->usermeta.meta_key = '{$tp}capabilities'
                {$theroles}
                {$eat_spammy_brains}
                {$eat_quiet_brains}
                GROUP BY display_name
                {$limiter};
            ";
    $userlist = (array)$wpdb->get_results("$mainquery", object);

    // Has the user confirmed that they know what they are doing?
    if (isset($_POST['confirm-deletes']) && isset($_POST['confirm-backup'])) {
        foreach ($userlist as $u) {
            wp_delete_user($u->user_id);
            echo "Deleted: " . $u->display_name . "<br />";
        }
        $thelist = "<h1>Braaaaaaains!</h1>";
    } else {
        $thelist = "<h3>The following users would have been deleted by this operation:</h3><ol class='testlist'>";
        foreach ($userlist as $u) {
            $thelist .= "<li>" . $u->display_name . " [" . $u->user_id . "] ( " . $u->user_email . " ) | User  Level: " . $u->meta_value . "</li>";
        }
        $thelist .= "</ol>";
    }

    $result .= 'Zombie users deleted: ' . count($userlist);


    // end processing


}
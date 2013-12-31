<?php
/*
Plugin Name: Clean Up Zombie Users
Plugin URI: http://imperativeideas.com
Description: Damn zombie users! Always eating your MySQL database. This plugin cleans up all sorts of users, especially spammy ones. Braaaaaains.
Author: Imperative Ideas
Version: 0.3
Author URI: http://imperativeideas.com/
*/

$braaains_version = '0.4';

global $authorlist;

function braaains_add_options_pages()
{
    if (function_exists('add_options_page')) {
        add_options_page("Clean Zombie Users", 'Clean Zombie Users', 8, __FILE__, 'braaains_options_page');
    }
}


function braaains_options_page()
{
    global $wpdb, $zombie_version;
    $tp = $wpdb->prefix;

    // Get a list of user roles on this site from lowest to highest
    $roles = array_reverse(get_editable_roles());

    // Initialize the results list in case we need it
    $thelist = "";

    /** HELPER QUERIES **/

    // A list of users with posts or revisions in the database
    $authorquery = "
        SELECT *
            FROM $wpdb->users
                LEFT JOIN $wpdb->posts ON ($wpdb->users.ID = $wpdb->posts.post_author)
            WHERE $wpdb->posts.post_author IS NOT NULL
        GROUP BY display_name;
    ";
    $authorlist = (array)$wpdb->get_results("$authorquery", object);

    // A list of users with comments in the database
    $commentquery = "
    SELECT user_id, COUNT( * ) AS total
		FROM {$wpdb->comments}
		WHERE comment_approved = 1 AND user_id <> 0
		GROUP BY user_id
    ";
    // Count user comments
    $comment_counts = (array)$wpdb->get_results("$commentquery", object);


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
            ${"include_" . strtolower($role['name'])} = (bool)$_POST["{$getcheck}"]; // set a dynamic variable and check it against the submitted form
            if (${"include_" . strtolower($role['name'])}) { // if the matching role is checked in the form
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
            $limiter = "LIMIT 0, " . $chunk_size;
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


    } ?>

    <!-- BEGIN DOCUMENT -->

    <script type="text/javascript">
        WebFontConfig = {
            google: { families: [ 'Oswald::latin' ] }
        };
        (function () {
            var wf = document.createElement('script');
            wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
                '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
            wf.type = 'text/javascript';
            wf.async = 'true';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(wf, s);
        })();

        jQuery(document).ready(function ($) {

            // Disable deletion of admins, because you just can't fix stupid
            var optionset = $('#the-options');
            optionset.find('input[name=include-administrator]').attr('disabled', 'disabled');
            optionset.find('label[name=include-administrator]').css('color', 'rgb(180,180,180)');

            // Hide the confirmation options by default
            confirmation = $('#confirmation');
            confirmation.hide();

            // Check the radio button
            $('input:radio[name="operation"]').change(
                function () {
                    if ($(this).is(':checked') && $(this).val() == 'test') {
                        confirmation.hide();
                        $('input[name=confirm-deletes]').attr('checked', false);
                        $('input[name=confirm-backup]').attr('checked', false);
                    }
                    if ($(this).is(':checked') && $(this).val() == 'live') {
                        confirmation.show();
                    }
                }
            );

            // Hide the user limit field by default
            confirmation = $('#userlimit');
            confirmation.hide();

            // Conditionally Show the user limit field
            $('input:radio[name="limiter"]').change(
                function () {
                    if ($(this).is(':checked') && $(this).val() == 'unlimited') {
                        confirmation.hide();
                    }
                    if ($(this).is(':checked') && $(this).val() == 'limited') {
                        confirmation.show();
                    }
                }
            );
        });
    </script>

    <style>
        #clean-wrap {
            max-width: 800px;
            overflow: hidden;
        }

        #clean-wrap h2 {
            font-family: Oswald, Arial, Helvetica, arial, sans-serif;
            border-bottom: 1px solid rgb(20, 20, 20);
            font-size: 20px;
            line-height: 22px;
        }

        #the-options {
            padding: 0 0 15px 12px;
        }

        #the-options input[name=include-administrator] {
            background: silver;
        }

        .testlist li {
            margin-left: 20px;
        }
    </style>

    <div id=clean-wrap>

        <h2>Clean Up Users <?php echo $zombie_version; ?></h2>

        <?php

        if ($result != "") {
            echo '<div style="border: 1px solid #888888; padding: 5px;">';
            echo '<strong>Results</strong>:<br /> ' . trim($result) . '</div>';
        }

        echo $thelist;

        ?>

        <form id="zombie-cage" enctype="multipart/form-data" method="post"
              action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
            <input type="hidden" name="page_updated" id="page_updated" value="true"/>

            <p>The reason nobody writes these plugins is that they don't want hate mail when a user breaks their
                install. If you do not know how to back up and restore a database, the author of this plugin makes
                no implied warranty against catastrophic failure.</p>

            <p><em>Be advised that this plugin has not been tested with e-commerce plugins in WordPress. DO NOT use this
                    on a commerce site or you may delete customers who have not technically commented on a blog entry
                    or written a post. It may see purchases as posts, it probably won't. DO NOT TOUCH THE DELETE BUTTON!</em></p>

            <p>In order to proceed, please confirm the following:</p>

            <p>
                <input type="radio" name="operation" checked="checked" value="test">This is just a test run, list the
                users to be deleted.<br>
                <input type="radio" name="operation" value="live">This is for real, let's kill some zombies!<br>
            </p>

            <ul id="confirmation">
                <li>
                    <input type="checkbox" name="confirm-deletes" id="confirm-deletes"/>
                    <label name="confirm-deletes">I understand that this plugin permanently deletes users from my
                        database</label>
                </li>
                <li>
                    <input type="checkbox" name="confirm-backup" id="confirm-backup"/>
                    <label name="confirm-backup">I confirm that I have backed up my database and that I know how to
                        restore it</label>
                </li>
            </ul>

            <p>Do you have a slow database? If so, you may want to do this in chunks. You can limit the number of
                results processed
                at once with this setting. Highly recommended for the actual deletion, up to you for testing.</p>
            <ul>
                <li><input type="radio" name="limiter" checked="checked" value="unlimited">Run the entire query at once
                    (ok for small sites)<br></li>
                <li><input type="radio" name="limiter" value="limited">Break this operation into chunks (good for 1000+
                    users)<br></li>
                <li id="userlimit"><label name="thelimit" id="thelimit"/>Limit users processed to: <input type="text"
                                                                                                          name="thelimit"
                                                                                                          id="thelimit"
                                                                                                          placeholder="Enter Limit">
                </li>
            </ul>

            <h2>Setting Up Your User Sweep</h2>

            <div id="the-options">

                <h3>Include the following user roles when cleaning up:</h3>
                <?php
                foreach ($roles as $role) {
                    echo "<input type='checkbox' value='include-" . strtolower($role['name']) . "' name='include-" . strtolower($role['name']) . "' />";
                    echo "<label name='include-" . strtolower($role['name']) . "' >" . $role['name'] . "s</label><br/>";
                }
                ?>

                <h3>Remove users who meet the following criteria</h3>
                <input type="checkbox" name="never-comment" value="never-comment"/>
                <label name="never-comment">User has never left a comment</label><br/>
                <input type="checkbox" name="never-post" value="never-post"/>
                <label name="never-post">User has never made a post</label><br/>

            </div>


            <div class="submit">
                <input type="submit" name="page_updated" value="Remove Zombie Users"/>
            </div>
        </form>

        <h2>Author Security Check</h2>

        <p>If an author with no posts shows up here, it means they have active revisions. Often this is your webmaster
            but
            sometimes it's a hack that didn't work. Make sure everyone on this list is authorized and delete any mystery
            authors!</p>

        <ol><?php
            foreach ($authorlist as $u) {
                echo "<li>" . $u->display_name . " [ID: " . $u->post_author . "] ( " . $u->user_email . " ) | Posts Authored: " . count_user_posts($u->post_author);
            }
            ?>
        </ol>

        <h2>A list of users with approved comments</h2>
        <ol>
            <?php
            foreach ($comment_counts as $count) {
                $user = get_userdata($count->user_id);
                echo '<li>' . $user->display_name . ' | comments: ' . $count->total . '</li>';
            }
            ?>
        </ol>

    </div>
<?php
}

add_action('admin_menu', 'braaains_add_options_pages');

?>
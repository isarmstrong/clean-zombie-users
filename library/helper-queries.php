<?php

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
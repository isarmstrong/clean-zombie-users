<?php

// Start HTML view file

?>

<div id=clean-wrap>

        <h2>Clean Zombie Users (v.<?php echo ZOMBIE_VERSION; ?>)</h2>

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

    <p style="color: #c2403a;"><em><strong>DO NOT use this on a commerce site or you may delete customers</strong> who have not technically commented on a blog entry or written a post.</em></p>

    <h2>Set Plugin Run Mode</h2>

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

    <p>Do you have a slow database? If so, you may want to do this in chunks. You can limit the number of results processed at once with this setting. Highly recommended for the actual deletion, up to you for testing.</p>
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
<p>If an author with no posts shows up here, it means they have active revisions. Often this is your webmaster but sometimes it's a SQL injection or administrative error. Make sure everyone on this list is authorized and delete/modify any mystery authors!</p>

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
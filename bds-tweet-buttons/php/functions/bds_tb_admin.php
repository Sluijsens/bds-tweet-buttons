<?php
// When the admin menu loads, load the plugin menu
add_action("admin_menu", "bds_tb_admin_pages");

/**
 * Loads all the pages
 */
function bds_tb_admin_pages() {
    add_menu_page("Tweet Buttons", "BDS Tweet Buttons", "manage_options", "bds-tweet-buttons");
    add_submenu_page("bds-tweet-buttons", "Tweet Buttons", "Tweet Buttons", "manage_options", "bds-tweet-buttons", "bds_tb_overview");
    add_submenu_page("bds-tweet-buttons", "New Tweet Button", "Add new", "manage_options", "bds-tb-edit", "bds_tb_edit");
}

/**
 * Displays the overview page containing the table
 */
function bds_tb_overview() {
    // Get the wpdb variable
    global $wpdb;

    // Check if the user has sufficient permissions
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    // Check if a new tweet button has to be created/edited
    if (isset($_POST["title"])) {
        $table = $wpdb->prefix . "bds_tweet_buttons";

        // Put all data into a variable and remove/unset unneeded data
        $values = $_POST;

        $values["data"]["optout"] = (isset($values["data"]["optout"])) ? true : false;
        $values["data"]["hashtags"] = str_replace(" ", "", $values["data"]["hashtags"]);
        $values["data"]["recommended"] = str_replace(" ", "", $values["data"]["recommended"]);
        $values['twitter_options'] = serialize($values["data"]);
        $values["own_style"] = (isset($values["own_style"])) ? true : false;
        $values["tweet_box"] = (isset($values["tweet_box"])) ? true : false;

        // Unset unneeded variables
        unset($values["id"]);
        unset($values["data"]);

        if ($_POST["id"] == -1) { // Create new tweet button
            // extract the variable with data
            extract($values);

            // Insert twete button into database
            $wpdb->insert($table, array(
                "title" => $title,
                "css_id" => $css_id,
                "css_class" => $css_class,
                "own_style" => (isset($own_style)) ? true : false,
                "tweet_box" => (isset($tweet_box)) ? true : false,
                "twitter_options" => $twitter_options
            ));

            // Notify user about succession
            ?><div class="updated">
                A new Tweet Button has successfully been created!
            </div><?php
        } else if ($_POST["id"] > 0) { // Edit tweet button
            // Edit/update database table row
            $wpdb->update($table, $values, array("id" => $_POST["id"]));

            // Notify user about succession
            ?>
            <div class="updated">
                Tweet button successfully edited!
            </div>
            <?php
        } // Edit
    } // End tweet button creation
    ?>
    <!-- Needed div -->
    <div class="wrap">
        <h2>Tweet Buttons <a href="admin.php?page=bds-tb-edit" class="add-new-h2">Add new</a></h2>
        <?php
        // Prepare the table with data
        $tb_list_tabel = new BDS_TB_ListTable();
        $tb_list_tabel->prepare_items();
        ?>
        <form id="bds-tb-filter" method="get">
            <!-- For plugins, we need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Show the table -->
            <?php $tb_list_tabel->display(); ?>
        </form>
    </div> <!-- end wrap -->
    <?php
}

/**
 * Create or edit a tweet button
 */
function bds_tb_edit() {
    // Get the wpdb variable
    global $wpdb;

    // Check if user has sufficient permissions
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    // Check if a new button has to be made or an existing one needs editing. If nothing was set create a new.
    if (isset($_GET['a'])) {
        $action = $_GET['a'];
    } else {
        $action = "new";
    }

    // Set page title according to teh action
    if ($action == "new") {
        $page_title = "New Tweet Button";
    } else {
        $page_title = "Edit Tweet Button";

        // When editing also retrieve existing data and extract that
        extract(bds_get_tweet_button($_GET['tbid']));
    }
    ?>
    <!-- Needed div -->
    <div class="wrap">
        <h2><?php echo $page_title; ?></h2>

        <form action="admin.php?page=bds-tweet-buttons" method="post">
            <table class="form-table">
                <tr>
                    <th><!-- title -->
                        <label for="title">Title:</label>
                    </th>
                    <td>
                        <input id="title" class="regular-text" type="text" name="title" value="<?php echo (isset($title)) ? $title : ""; ?>" />
                    </td>
                </tr>
                <tr>
                    <th><!-- Own css id -->
                        <label for="css_id">CSS ID:</label>
                    </th>
                    <td>
                        <input id="css_id" class="regular-text" type="text" name="css_id" value="<?php echo (isset($css_id)) ? $css_id : ""; ?>" />
                    </td>
                </tr>
                <tr>
                    <th><!-- Own css class -->
                        <label for="css_class">CSS Class(es):</label>
                    </th>
                    <td>
                        <input id="css_class" class="regular-text" type="text" name="css_class" value="<?php echo (isset($css_class)) ? $css_class : ""; ?>" />
                    </td>
                </tr>
                <tr>
                    <th><!-- Tweet box? -->
                        <label for="tweet_box">Use Tweet Box?</label>
                    </th>
                    <td>
                        <input id="tweet_box" type="checkbox" <?php echo (isset($tweet_box) && $tweet_box) ? "checked='checked'" : ""; ?> name="tweet_box" value="1" /><br />
                        <span class="description">Let users enter the tweet on your site before going to the pop-up.</span>
                    </td>
                </tr>
                <tr>
                    <th><!-- Own styling? -->
                        <label for="own_style">Use own styling?</label>
                    </th>
                    <td>
                        <input id="own_style" type="checkbox" <?php echo (isset($own_style) && $own_style) ? "checked='checked'" : ""; ?> name="own_style" value="1" />
                        <br />
                        <span class="description">Use your own styling instead of the standard styling of the plugin.</span>
                    </td>
                </tr>
                <tr><!--  -->
                    <td colspan="2">
                        <div class="twitter-options options-box">
                            <div class="options-title">
                                <span>Twitter options</span>
                            </div>
                            <div class="options">
                                <div class="options-content">
                                    <!--  -->
                                    <div style="width: 50%; display: inline-block;">
                                        <p><label for="data-hashtags">Hastags: <span class="description">(comma-separated)</span></label><input id="data-hashtags" type="text" value="<?php echo (isset($data['hashtags'])) ? $data['hashtags'] : ""; ?>" name="data[hashtags]" /></p>
                                        <p>
                                            <label for="data-recommended">Recommended accounts: <span class="description">(comma-separated)</span></label><input id="data-recommended" type="text" value="<?php echo (isset($data['recommended'])) ? $data['recommended'] : ""; ?>" name="data[recommended]" />
                                        </p>
                                        <p><label for="data-language">Language: </label>
                                            <select id="data-language" type="text" value="" name="data[language]">
                                                <?php
                                                $languages = array(
                                                    "en" => "English",
                                                    "ja" => "Japanese",
                                                    "es" => "Spanish"
                                                );

                                                $selected = array(
                                                    "en" => "",
                                                    "ja" => "",
                                                    "es" => ""
                                                );

                                                $selected[$data["language"]] = 'selected="selected"';

                                                foreach ($languages as $abbr => $language) {
                                                    ?>
                                                    <option <?php echo $selected[$abbr]; ?> value="<?php echo $abbr; ?>"><?php echo $language; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </p>
                                        <p><label for="data-size">Size:</label>
                                            <select id="data-size" name="data[size]">
                                                <option selected="selected" value="medium">Medium (default)</option>
                                                <option value="large">Large</option>
                                            </select></p>
                                        <p><label for="data-text">Default Tweet:</label><textarea id="data-text" name="data[text]"><?php echo (isset($data['text'])) ? $data['text'] : ""; ?></textarea></p>
                                    </div>

                                    <!--  -->
                                    <div style="width: 49%;" class="right">
                                        <p><label for="data-url">URL to Tweet:</label><input id="data-url" type="text" value="<?php echo (isset($data['url'])) ? $data['url'] : ""; ?>" name="data[url]" /></p>
                                        <p><label for="data-via">Via user:</label><input id="data-via" type="text" value="<?php echo (isset($data['via'])) ? $data['via'] : ""; ?>" name="data[via]" /></p>
                                        <p><label for="data-count">Count Box position:</label><input id="data-count" type="text" value="<?php echo (isset($data['url'])) ? $data['count'] : ""; ?>" name="data[count]" /></p>
                                        <p><label for="data-counturl">Count Box URL:</label><input id="data-counturl" type="text" value="<?php echo (isset($data['counturl'])) ? $data['counturl'] : ""; ?>" name="data[counturl]" /></p>
                                        <p><label for="data-optout">Opt-Out: <span class="description">Simply said, turn on if you DO NOT want twitter to receive usage data.</span></label><input id="data-optout" type="checkbox" <?php echo (isset($data['optout']) && $data['optout']) ? "checked='checked'" : ""; ?> value="1" name="data[optout]" /></p>
                                    </div>
                                </div> <!-- end options-content -->
                            </div> <!-- end options  -->
                        </div> <!-- End twitter-options -->
                    </td>
                </tr>
                <tr><!-- Submit button -->
                    <td colspan="2">
                        <input type="hidden" name="id" value="<?php echo (isset($id)) ? $id : -1; ?>" />
                        <input class="button-primary" type="submit" value="Save" />
                    </td>
                </tr>
            </table>
        </form>
    </div><!-- End wrap --><?php
}
?>

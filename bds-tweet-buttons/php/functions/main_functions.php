<?php

/**
 * Execute the code for the shortcodes
 * @param mixed array $atts
 */
function bds_tb_shortcode($atts) {
    // Extract the attributes
    extract($atts);

    if (isset($tbid)) {
        // Get the tweet button by it's id
        $tweet_button = bds_get_tweet_button($tbid);

        // Extract the variables
        extract($tweet_button);
        extract($twitter_options);

        if ($own_style) { // Using own styling, note that the count box does not work here!
            // Build the link for the twitter button
            $tweet_button_link = "https://www.twitter.com/share";
            $tweet_button_link .= "?lang=" . $language;
            $tweet_button_link .= (isset($hashtags) && $hashtags != '') ? "&hashtags=" . $hashtags : "";
            $tweet_button_link .= (isset($recommended) && $recommended != "") ? "&related=" . $recommended : "";
            $tweet_button_link .= (isset($size) && $size != "") ? "&size=" . $size : "";
            $tweet_button_link .= (isset($via) && $via != "") ? "&via=" . $via : "";
            $tweet_button_link .= (isset($optout) && $optout != "") ? "&dnt=" . $optout : "";
            $tweet_button_link .= (isset($count) && $count != "") ? "&count=" . $count : "";
            $tweet_button_link .= (isset($counturl) && $counturl != "") ? "&counturl=" . $counturl : "";
            $tweet_button_link .= (isset($url)) ? "&url=" . urlencode($url) : "";

            $base_link = $tweet_button_link; // Create a link without the text. This is used when a tweet box is used too.

            $tweet_button_link .= (isset($text) && $text != "") ? "&text=" . urlencode($text) : "";

            // Text that is in the tweet box by default according to the settings/options
            $tweet_box_defaults = $text;
            $tweet_additional_text = (isset($url) && $url != "") ? " $url" : "";

            if (isset($hashtags) && $hashtags != "") {
                $hashtags_array = explode(",", $hashtags);
                // Loop through hashtag array to place a hashtag in front of the term
                foreach ($hashtags_array as $hashtag) {
                    $tweet_additional_text .= " #$hashtag";
                }
            }

            $tweet_additional_text .= (isset($via) && $via != "") ? " via @$via" : "";
            ?>

            <!-- Create the button -->
            <div class="custom-tweet-button">
                <input type="hidden" value="<?php echo $tweet_box_defaults; ?>" class="tweet_box_defaults" />
                <input type="hidden" value="<?php echo $tweet_additional_text; ?>" class="tweet_additional_text" />
                <?php
                if ($tweet_box) { // Also use tweet box, so create it
                    ?>
                    <div class="twitter_bird"></div>
                    <textarea base-link="<?php echo $base_link; ?>" style="resize: none;"><?php echo (isset($text)) ? $text : "" ?></textarea>
                    <?php
                }
                ?>
                <!-- Create the button itself -->
                <a class="tweet_button" href="<?php echo $tweet_button_link; ?>" target="_blank">Tweet</a>
                <?php
                if ($tweet_box) {
                    ?>
                    <div class="chars_rem"></div>
                    <?php
                }
                ?>
            </div>
            </div>
            <?php
        } else { // Use the default javascript buttons from twitter itself
            ?>
            <!--Javascript:<br />
            <a href = "https://twitter.com/share" class = "twitter-share-button" data-lang = "en">Tweet</a>
            <div id = 'empty-thing'></div>
            <script>!function(d, s, id) {
            //document.getElementById(FrameID).contentDocument.location.reload(true);
            var js, fjs = d . getElementsByTagName(s)[0];
            if (!d . getElementById(id)) {
                js = d . createElement(s);
                js.id = id;
                js.src = "https://platform.twitter.com/widgets.js";
                fjs . parentNode . insertBefore(js, fjs);
            }
            }(document, "script", "twitter-wjs");
            </script>-->
            <?php
        }
    }
}

/**
 * get a list of tweet buttons
 * @return array
 */
function bds_get_tweet_button_list() {
    global $wpdb;

    $table = "{$wpdb->prefix}bds_tweet_buttons";
    return $wpdb->get_results("SELECT id, title FROM $table", ARRAY_A);
}

/**
 * Get the tweet button from the database and return it.
 * @param Integer $tbid
 * @return mixed array
 */
function bds_get_tweet_button($tbid) {
    global $wpdb;

    // Get the actual data from the database
    $tweet_button = $wpdb->get_row("SELECT id, title, own_style, css_id, css_class, tweet_box, twitter_options FROM {$wpdb->prefix}bds_tweet_buttons WHERE id='$tbid'", ARRAY_A);

    // Unserialize the stored array and retrieve the twitter options this way
    $tweet_button['data'] = unserialize($tweet_button['twitter_options']);
    $tweet_button['twitter_options'] = $tweet_button['data'];

    // Convert to booleans because these are checkbox values
    $tweet_button["own_style"] = ((int) $tweet_button["own_style"] == 1) ? true : false;
    $tweet_button["tweet_box"] = ((int) $tweet_button["tweet_box"] == 1) ? true : false;

    $tweet_button["twitter_options"]["optout"] = (isset($tweet_button["twitter_options"]["optout"]) &&
            (int) $tweet_button["twitter_options"]["optout"] == 1) ? true : false;

    return $tweet_button;
}

/**
 * Return the title of a tweet button
 * @param Integer $tbid
 * @return String
 */
function bds_get_tweet_button_title($tbid) {
    $tweet_button = bds_get_tweet_button($tbid);

    return $tweet_button["title"];
}

/**
 * Delete a tweet button
 * @param Integer $tbid
 */
function bds_delete_tweet_button($tbid) {
    // Get the wpdb variable
    global $wpdb;

    // Delete the button from the database
    $wpdb->delete("{$wpdb->prefix}bds_tweet_buttons", array(
        "id" => $tbid
    ));
}
?>

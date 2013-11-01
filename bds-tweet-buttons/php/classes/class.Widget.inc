<?php

/**
 * Description of class
 *
 * @author Bryan
 */
class BDS_Tweet_Button extends WP_Widget {

    public function __construct() {
        parent::__construct("bds-tweet-buttons", "BDS Tweet Buttons", "Choose a created tweet button to show.");
    }

    public function widget($args, $instance) {
        echo $args["before_widget"].$args["before_title"].$instance["title"].$args["after_title"];
        echo do_shortcode($instance["shortcode"]);
        echo $args["after_widget"];
    }

    public function form($instance) {
        ?>
        <p>
            <label for="<?php echo $this->get_field_id("tbid"); ?>">Tweet Button:</label>
            <select id="<?php echo $this->get_field_id("tbid"); ?>" name="<?php echo $this->get_field_name("tbid"); ?>" class="widefat">
                <?php
                $tweet_buttons = bds_get_tweet_button_list();

                foreach ($tweet_buttons as $tweet_button) {
                    ?><option <?php echo ($instance["tbid"] == $tweet_button["id"]) ? "selected='selected'" : ""; ?> value="<?php echo $tweet_button["id"]; ?>"><?php echo $tweet_button["title"]; ?></option><?php
                }
                ?>
            </select>
        </p>
        <p>
            <input id="<?php echo $this->get_field_id("show_title"); ?>" type="checkbox" <?php echo (isset($instance["show_title"]) && $instance["show_title"] == true) ? "checked='checked'" : ""; ?> name="<?php echo $this->get_field_name("show_title"); ?>" value="1" />
            <label for="<?php echo $this->get_field_id("show_title"); ?>">Show Title?</label>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance["tbid"] = (isset($new_instance["tbid"])) ? $new_instance["tbid"] : -1;
        $instance["show_title"] = (isset($new_instance["show_title"]) && (int) $new_instance == 1) ? true : false;
        $title = ($instance["show_title"] == true) ? bds_get_tweet_button_title($instance["tbid"]) : "";
        $instance["title"] = $title;
        $instance["shortcode"] = "[bds_tb tbid='" . $instance["tbid"] . "']";
        
        return $instance;
    }

}
?>

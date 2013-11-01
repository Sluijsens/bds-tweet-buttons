<?php

/**
 * Builds a table with the given information. Extends WP_List_Table
 *
 * @author Bryan
 */
class BDS_TB_ListTable extends WP_List_Table {
    /**
     * Create the class with the parent constructor
     */
    function __construct($args = array()) {
        global $status, $page;
        
        parent::__construct(array(
            "singular" => "bds_tweet_button_list",
            "plural" => "bds_tweet_buttons_list",
            "ajax" => false
        ));
    }
    
    /**
     * Adds text before or after the table
     * @param String $which -> 'top' or 'bottom'
     */
    function extra_tablenav($which) {
        if ($which == "top") {
            echo "Here you have an overview of your available Tweet Buttons. Simply copy-paste the shortcode to use it. If you do not want to use the title you can delete it from the shortcode.";
        }
        if ($which == "bottom") {
            
        }
    }
    
    /**
     * Set the columns for the header and footer rows
     * @return array
     */
    function get_columns() {
        return $columns = array(
            "cb" => "<input type='checkbox' />",
            "bds_tb_title" => "Title",
            "bds_tb_own_style" => "Own Style?",
            "bds_tb_tweet_box" => "Tweet Box?",
            "bds_tb_shortcode" => "Shortcode"
        );
    }
    
    /**
     * Set the sortable columns
     * @return array
     */
    function get_sortable_columns() {
        return $sortable = array(
            "bds_tb_id" => array("id", false),
            "bds_tb_title" => array("title", false)
        );
    }
    
    /**
     * Get the items from the database
     */
    function prepare_items() {
        // Get the wpdb variable
        global $wpdb;
        
        // Put table name in a variable and start building the query
        $table = $wpdb->prefix . "bds_tweet_buttons";
        $query = "SELECT id, title, own_style, tweet_box FROM $table";
        
        // Get order by functionalities and add it to the query
        $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
        $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
        if (!empty($orderby) && !empty($order)) {
            $query .= " ORDER BY $orderby $order";
        }

        // Process the bulk actions
        $this->process_bulk_action();

        /* Pagination */
        // Amount of items
        $total_items = $wpdb->query($query);
        // How many items per page?
        $per_page = "25";
        // Which page is this?
        $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : "";
        // Page Number
        if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }
        // How many pages do we have in total?
        $total_pages = ceil($total_items / $per_page);
        // Adjust query to use the pagination
        if (!empty($paged) && !empty($per_page)) {
            $offset = ($paged - 1) * $per_page;
            $query .= " LIMIT " . (int) $offset . "," . (int) $per_page;
        }

        // Register pagination
        $this->set_pagination_args(array(
            "total_items" => $total_items,
            "total_pages" => $total_pages,
            "per_page" => $per_page
        ));

        // Register the columns
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        // Fetch the items
        $this->items = $wpdb->get_results($query);
    }
    
    // Get the bulk action that can be performed
    function get_bulk_actions() {
        return $actions = array(
            "delete" => "Delete"
        );
    }
    
    // Prcocess the bulk actions
    function process_bulk_action() {
        if ($this->current_action() == "delete") {
            
            // Loop through all the selected buttons
            foreach ($_GET["bds_tb"] as $tbid) {
                // Remove the tweet button
                bds_delete_tweet_button($tbid);
            }
        }
    }

    /**
     * Set the column 'cb' content
     */
    function column_cb($item) {
        return sprintf(
                '<input type="checkbox" name="bds_tb[]" value="%1$s" />',
                /* $2%s */ $item->id                //The value of the checkbox should be the record's id
        );
    }
    
    // Set the default column content function to fill the columns
    function column_default($item, $column_name) {

        switch ($column_name) {
            case 'bds_tb_shortcode': // Show the shortcode
                return "[bds_tb tbid='" . $item->id . "' title='" . $item->title . "']";
            case 'bds_tb_title': // Show the title + id and make it a link to edit the button
                return "<a href='admin.php?page=bds-tb-edit&a=edit&tbid=" . $item->id . "'>" . $item->title . "</a> <span class='description'>(ID: " . $item->id . ")</span>";
            case "bds_tb_own_style": // Show if own style is being used
                return ((int) $item->own_style == 1) ? "Yes" : "No";
            case "bds_tb_tweet_box": // Show if tweet box is being used
                return ((int) $item->tweet_box == 1) ? "Yes" : "No";
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

}

?>
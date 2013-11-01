jQuery(document).ready(function($) {

    // The amount of characters left to type in the box
    //var chars_left = 140;

    /**
     * Get the number of characters left to type.
     * @returns Integer
     */
    function get_chars_left(message_box) {
        var chars_left = 140 - message_box.val().length - $(".custom-tweet-button").children(".tweet_additional_text").val().length;
        $(".custom-tweet-button").children(".chars_rem").html(chars_left);
    }
    if ($(".custom-tweet-button").children(".chars_rem").size() > 0) {
        get_chars_left($(".custom-tweet-button textarea"));
    }
    // Collapse the twitter options box in the admin panel when screen is loaded
    $(".options-box .options").slideToggle();

    // When clicked on the options box title expand or collapse the options box
    $(".options-box .options-title").click(function() {
        var options_element = $(".options-box .options");

        options_element.slideToggle();
    });

    // When using a tweet box and when the text has changed, update the link of the button.
    $(".custom-tweet-button").children("textarea").keyup(function() {
        var button = $(".custom-tweet-button").children("a");
        var base_link = $(this).attr("base-link");
        get_chars_left($(this));
        button.attr("href", base_link + "&text=" +  encodeURIComponent($(this).val()));
    });
    
    $(".custom-tweet-button").children("a").click(function() {
        var text_box = $(".custom-tweet-button").children("textarea");
        
        text_box.val($(".custom-tweet-button").children(".tweet_box_defaults").val());
        
        get_chars_left(text_box);
    });
});
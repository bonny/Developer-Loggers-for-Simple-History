<?php
/**
 * Plugin Name: Simple History Testloggers
 * Plugin URI: https://github.com/bonny/WordPress-Simple-History-Testloggers
 * Description: Misc loggers to test Simple History
 * Version: 0.1
 * Author: Pär Thernström
 */


/**
 * Log emails sent with wp_mail()
 */
add_filter( 'wp_mail', function($args) {

    $context = array(
        "email_to" => $args["to"],
        "email_subject" => $args["subject"],
        "email_message" => $args["message"]
    );

    SimpleLogger()->info("Sent an email to '{email_to}' with subject '{email_subject}' using wp_mail()", $context);

    return $args;

} );


/**
 * Log public JavaScript errors
 */
add_action("wp_head", "add_js_to_header");
add_action('wp_ajax_simple_history_log_js_error', "log_error");
add_action('wp_ajax_nopriv_simple_history_log_js_error', "log_error");

function add_js_to_header() {
    ?>
    <script>
        window.onerror = function(m, u, l) {
        // console.log('Error message: '+m+'\nURL: '+u+'\nLine Number: '+l);
        if (encodeURIComponent) {
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>',
                img = new Image(1,1);
            img.src = ajaxurl + "?action=simple_history_log_js_error&m=" + encodeURIComponent(m) + "&u=" + encodeURIComponent(u) + "&l=" + encodeURIComponent(l);
        }
        return true;
        }
    </script>
    <?php

}

// Log error with ajax
function log_error() {

    $error = isset( $_GET["m"] ) ? $_GET["m"] : "";
    $url = isset( $_GET["u"] ) ? $_GET["u"] : "";
    $line = isset( $_GET["l"] ) ? $_GET["l"] : "";

    $context = array(
        "error" => $error,
        "url" => $url,
        "line" => $line,
        "browser" => $_SERVER["HTTP_USER_AGENT"]
    );

    SimpleLogger()->info("Detected a JavaScript error on page '{url}': '{error}'", $context);

    exit;

}


/**
 * Log free space on harddrives on server
 */
add_action("init", function() {

    $disk_free_space = disk_free_space(dirname(__FILE__));
    $disk_total_space = disk_total_space(dirname(__FILE__));

    $context = array(
        "disk_free_space" => $disk_free_space,
        "disk_total_space" => $disk_total_space,
        "disk_free_space_formatted" => size_format($disk_free_space),
        "disk_total_space_formatted" => size_format($disk_total_space)
    );

    #SimpleLogger()->info("Free space", $context);

});

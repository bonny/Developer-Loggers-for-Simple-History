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

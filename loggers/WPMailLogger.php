<?php

/**
 * Our logger is a class that extends the built in SimpleLogger-class
 */
class WPMailLogger extends SimpleLogger {

    /**
     * The slug is used to identify this logger in various places.
     * We use the name of the class too keep it simple.
     */
    public $slug = __CLASS__;

    /**
     * Method that returns an array with information about this logger.
     * Simple History used this method to get info about the logger at various places.
     */
    function getInfo() {

        $arr_info = array(
            "name" => "WP Mail Logger",
            "description" => "Logs mail sent by WordPress using the wp_mail-function"
        );

        return $arr_info;

    }

    /**
     * The loaded method is called automagically when Simple History is loaded.
     * Much of the init-code for a logger goes inside this method. To keep things
     * simple in this example, we add almost all our code inside this method.
     */
    function loaded() {

        /**
         * Use the "wp_mail" filter to log emails sent with wp_mail()
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

    }
}

// Tell Simple History that we have a new logger available
// $simpleHistory->register_logger("wp_mail_logger");

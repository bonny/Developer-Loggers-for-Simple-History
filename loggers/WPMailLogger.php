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
            "description" => "Logs mail sent by WordPress using the wp_mail-function",
            "messages" => array(
                "email_sent" => __( 'Sent an email to "{email_to}" with subject "{email_subject}" using wp_mail()', "simple-history" )
            )
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
        add_filter( 'wp_mail', function( $args ) {

            $context = array(
                "email_to" => $args["to"],
                "email_subject" => $args["subject"],
                "email_message" => $args["message"]
            );

            $this->infoMessage("email_sent", $context );

            return $args;

        } );

    }

    /**
	 * Get output for detailed log section
     * Make the send mail look a little bit like a real email client
	 */
	function getLogRowDetailsOutput( $row ) {

        $context = $row->context;
        $message_key = $context["_message_key"];

        $output = "";

        $output .= '<table class="SimpleHistoryLogitem__keyValueTable"><tbody>';

        if ( ! empty( $context["email_to"] ) ) {

            $output .= sprintf(
                '<tr>
                    <td>To</td>
                    <td>%1$s</td>
                </tr>',
                esc_html( $context["email_to"] )
            );

        }

        if ( ! empty( $context["email_subject"] ) ) {

            $output .= sprintf(
                '<tr>
                    <td>Subject</td>
                    <td>%1$s</td>
                </tr>',
                esc_html( $context["email_subject"] )
            );

        }

        if ( ! empty( $context["email_message"] ) ) {

            $output .= sprintf(
                '<tr>
                    <td>Body</td>
                    <td>%1$s</td>
                </tr>',
                nl2br( esc_html( $context["email_message"] ) )
            );

        }

        $output .= '</tbody></table>';

        return $output;

    }

}

// Tell Simple History that we have a new logger available
// $simpleHistory->register_logger("wp_mail_logger");

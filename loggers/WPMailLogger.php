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
                "email_sent" => __( 'Sent an email using wp_mail()', "simple-history" )
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
         * $args = apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) );
         */
        /*
        add_filter( 'wp_mail', function( $args ) {

            $context = array(
                "email_to" => $args["to"],
                "email_subject" => $args["subject"],
                "email_message" => $args["message"]
            );

            $this->infoMessage("email_sent", $context );

            return $args;

        } );
        */


        /*
        * Fires after PHPMailer is initialized.
       	* @param PHPMailer &$phpmailer The PHPMailer instance, passed by reference.
       	do_action_ref_array( 'phpmailer_init', array( &$phpmailer ) );
        */
        add_action('phpmailer_init', function( $phpmailer ) {

            $context = array(
                "to" => $phpmailer->getToAddresses(),
                "from" => $phpmailer->From,
                "fromName" => $phpmailer->FromName,
                "subject" => $phpmailer->Subject,
                "body" => $phpmailer->Body
            );

            $this->infoMessage("email_sent", $context );

        });

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

        if ( ! empty( $context["from"] ) ) {

            $output .= sprintf(
                '<tr>
                    <td>From</td>
                    <td>%1$s</td>
                </tr>',
                esc_html( $context["from"] )
            );

        }

        if ( ! empty( $context["to"] ) ) {

            $to_arr = json_decode( $context["to"] );
            $to_html = "";

            // $one_to = [ address, name ]
            foreach ( $to_arr as $one_to ) {
                $to_html .= sprintf( '%1$s, ', esc_html( $one_to[0] ) );
            }

            $to_html = preg_replace( '!, $!', '', $to_html );

            $output .= sprintf(
                '<tr>
                    <td>To</td>
                    <td>%1$s</td>
                </tr>',
                $to_html
            );

        }

        if ( ! empty( $context["subject"] ) ) {

            $output .= sprintf(
                '<tr>
                    <td>Subject</td>
                    <td>%1$s</td>
                </tr>',
                esc_html( $context["subject"] )
            );

        }

        if ( ! empty( $context["body"] ) ) {

            $output .= sprintf(
                '<tr>
                    <td>Body</td>
                    <td>%1$s</td>
                </tr>',
                nl2br( esc_html( $context["body"] ) )
            );

        }

        $output .= '</tbody></table>';

        return $output;

    }

}

// Tell Simple History that we have a new logger available
// $simpleHistory->register_logger("wp_mail_logger");

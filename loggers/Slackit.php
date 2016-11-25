<?php

/**
 * Post to slack yo
 * Not really a logger, since it does not log anything, but it's more easy to maintain if we pretend it is.. :)
 */
 class Slackit extends SimpleLogger {

     public $slug = __CLASS__;

     function getInfo() {

         $arr_info = array(
             "name" => "Slack",
             "description" => "Posts all logged events to a Slack channel",
             "capability" => "manage_options"
         );

         return $arr_info;

     }

     function loaded() {

        add_action( "simple_history/developer_loggers/before_plugins_table", array( $this, "settings" ) );

        // Prio 100 so we run late and give other filters chance to run
        add_filter( "simple_history/log/inserted", array( $this, "on_log_insert_context_slackit" ), 100, 3 );

        $this->maybe_save_settings();

     }

     function maybe_save_settings() {

        if ( isset( $_POST[ "{$this->slug}_nonce" ] ) && check_admin_referer( "{$this->slug}_save_settings", "{$this->slug}_nonce" ) ) {

            $input_name = sprintf( '%1$s_webhook_url', $this->slug );

            $webhook_url = isset( $_POST[$input_name] ) ? filter_var( $_POST[$input_name], FILTER_VALIDATE_URL ) : "" ;

            $settings = $this->get_settings();

            $settings["webhook_url"] = $webhook_url;

            $this->save_settings( $settings );

        }

     }

     function save_settings( $settings ) {

         update_option( "{$this->slug}_settings", $settings );

     }

     function get_settings() {

         $settings = get_option( "{$this->slug}_settings" );

         if ( ! is_array( $settings ) ) {
             $settings = array();
         }

        /**
         * Filter to modify the settings of the slackit logger
         * Setting is array with info like:
         * Array (
         *  [webhook_url] => "https://hooks.slack.com/services/blabla/buhu/woopwoop"
         * )
         */
        $settings = apply_filters( "simple_history/developer_loggers/slackit/settings", $settings );

        return $settings;

     }

     function settings() {

        $input_name = sprintf( '%1$s_webhook_url', $this->slug );

        $settings = $this->get_settings();
        $webhook_url = isset( $settings["webhook_url"] ) ? $settings["webhook_url"] : "";

        ?>

        <h2>Slack settings</h2>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="<?php echo $input_name ?>">WebHook</label></th>
                    <td>
                        <p>
                            <input value="<?php echo esc_attr( $webhook_url ) ?>" class="regular-text ltr" type="url" id="<?php echo $input_name ?>" name="<?php echo $input_name ?>" placeholder="https://hooks.slack.com/services/ABC123/B123456/O4j88fj33bbbbjjkkfssd">
                        </p>
                        <p class="description">
                            Create an <a href="https://my.slack.com/services/new/incoming-webhook/">incoming WebHook</a>
                            and then paste the WebHook URL here.
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php
        wp_nonce_field( "{$this->slug}_save_settings", "{$this->slug}_nonce" );
        ?>

        <?php

     }

     function on_log_insert_context_slackit( $context, $data, $logger ) {

        // do not log messages from HTTP_logger, beacuse that would cause infinite loops
        if ( isset( $logger->slug ) && $logger->slug == "HTTP_Logger" ) {
            return $context;
        }

        $settings = $this->get_settings();
        $slack_webhook_url = isset( $settings["webhook_url"] ) ? $settings["webhook_url"] : "";

         $remote_addr = empty( $context["_server_remote_addr"] ) ? "" : $context["_server_remote_addr"];
         $level = empty( $data["level"] ) ? "" : $data["level"];
         $message = SimpleLogger::interpolate( $data["message"], $context );
         $initiator = empty( $data["initiator"] ) ? "" : $data["initiator"];

         $color = "";
         switch ( $level ) {

             case "debug":
                 $color = "#CEF6D8";
                 break;

             case "info":
                 $color = "white";
                 break;

             case "notice":
                 $color = "#FFFFE0";
                 break;

             case "warning":
                 $color = "#F7D358";
                 break;

             case "error":
                 $color = "#F79F81";
                 break;

             case "critical":
                 $color = "#FA5858";
                 break;

             case "alert":
                 $color = "#c74545";
                 break;

             case "emergency":
                 $color = "#610B0B";
                 break;

         }

         $fields = array(
             array(
                 "title" => "Site",
                 "value" => home_url(),
                 "short" => true
             ),
             array(
                 "title" => "IP address",
                 "value" => sprintf( 'https://ipinfo.io/%s', $remote_addr ),
                 "short" => true
             ),
         );

         // Make a fake row so we can use functions directly on the logger
         $row = (object) $data;
         $row->context = $context;

         // Don't want it to output "you"
         // + Disable relative time output in header
         // @TODO: remove these filters when our function calls are done
         add_filter( "simple_history/header_initiator_use_you", "__return_false" );
         add_filter( "simple_history/user_logger/plain_text_output_use_you", "__return_false" );

         // Remove the date part of the section, because slack also shows date
         add_filter( "simple_history/row_header_date_output", "__return_empty_string" );

         // Contains both user and date, too much info?
         $header_html = $logger->getLogRowHeaderOutput( $row );
         $plain_text_html = $logger->getLogRowPlainTextOutput( $row );
         $sender_image = "";
         $sender_image_html = "";
         $initiator_text = "";

         switch ( $initiator ) {

             case "wp":
                 $initiator_text .= 'WordPress';
                 $sender_image = "https://s.w.org/about/images/logos/wordpress-logo-32-blue.png";
                 break;

             case "wp_cli":
                 $initiator_text .= 'WP-CLI';
                 $sender_image = "https://s.w.org/about/images/logos/wordpress-logo-32-blue.png";
                 break;

             case "wp_user":
                 $initiator_wp_user = trim( strip_tags( $header_html ) );
                 $initiator_wp_user = str_replace( "\n", " ", $initiator_wp_user );
                 $initiator_text = $initiator_wp_user;

                 // We need to post plain image to slack so just get it through a regexp.
                 // <img alt='' src='http://2.gravatar.com/avatar/e57939a1ce063c7619aceda8be6fe04b?s=32&#038;d=mm&#038;r=pg' srcset='http://2.gravatar.com/avatar/e57939a1ce063c7619aceda8be6fe04b?s=64&amp;d=mm&amp;r=pg 2x' class='avatar avatar-32 photo' height='32' width='32' />
                 $sender_image_html = $logger->getLogRowSenderImageOutput( $row );
                 $image_arr = json_decode( json_encode( simplexml_load_string( $sender_image_html ) ), true );
                 $sender_image = empty( $image_arr["@attributes"]["src"] ) ? "" : $image_arr["@attributes"]["src"];
                 $sender_image = str_replace( "s=32", "s=64", $sender_image );

                 break;

             case "web_user":
                 $initiator_text .= "Anonymous web user";
                 $sender_image = "http://www.gravatar.com/avatar/00000000000000000000000000000000?d=mm&f=y";
                 break;

             case "other":
                 $initiator_text = 'Other';
                 break;

             default:
                 $initiator_text = $initiator;

         }

         // Use site icon as the thumb for this event
         $author_icon = "";
         if ( function_exists( "get_site_icon_url" ) ) {
             $author_icon = get_site_icon_url( 512 );
         }

         // Clear possible shitespace that is left, because removed tags and newlines left etc.
         $initiator_text = preg_replace( '/\s+/', ' ', $initiator_text );

         $title = html_entity_decode( strip_tags( $plain_text_html ) );

         $item_permalink = admin_url( "index.php?page=simple_history_page" );
         $item_permalink .= "#item/{$logger->lastInsertID}";

         $arr_slack_data = array(
             "username" => "WordPress Simple History",
             "icon_url" => "http://simple-history.s3-website.eu-central-1.amazonaws.com/images/simple-history-icon-32.png",
             //"text" => html_entity_decode( strip_tags( $plain_text_html ) ), // 'An event was logged',
             "attachments" => array(
                 array(
                     "fallback" => "$initiator_text: " . $title,
                     "thumb_url" => $author_icon,
                     "author_name" => $initiator_text,
                     // "author_link" => home_url(),
                     "author_icon" => $sender_image,
                     // An optional value that can either be one of good, warning, danger, or any hex color code (eg. #439FE0). This value is used to color the border along the left side of the message attachment.
                     "color" => $color,
                     "title" => $title,
                     //"text" => $title,
                     "title_link" => $item_permalink,
                     "fields" => $fields,
                 ) // attachments
             )
         );

         $post_args = array(
             'blocking' => false,
             'timeout' => 0.01,
             'body' => json_encode( $arr_slack_data )
         );

         wp_remote_post( $slack_webhook_url, $post_args );

         return $context;

     }

}

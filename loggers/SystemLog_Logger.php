<?php

class SystemLog_Logger extends SimpleLogger {

    public $slug = __CLASS__;

    function getInfo() {

        return array(
            "name" => "System Logger",
            "description" => "Add all logged messages to PHP's system logger",
            "capability" => "manage_options",
            "messages" => array(),
            "labels" => array(),
        );

    }

    function loaded() {

        /**
         * Filter the context to store for this event/row
         *
         * @since 2.0.29
         *
         * @param array $context Array with all context data to store. Modify and return this.
         * @param array $data Array with data used for parent row.
         */
        // $context = apply_filters("simple_history/log_insert_context", $context, $data);
        add_filter( "simple_history/log_insert_context", array( $this, "on_log_insert_context" ), 10, 2 );

    }

    function on_log_insert_context( $context, $data ) {

        // This is the raw format, so we must generate the nice format outself
        error_log( SimpleHistory::json_encode( $data ) );
        error_log( SimpleHistory::json_encode( $context ) );

        $message = "";

        if ( ! empty( $data["level"] ) ) {
            $message .= $data["level"] . ": " ;
        }

        if ( ! empty( $context["_user_login"] ) ) {
            $message .= $context["_user_login"] . ": " ;
        }

        if ( ! empty( $context["_server_remote_addr"] ) ) {
            $message .= " IP " . $context["_server_remote_addr"] . ": " ;
        }

        $message .= $this->interpolate( $data["message"], $context );
        error_log( $message );

        return $context;

    }

}

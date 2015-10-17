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
         * Fired when Simple History filters the context to store for this event/row
         *
         * @param array $context Array with all context data to store. Modify and return this.
         * @param array $data Array with data used for parent row.
         */
        add_filter( "simple_history/log_insert_context", array( $this, "on_log_insert_context" ), 10, 2 );

    }

    function on_log_insert_context( $context, $data ) {

        $remote_addr = empty( $context["_server_remote_addr"] ) ? "" : $context["_server_remote_addr"];
        $level = empty( $data["level"] ) ? "" : $data["level"];
        $user_login = empty( $context["_user_login"] ) ? "" : $context["_user_login"];
        $message = $this->interpolate( $data["message"], $context );

        $log_message = sprintf(
            'WordPress Simple History: %1$s %2$s %3$s %4$s',
            $remote_addr, // 1
            $level, // 2
            $user_login, // 3
            $message // 4
        );

        error_log( $log_message );

        // Comment out this to store some more debug info
        // error_log( SimpleHistory::json_encode( $data ) );
        // error_log( SimpleHistory::json_encode( $context ) );

        return $context;

    }

}

<?php

class AvailableUpdatesLogger extends SimpleLogger {

    public $slug = __CLASS__;

    function getInfo() {

        $arr_info = array(
            "name" => "AvailableUpdatesLogger",
            "description" => "Logs available updates",
            "capability" => "manage_options",
            "messages" => array(),
            "labels" => array(),
        );

        return $arr_info;

    }

    function loaded() {

        #add_action("init", array( $this, "log_usage" ), 10, 2);

    }


}

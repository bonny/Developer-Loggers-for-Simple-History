<?php

class CPUUsageLogger extends SimpleLogger {

    public $slug = __CLASS__;

    function getInfo() {

        $arr_info = array(
            "name" => "CPU Usage Logger",
            "description" => "Logs CPU usage",
            "capability" => "manage_options",
            "messages" => array(),
            "labels" => array(),
        );

        return $arr_info;

    }

    function loaded() {

        // sys_getloadav() does not exist in windows, so don't do anything if it does not exist
        // https://wordpress.org/support/topic/sys_getloadavg-php-windows
        if ( ! function_exists("sys_getloadavg") ) {
            return;
        }

        add_action("init", array( $this, "log_usage" ), 10, 2);

    }

    function log_usage() {

        // 1, 5, 15
        $load = sys_getloadavg();

        SimpleLogger()->debug(
            '
            CPU usage last 1 minute: {cpu_1_minutes},
            CPU usage last 5 minutes: {cpu_5_minutes},
            CPU usage last 15 minutes: {cpu_15_minutes},
            ',
            array(
                "cpu_1_minutes" => round( $load[0], 4 ),
                "cpu_5_minutes" => round( $load[1], 4),
                "cpu_15_minutes" => round( $load[2], 4),
            )
        );

    }

}

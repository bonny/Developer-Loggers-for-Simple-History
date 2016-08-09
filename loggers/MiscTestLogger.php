<?php

class MiscTestLogger extends SimpleLogger {

    public $slug = __CLASS__;

    function getInfo() {

        $arr_info = array(
            "name" => "Misc Logger",
            "description" => "Logs misc things - mostly for testing!",
            "capability" => "manage_options",
            "messages" => array(),
            "labels" => array(), // end search
        );

        return $arr_info;

    }

    function loaded() {

        if ( ! function_exists("sys_getloadavg") ) {
            return;
        }

        add_action("xinit", function() {

        	$load = sys_getloadavg(); // 1, 5, 15

        	SimpleLogger()->debug(
        		'
        		CPU usage last 1 minute: {cpu_1_minutes},
        		CPU usage last 5 minutes: {cpu_5_minutes},
        		CPU usage last 15 minutes: {cpu_15_minutes},
        		',
        		array(
        			"cpu_1_minutes" => $load[0],
        			"cpu_5_minutes" => $load[1],
        			"cpu_15_minutes" => $load[2],
        		)
        	);

        	SimpleLogger()->debug(
        		"Free disk space on harddrive where WordPress is installed ({abspath}): {disk_free}",
        		array(
        			"disk_free" => size_format( disk_free_space( dirname(ABSPATH) ) ),
        			"abspath" => ABSPATH
        		)
        	);


        }, 10, 2);

        /**
         * Log free space on harddrives on server
         */
        add_action("xinit", function() {

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

    }

}

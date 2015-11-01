<?php


class WPCron extends SimpleLogger {

    public $slug = __CLASS__;

    function getInfo() {

        // error_log("AvailableUpdatesLogger: getInfo()");

        $arr_info = array(
            "name" => "WPCron",
            "description" => "Logs WP Crons",
            "capability" => "manage_options",
            "messages" => array(
                "cron_runned" => __( 'Found an update to WordPress.', "simple-history" ),
            ),
            "labels" => array(),
        );

        return $arr_info;

    }

    function loaded() {

        if ( ! defined( 'DOING_CRON' ) || ! DOING_CRON ) {
            return;
        }

        $crons = get_option('cron');

        $gmt_time = microtime( true );

        foreach ( $crons as $timestamp => $cronhooks ) {

            if ( $timestamp > $gmt_time ) {
            	break;
            }
            /*
            cron	{
                "1446328933": {
                    "um_hourly_scheduled_events": { // $hook
                        "40cd750bba9870f18aada2478b24840a": { // keys
                            "schedule": "hourly", // vals
                            "args": [], // vals
                            "interval": 3600 // vals
                        }
                    }
                },
                "1446329210": {
                    "simple_history\/maybe_purge_db": {
                        "40cd750bba9870f18aada2478b24840a": {
                            "schedule": "daily",
                            "args": [],
                            "interval": 86400
                        }
                    }
                },
            */
            foreach ( $cronhooks as $hook => $keys ) {

                foreach ( $keys as $k => $v ) {

                    /*
                    do_action_ref_array( $hook, $v['args'] );
                    do_action_ref_array( 'simple_history/maybe_purge_db', [] )
                    */

                    // When we get here, WordPress is probably about to do a cron job
                    add_action( $hook, function() use( $keys, $k, $v ) {

                        $this->debug( 'Did cron job with hook "{cron_hook}"', array(
                            "cron_hook" => current_filter(),
                            "keys" => $keys,
                            "k" => $k,
                            "v" => $v
                        ) );

                    } );


                }

            }


        }
        /*

        define('DOING_CRON', true);
        if ( defined( 'DOING_CRON' ) )

        set_transient( 'doing_cron', $doing_wp_cron );

        delete_transient( 'doing_cron' );

        https://github.com/WordPress/WordPress/blob/master/wp-cron.php

        Called for each cron hook being fired?
        wp_unschedule_event( $timestamp, $hook, $args );

        $cron = get_option('cron');

        */

    }

} // class

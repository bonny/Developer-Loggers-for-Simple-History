<?php


class WPCron extends SimpleLogger {

    public $slug = __CLASS__;

    function getInfo() {

        $arr_info = array(
            "name" => "WPCron",
            "description" => "Logs WP Crons. Can be very useful for debugging purposed. Also: can be highly annoying too. Use wisely!",
            "capability" => "manage_options",
            "messages" => array(
                "did_cron" => __(  'Did cron job with action hook "{cron_hook}"', "simple-history" ),
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
                    // Add action to same hook, but use a pretty high filter so our logging will
                    // probably take place after the real job has run
                    add_action( $hook, function() use( $keys, $k, $v ) {

                        $this->debugMessage("did_cron", array(
                            "cron_hook" => current_filter(),
                            "keys" => $keys,
                            "k" => $k,
                            "v" => $v
                        ) );

                    }, 50 );


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

<?php

class SimpleHistory_DeveloperLoggers {

    // Instance of simpleHistory
    private $simpleHistory;

    public $slug = "DeveloperLoggers";

    function init( $simpleHistory ) {

        $this->simpleHistory = $simpleHistory;

        $this->add_settings_tab();

        $this->maybe_save_settings();

        $this->load_enabled_loggers();

    }

    function load_enabled_loggers() {

        $enabled_loggers = $this->get_enabled_loggers();

        foreach ( $enabled_loggers as $logger_slug => $one_enabled_logger ) {

            $file_with_path_and_extension = __DIR__ . "/loggers/" . "{$logger_slug}.php";

            if ( file_exists( $file_with_path_and_extension ) ) {
                include_once $file_with_path_and_extension;
                $this->simpleHistory->register_logger( $logger_slug );
            }

        }

    }

    function maybe_save_settings() {

        $action = isset( $_POST["{$this->slug}_action"] ) ? $_POST["{$this->slug}_action"] : "";

        if ( ! $action ) {
            return;
        }


        if ( "save_settings" == $action && check_admin_referer( 'save_settings', "{$this->slug}_nonce" ) ) {

            $new_enabled_loggers = isset( $_POST["enabled_loggers"] ) ? (array) $_POST["enabled_loggers"] : array();

            $settings = $this->get_settings();

            // First unset all previos anabled loggers, because we don't get any new value for those
            foreach ( $settings["enabled_loggers"] as $slug => $vals ) {
                $settings["enabled_loggers"][$slug]["enabled"] = false;
            }

            foreach ( $new_enabled_loggers as $one_new_logger_slug ) {

                if ( empty( $settings["enabled_loggers"][ $one_new_logger_slug ] ) ) {
                    $settings["enabled_loggers"][ $one_new_logger_slug ] = array();
                }

                $settings["enabled_loggers"][ $one_new_logger_slug ]["enabled"] = true;

            }

            $this->save_settings( $settings );

            add_action( 'admin_notices', function() {
                ?>
                <div class="updated">
                    <p><?php _e( 'Settings saved', 'simple-history' ); ?></p>
                </div>
                <?php
            } );

        }

    }

    function save_settings( $settings ) {

        $settings = update_option( "{$this->slug}_settings", $settings );

        do_action( "simple_history/developer_loggers/save_settings" );

    }

    function get_settings() {

        $settings = get_option( "{$this->slug}_settings" );

        if ( ! $settings ) {

            // First install (or settings does not exist for other unknown reason)
            $settings = array(
                "enabled_loggers" => array()
            );

        }

        return $settings;

    }

    /**
     * Return array with enabled loggers. Format is like:
     *  Array
     *  (
     *      [FourOhFourLogger] => Array
     *          (
     *              [enabled] => 1
     *          )
     *
     *      [AvailableUpdatesLogger] => Array
     *          (
     *              [enabled] => 1
     *          )
     *
     *      [Slackit] => Array
     *          (
     *              [enabled] => 1
     *          )
     *  )
     */
    function get_enabled_loggers() {

        $settings = $this->get_settings();

        $enabled_loggers = (array) $settings["enabled_loggers"];

        /**
         * Filter enabled loggers. Use this filter to automagically enable or disable loggers
         *
         * @param array $enabled_loggers
         * @return array with enabled loggers info
         */
        $enabled_loggers = apply_filters( "simple_history/developer_loggers/enabled_loggers", $enabled_loggers );

        $enabled_loggers = array_filter( $enabled_loggers, function( $vals ) {
            return $vals["enabled"];
        } );

        return $enabled_loggers;

    }

    function is_logger_enabled( $logger_slug ) {

        $is_enabled = false;

        $enabled_loggers = $this->get_enabled_loggers();

        if ( isset( $enabled_loggers[$logger_slug] ) &&  $enabled_loggers[$logger_slug]["enabled"] ) {
             $is_enabled = true;
        }

        return $is_enabled;

    }

    /**
     * Return all loggers that are available
     */
    function get_available_loggers() {

        $iterator = new FilesystemIterator( __DIR__ . "/loggers/" );
        $filter = new RegexIterator( $iterator, '/.php$/' );

        $filelist = array();
        foreach ( $filter as $entry ) {
            $filelist[] = $entry->getPathname();
        }

        $arr_loggers_info = array();

        foreach ( $filelist as $file ) {

            $file_basename = basename( $file, ".php" );
            include_once $file;
            if ( ! class_exists( $file_basename ) ) {
                continue;
            }

            $file_instance = new $file_basename( $this->simpleHistory );
            if ( ! is_subclass_of( $file_instance, "SimpleLogger" ) ) {
                continue;
            }

            $arr_loggers_info[] = array(
                "pathname" => $file,
                "basename" => $file_basename,
                "slug" => $file_instance->slug,
                "info" => $file_instance->getInfo(),
            );

        }

        // Sort by name
        usort( $arr_loggers_info, function( $a, $b ) {
            return strcmp( $a["info"]["name"], $b["info"]["name"] );
        } );

        return $arr_loggers_info;

    }

    function add_settings_tab() {

        $this->simpleHistory->registerSettingsTab( array(
            "slug" => $this->slug,
            "name" => __( "Developer loggers", "simple-history" ),
            "function" => array( $this, "settings_output" ),
        ) );

    }

    /**
     * Output HTML for the settings tab
     */
    function settings_output() {

        include __DIR__ . "/templates/settings.php";

    }

}

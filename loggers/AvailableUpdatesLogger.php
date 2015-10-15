<?php

class AvailableUpdatesLogger extends SimpleLogger {

    public $slug = __CLASS__;

    function getInfo() {

        // error_log("AvailableUpdatesLogger: getInfo()");

        $arr_info = array(
            "name" => "AvailableUpdatesLogger",
            "description" => "Logs available updates",
            "capability" => "manage_options",
            "messages" => array(
                "core_update_available" => __( 'WordPress version {wp_core_new_version} is available (you have {wp_core_current_version})', "simple-history" ),
                "plugin_update_available" => __( 'Plugin "{plugin_name}" has an update available (version available is {plugin_new_version}, installed version is {plugin_current_version})', "simple-history" ),
            ),
            "labels" => array(),
        );

        return $arr_info;

    }

    function loaded() {

        // When WP is done checking for core udptes it sets a site transient called "update_core"
        // set_site_transient( 'update_core', null ); // Uncomment to test
        add_action( "set_site_transient_update_core", array( $this, "on_setted_update_core_transient" ), 10, 1 );

        // Dito for plugins
        // set_site_transient( 'update_plugins', null ); // Uncomment to test
        add_action( "set_site_transient_update_plugins", array( $this, "on_setted_update_plugins_transient" ), 10, 1 );

        add_action( "set_site_transient_update_themes", array( $this, "on_setted_update_update_themes" ), 10, 1 );

    }

    function on_setted_update_core_transient( $updates ) {

        global $wp_version;

        $last_version_checked = get_option( "simplehistory_{$this->slug}_wp_core_version_available" );
        $new_wp_core_version = $updates->updates[0]->current; // The new WP core version

        // Some plugins can mess with version, so get fresh from the version file.
        require_once ABSPATH . WPINC . '/version.php';

        // If found version is same version as we have logged about before then don't continue
        if ( $last_version_checked == $new_wp_core_version ) {
            return;
        }

        // is WP core update available?
        if ( 'upgrade' == $updates->updates[0]->response ) {

            $this->noticeMessage( "core_update_available", array(
                "wp_core_current_version" => $wp_version,
                "wp_core_new_version" => $new_wp_core_version
            ) );

            // Store updated version available, so we don't log that version again
            update_option( "simplehistory_{$this->slug}_wp_core_version_available", $new_wp_core_version );

        }

    }


    function on_setted_update_plugins_transient( $updates ) {

        if ( empty( $updates->response ) || ! is_array( $updates->response ) ) {
            return;
        }

        // If we only want to notify about active plugins
        /*
        $active_plugins = get_option( 'active_plugins' );
        $active_plugins = array_flip( $active_plugins ); // find which plugins are active
        $plugins_need_update = array_intersect_key( $plugins_need_update, $active_plugins ); // only keep plugins that are active
        */

        //update_option( "simplehistory_{$this->slug}_wp_core_version_available", $new_wp_core_version );
        $option_key = "simplehistory_{$this->slug}_plugin_updates_available";
        $checked_updates = get_option( $option_key );

        if ( ! is_array( $checked_updates ) ) {
            $checked_updates = array();
        }

        // File needed plugin API
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

        // For each available update
        foreach ( $updates->response as $key => $data ) {

            $plugin_info = get_plugin_data( WP_PLUGIN_DIR . "/" . $key );
            //$remote_plugin_info = plugins_api( 'plugin_information', array( 'slug' => $data->slug ) );

            $plugin_new_version = isset( $data->new_version ) ? $data->new_version : "";

            // check if this plugin and this version has been checked/logged already
            if ( ! array_key_exists( $key, $checked_updates ) ) {
                $checked_updates[ $key ] = array(
                    "checked_version" => null
                );
            }

            if ( $checked_updates[ $key ]["checked_version"] == $plugin_new_version ) {
                // This version has been checked/logged already
                continue;
            }

            $checked_updates[ $key ]["checked_version"] = $plugin_new_version;

            $this->noticeMessage( "plugin_update_available", array(
                "plugin_name" => isset( $plugin_info['Name'] ) ? $plugin_info['Name'] : "",
                "plugin_current_version" => isset( $plugin_info['Version'] ) ? $plugin_info['Version'] : "",
                "plugin_new_version" => $plugin_new_version,
                //"plugin_info" => $plugin_info,
                // "remote_plugin_info" => $remote_plugin_info,
                // "active_plugins" => $active_plugins,
                // "updates" => $updates,
            ) );

        } // foreach

        update_option( $option_key, $checked_updates );

    } // function


    function on_setted_update_update_themes( $updates ) {

        if ( empty( $updates->response ) || ! is_array( $updates->response ) ) {
            return;
        }

        //update_option( "simplehistory_{$this->slug}_wp_core_version_available", $new_wp_core_version );
        $option_key = "simplehistory_{$this->slug}_theme_updates_available";
        $checked_updates = get_option( $option_key );

        if ( ! is_array( $checked_updates ) ) {
            $checked_updates = array();
        }

        // For each available update
        foreach ( $updates->response as $key => $data ) {

            $theme_info = wp_get_theme( $key );

            $message .= "\n" . sprintf( __( "Theme: %s is out of date. Please update from version %s to %s", "wp-updates-notifier" ), $theme_info['Name'], $theme_info['Version'], $data['new_version'] ) . "\n";

            $settings['notified']['theme'][$key] = $data['new_version']; // set theme version we are notifying about

            $theme_new_version = isset( $data["new_version"] ) ? $data["new_version"] : "";

            // check if this plugin and this version has been checked/logged already
            if ( ! array_key_exists( $key, $checked_updates ) ) {
                $checked_updates[ $key ] = array(
                    "checked_version" => null
                );
            }

            if ( $checked_updates[ $key ]["checked_version"] == $theme_new_version ) {
                // This version has been checked/logged already
                continue;
            }

            $checked_updates[ $key ]["checked_version"] = $theme_new_version;

            $this->noticeMessage( "plugin_update_available", array(
                "plugin_name" => isset( $theme_info['Name'] ) ? $theme_info['Name'] : "" ,
                "plugin_current_version" => isset( $theme_info['Version'] ) ? $theme_info['Version'] : "",
                "plugin_new_version" => $theme_new_version,
                //"plugin_info" => $plugin_info,
                // "remote_plugin_info" => $remote_plugin_info,
                // "active_plugins" => $active_plugins,
                // "updates" => $updates,
            ) );

        } // foreach

        update_option( $option_key, $checked_updates );

    } // function

} // class

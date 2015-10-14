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
                "core_update_available" => __("WordPress version {wp_core_new_version} is available (you have {wp_core_current_version})", "simple-history")
            ),
            "labels" => array(),
        );

        return $arr_info;

    }

    function loaded() {

        // WP is done checking for core udptes it sets a site transient called "update_core"
        add_action("set_site_transient_update_core", array( $this, "on_setted_update_core_transient"), 10, 1);

    }

    function on_setted_update_core_transient( $updates ) {

        global $wp_version;

        $last_version_checked = get_option("simplehistory_{$this->slug}_wp_core_version_available");
        $new_wp_core_version = $updates->updates[0]->current; // The new WP core version

        // Some plugins can mess with version, so get fresh from the version file.
        require_once( ABSPATH . WPINC . '/version.php' );

        // If found version is same version as we have logged about before then don't continue
        if ( $last_version_checked == $new_wp_core_version ) {
            return;
        }

        // is WP core update available?
        if ( 'upgrade' == $updates->updates[0]->response ) {

            $this->debugMessage("core_update_available", array(
                "wp_core_current_version" => $wp_version,
                "wp_core_new_version" => $new_wp_core_version
            ));

            // Store updated version available, so we don't log that version again
            update_option("simplehistory_{$this->slug}_wp_core_version_available", $new_wp_core_version);

        }

    }


}

/**
 * Check to see if any plugin updates.
 *
 * @param string $message     holds message to be sent via notification
 * @param int    $allOrActive should we look for all plugins or just active ones
 *
 * @return bool
 */
function plugins_update_check( &$message, $allOrActive ) {
    global $wp_version;
    $cur_wp_version = preg_replace( '/-.*$/', '', $wp_version );
    $settings       = self::getSetOptions( self::OPT_FIELD ); // get settings
    do_action( "wp_update_plugins" ); // force WP to check plugins for updates
    $update_plugins = get_site_transient( 'update_plugins' ); // get information of updates
    if ( !empty( $update_plugins->response ) ) { // any plugin updates available?
        $plugins_need_update = $update_plugins->response; // plugins that need updating
        if ( 2 == $allOrActive ) { // are we to check just active plugins?
            $active_plugins      = array_flip( get_option( 'active_plugins' ) ); // find which plugins are active
            $plugins_need_update = array_intersect_key( $plugins_need_update, $active_plugins ); // only keep plugins that are active
        }
        $plugins_need_update = apply_filters( 'sc_wpun_plugins_need_update', $plugins_need_update ); // additional filtering of plugins need update
        if ( count( $plugins_need_update ) >= 1 ) { // any plugins need updating after all the filtering gone on above?
            require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' ); // Required for plugin API
            require_once( ABSPATH . WPINC . '/version.php' ); // Required for WP core version
            foreach ( $plugins_need_update as $key => $data ) { // loop through the plugins that need updating
                $plugin_info = get_plugin_data( WP_PLUGIN_DIR . "/" . $key ); // get local plugin info
                $info        = plugins_api( 'plugin_information', array( 'slug' => $data->slug ) ); // get repository plugin info
                $message .= "\n" . sprintf( __( "Plugin: %s is out of date. Please update from version %s to %s", "wp-updates-notifier" ), $plugin_info['Name'], $plugin_info['Version'], $data->new_version ) . "\n";
                $message .= "\t" . sprintf( __( "Details: %s", "wp-updates-notifier" ), $data->url ) . "\n";
                $message .= "\t" . sprintf( __( "Changelog: %s%s", "wp-updates-notifier" ), $data->url, "changelog/" ) . "\n";
                if ( isset( $info->tested ) && version_compare( $info->tested, $wp_version, '>=' ) ) {
                    $compat = sprintf( __( 'Compatibility with WordPress %1$s: 100%% (according to its author)' ), $cur_wp_version );
                }
                elseif ( isset( $info->compatibility[$wp_version][$data->new_version] ) ) {
                    $compat = $info->compatibility[$wp_version][$data->new_version];
                    $compat = sprintf( __( 'Compatibility with WordPress %1$s: %2$d%% (%3$d "works" votes out of %4$d total)' ), $wp_version, $compat[0], $compat[2], $compat[1] );
                }
                else {
                    $compat = sprintf( __( 'Compatibility with WordPress %1$s: Unknown' ), $wp_version );
                }
                $message .= "\t" . sprintf( __( "Compatibility: %s", "wp-updates-notifier" ), $compat ) . "\n";
                $settings['notified']['plugin'][$key] = $data->new_version; // set plugin version we are notifying about
            }
            self::getSetOptions( self::OPT_FIELD, $settings ); // save settings
            return true; // we have plugin updates return true
        }
    }
    else {
        if ( 0 != count( $settings['notified']['plugin'] ) ) { // is there any plugin notifications?
            $settings['notified']['plugin'] = array(); // set plugin notifications to empty as all plugins up-to-date
            self::getSetOptions( self::OPT_FIELD, $settings ); // save settings
        }
    }
    return false; // No plugin updates so return false
}


/**
 * Check to see if any theme updates.
 *
 * @param string $message     holds message to be sent via notification
 * @param int    $allOrActive should we look for all themes or just active ones
 *
 * @return bool
 */
function themes_update_check( &$message, $allOrActive ) {
    $settings = self::getSetOptions( self::OPT_FIELD ); // get settings
    do_action( "wp_update_themes" ); // force WP to check for theme updates
    $update_themes = get_site_transient( 'update_themes' ); // get information of updates
    if ( !empty( $update_themes->response ) ) { // any theme updates available?
        $themes_need_update = $update_themes->response; // themes that need updating
        if ( 2 == $allOrActive ) { // are we to check just active themes?
            $active_theme       = array( get_option( 'template' ) => array() ); // find current theme that is active
            $themes_need_update = array_intersect_key( $themes_need_update, $active_theme ); // only keep theme that is active
        }
        $themes_need_update = apply_filters( 'sc_wpun_themes_need_update', $themes_need_update ); // additional filtering of themes need update
        if ( count( $themes_need_update ) >= 1 ) { // any themes need updating after all the filtering gone on above?
            foreach ( $themes_need_update as $key => $data ) { // loop through the themes that need updating
                $theme_info = wp_get_theme( $key ); // get theme info
                $message .= "\n" . sprintf( __( "Theme: %s is out of date. Please update from version %s to %s", "wp-updates-notifier" ), $theme_info['Name'], $theme_info['Version'], $data['new_version'] ) . "\n";
                $settings['notified']['theme'][$key] = $data['new_version']; // set theme version we are notifying about
            }
            self::getSetOptions( self::OPT_FIELD, $settings ); // save settings
            return true; // we have theme updates return true
        }
    }
    else {
        if ( 0 != count( $settings['notified']['theme'] ) ) { // is there any theme notifications?
            $settings['notified']['theme'] = array(); // set theme notifications to empty as all themes up-to-date
            self::getSetOptions( self::OPT_FIELD, $settings ); // save settings
        }
    }
    return false; // No theme updates so return false
}

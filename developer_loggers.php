<?php
/**
 * Plugin Name: Developer Loggers for Simple History
 * Plugin URI: https://github.com/bonny/Developer-Loggers-for-Simple-History
 * GitHub URI: https://github.com/bonny/Developer-Loggers-for-Simple-History
 * Description: Loggers for Simple History that are useful for developers and admins
 * Version: 0.3.3
 * Author: Pär Thernström
 */

if ( version_compare( phpversion(), "5.4", ">=") ) {

    // ok
    include_once( __DIR__ . "/class.php" );

    /**
     * Init plugin using the add_custom_logger filter
     */
    function SimpleHistory_DeveloperLoggers_addCustomLogger( $simpleHistory ) {

        $logger = new SimpleHistory_DeveloperLoggers;
        $logger->init( $simpleHistory );

    }

    add_action( "simple_history/add_custom_logger", "SimpleHistory_DeveloperLoggers_addCustomLogger" );


} else {

    // not ok
    // user is running to old version of php, add admin notice about that
    add_action( 'admin_notices', 'SimpleHistory_DeveloperLoggers_oldVersionNotice' );

    function SimpleHistory_DeveloperLoggers_oldVersionNotice() {
        ?>
        <div class="updated error">
            <p><?php

                printf(
                    __( 'Developer Loggers for Simple History requires at least PHP 5.4 (you have version %s).', 'simple-history' ),
                    phpversion()
                );

                ?></p>
        </div>
        <?php

    }


}


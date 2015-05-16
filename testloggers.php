<?php
/**
 * Plugin Name: Simple History Testloggers
 * Plugin URI: https://github.com/bonny/WordPress-Simple-History-Testloggers
 * Description: Misc loggers to test Simple History
 * Version: 0.1
 * Author: Pär Thernström
 */

// Load and register the 404-logger
add_action("simple_history/add_custom_logger", function($simpleHistory) {

    include __DIR__ . "/loggers/FourOhFourLogger.php";
    $simpleHistory->register_logger("FourOhFourLogger");

});


add_action("simple_history/add_custom_logger", function($simpleHistory) {

    include __DIR__ . "/loggers/MiscTestLogger.php";
    $simpleHistory->register_logger("MiscTestLogger");

});

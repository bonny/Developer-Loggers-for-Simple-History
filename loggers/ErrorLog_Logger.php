<?php

class ErrorLog_Logger extends SimpleLogger {

    public $slug = __CLASS__;

    function getInfo() {

        return array(
            "name" => "ErrorLog Logger",
            "description" => "Logs PHP errors during runtime",
            "capability" => "manage_options",
            "messages" => array(

                "deprecated_function" => __(
                    'Function "{function}" is deprecated since version "{version}"! Use "{replacement}" instead.',
                    "simple-history"
                ),

                "deprecated_argument" => __(
                    'Function "{function}" was called with an argument that is deprecated since version "{version}" "{message}"',
                    "simple-history"
                ),

                "doing_it_wrong" => __(
                    'You\'re doing it wrong: "{function}" "{message}"',
                    "simple-history"
                ),
            ),
            "labels" => array(),
        );

    }

    function loaded() {

        #add_action("init", function() {
        #    var_dump($this->messages);exit;
        #});

        // Don't log things when doing the Simple History AJAX call becuase will log on and on and on...
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_GET["simple_history_api"] ) ) {
            #return;
        }

        if ( WP_DEBUG ) {
			foreach ( array( 'deprecated_function', 'deprecated_file', 'deprecated_argument', 'doing_it_wrong', 'deprecated_hook' ) as $item )
				add_action( "{$item}_trigger_error", '__return_false' );
		}

        // Log deprecated notices.
        add_action( 'deprecated_function_run',  array( &$this, 'log_function' ), 10, 3 );
		//add_action( 'deprecated_file_included', array( &$this, 'log_file'     ), 10, 4 );
		add_action( 'deprecated_argument_run',  array( &$this, 'log_argument' ), 10, 4 );
		add_action( 'doing_it_wrong_run',       array( &$this, 'log_wrong'    ), 10, 3 );
		//add_action( 'deprecated_hook_used',     array( &$this, 'log_hook'     ), 10, 4 );

        #error_reporting( 0 );
        #@ini_set( 'display_errors', 0 );

        set_error_handler( array( $this, "handle_error" ) );

        // Must test after loggers are loaded. or later.
        add_action("admin_init", function() {

            // Deprecated functions
            // js_escape("test");
            // clean_url("test");

            // Uses deprecated argument "misc" for options group name
            // register_setting( "misc", "My option name", "sanitize_callback");

            // do_shortcode_tag("shortcode_that_does_not_exist");

        });

        #var_dump($this->messages);exit;

    }

    function log_function( $function, $replacement, $version ) {

        $this->debugMessage( "deprecated_function", array(
            "function" => $function,
            "replacement" => $replacement,
            "version" => $version
        ) );

    }

    /*
    function log_file( $file, $replacement, $version, $message ) {

        $this->debug( "log_file", array(
            "function" => __FUNCTION__,
            "args" => func_get_args()
        ) );

    }
    */

    function log_argument( $function, $message, $version ) {

        $this->debugMessage( "deprecated_argument", array(
            "function" => $function,
            "message" => $message,
            "version" => $version
        ) );

    }

    function log_wrong( $function, $message, $version ) {

        $this->debugMessage( "doing_it_wrong", array(
            "function" => $function,
            "message" => $message,
            "version" => $version
        ) );

    }

    /*
    function log_hook( $hook, $replacement, $version, $message ) {

        $this->debug( "log_hook", array(
            "function" => __FUNCTION__,
            "args" => func_get_args()
        ) );

    }
    */


    function handle_error( $errno = null, $errstr = null, $errfile = null, $errline = null, $errcontext = null ) {

        // The first parameter, errno, contains the level of the error raised, as an integer.

        // The second parameter, errstr, contains the error message, as a string.

        // The third parameter is optional, errfile, which contains the filename that the error was raised in, as a string.

        // The fourth parameter is optional, errline, which contains the line number the error was raised at, as an integer.

        // The fifth parameter is optional, errcontext, which is an array that points to the active symbol table at the point the error occurred. In other words, errcontext will contain an array of every variable that existed in the scope the error was triggered in. User error handler must not modify error context.

        $errorno_str = $this->get_error_no_as_string( $errno );

        $this->noticeMessage( "php_error", array(
            "errorno_str" => $errorno_str,
            "errstr" => $errstr,
            #"errfile" => $errfile,
            #"errline" => $errline,
        ) );
        //error_log( "error logger: $errno ($errorno_string) $errstr $errfile $errline" );

        return false;

    }

    function get_error_no_as_string( $type ) {

        switch ( $type ) {
            case E_ERROR: // 1 //
                return 'E_ERROR';
            case E_WARNING: // 2 //
                return 'E_WARNING';
            case E_PARSE: // 4 //
                return 'E_PARSE';
            case E_NOTICE: // 8 //
                return 'E_NOTICE';
            case E_CORE_ERROR: // 16 //
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: // 32 //
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR: // 64 //
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING: // 128 //
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: // 256 //
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512 //
                return 'E_USER_WARNING';
            case E_USER_NOTICE: // 1024 //
                return 'E_USER_NOTICE';
            case E_STRICT: // 2048 //
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096 //
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 //
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED: // 16384 //
                return 'E_USER_DEPRECATED';
        }

        return "";

    }

}

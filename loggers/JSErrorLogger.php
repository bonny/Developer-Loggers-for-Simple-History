<?php

/**
 * Log 404-errors
 */
class JSErrorLogger extends SimpleLogger {

    /**
     * The slug is ised to identify this logger in various places.
     * We use the name of the class too keep it simple.
     */
    public $slug = __CLASS__;

    /**
	 * Return information about this logger.
     * Used to show info about the logger at various places.
	 */
	function getInfo() {

		$arr_info = array(
			"name" => "JavaScript Error Logger",
			"description" => "Logs JavaScript errors",
			"capability" => "edit_pages",
			"messages" => array(),
			"labels" => array(), // end labels
		);

		return $arr_info;

	}

    /**
     * When Simple History has loaded this logger it automagically
     * calls a loaded() function. This is where you add your actions
     * and other logger functionality.
     */
    function loaded() {

        /**
         * Log public JavaScript errors
         */
        add_action( "wp_head", array( $this, "add_js_to_header" ) );
        add_action( 'wp_ajax_simple_history_log_js_error', array( $this, "log_error" ) );
        add_action( 'wp_ajax_nopriv_simple_history_log_js_error', array( $this, "log_error" ) );

    }

    function add_js_to_header() {
        ?>
        <script>
            window.onerror = function(m, u, l) {
                // console.log('Error message: '+m+'\nURL: '+u+'\nLine Number: '+l);
                if (encodeURIComponent) {
                    var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                        img = new Image(1,1);
                    img.src = ajaxurl + "?action=simple_history_log_js_error&m=" + encodeURIComponent(m) + "&u=" + encodeURIComponent(u) + "&l=" + encodeURIComponent(l);
                }
                return true;
            }
        </script>
        <?php

    }

    // Log error with ajax
    function log_error() {

        $error = isset( $_GET["m"] ) ? $_GET["m"] : "";
        $url = isset( $_GET["u"] ) ? $_GET["u"] : "";
        $line = isset( $_GET["l"] ) ? $_GET["l"] : "";

        $context = array(
            "error" => $error,
            "url" => $url,
            "line" => $line,
            "browser" => $_SERVER["HTTP_USER_AGENT"]
        );

        SimpleLogger()->info( "Detected a JavaScript error on page '{url}': '{error}'", $context );

        exit;

    }

}

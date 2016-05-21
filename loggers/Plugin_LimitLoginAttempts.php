<?php

/**
 * Logger for the (old but still) very popular plugin Limit Login Attempts
 * https://sv.wordpress.org/plugins/limit-login-attempts/
 */
class Plugin_LimitLoginAttempts extends SimpleLogger {

    public $slug = __CLASS__;

	function getInfo() {

		$arr_info = array(
			"name" => "Plugin Limit Login Attempts",
			"description" => "",
            "name_via" => _x("Using plugin Limit Login Attempts", "PluginLimitLoginAttempts", "simple-history"),
			"capability" => "manage_options",
			"messages" => array(
				//'user_locked_out' => _x( 'User locked out', "Logger: Plugin Limit Login Attempts", "simple-history" ),
                'failed_login_whitelisted' => _x( 'Failed login attempt from whitelisted IP', "Logger: Plugin Limit Login Attempts", 'simple-history' ),
                'failed_login' => _x( 'Was locked out because too many failed login attempts', "Logger: Plugin Limit Login Attempts", 'simple-history' ),
                "cleared_ip_log" => _x( 'Cleared IP log', "Logger: Plugin Limit Login Attempts", "simple-history" ),
                "reseted_lockout_count" => _x( 'Reseted lockout count', "Logger: Plugin Limit Login Attempts", "simple-history" ),
                "cleared_current_lockouts" => _x( 'Cleared current lockouts', "Logger: Plugin Limit Login Attempts", "simple-history" ),
                "updated_options" => _x( 'Updated options', "Logger: Plugin Limit Login Attempts", "simple-history" ),
			),
			/*"labels" => array(
				"search" => array(
					"label" => _x( "Limit Login Attempts", "Logger: Plugin Limit Login Attempts", "simple-history" ),
					"options" => array(
						_x( "xxxPages not found", "User logger: 404", "simple-history" ) => array(
							"page_not_found",
						),
					),
				), // end search
			),*/  // end labels
		);

		return $arr_info;

	}

    function loaded() {

        add_filter( "pre_option_limit_login_lockouts_total", array( $this, "on_option_limit_login_lockouts_total" ), 10, 1 );

        add_action( "load-settings_page_limit-login-attempts", array( $this, "on_load_settings_page" ), 10, 1 );

    }

    /**
     * Fired when plugin options screen is loaded
     */
    function on_load_settings_page($a) {

        #var_dump( check_admin_referer('limit-login-attempts-options') );
        #dd( $_POST );

        if ( $_POST && wp_verify_nonce($_POST["_wpnonce"], "limit-login-attempts-options") ) {

            // Settings saved
        	if (isset($_POST['clear_log'])) {
                $this->noticeMessage( "cleared_ip_log" );
            }

            if (isset($_POST['reset_total'])) {
                $this->noticeMessage( "reseted_lockout_count" );
            }

            if (isset($_POST['reset_current'])) {
                $this->noticeMessage( "cleared_current_lockouts" );
            }

            if (isset($_POST['update_options'])) {

                $options = array(
                    "client_type" => sanitize_text_field( $_POST['client_type'] ),
            		"allowed_retries" => sanitize_text_field( $_POST['allowed_retries'] ),
            		"lockout_duration" => sanitize_text_field( $_POST['lockout_duration'] ) * 60 ,
            		"valid_duration" => sanitize_text_field( $_POST['valid_duration'] ) * 3600,
            		"allowed_lockouts" => sanitize_text_field( $_POST['allowed_lockouts'] ),
            		"long_duration" => sanitize_text_field( $_POST['long_duration'] ) * 3600,
            		"email_after" => sanitize_text_field( $_POST['email_after'] ),
                    "cookies" => (isset($_POST['cookies']) && $_POST['cookies'] == '1') ? "yes" : "no"
                );

                $v = array();
        		if (isset($_POST['lockout_notify_log'])) {
        			$v[] = 'log';
        		}
        		if (isset($_POST['lockout_notify_email'])) {
        			$v[] = 'email';
        		}
        		$lockout_notify = implode(',', $v);
                $options["lockout_notify"] = $lockout_notify;

                $this->noticeMessage("updated_options", array(
                    "options" => $options
                ));

            }

            /*
            Should we clear log?
        	if (isset($_POST['clear_log'])) {
        			. __('Cleared IP log', 'limit-login-attempts')
        			. '</p></div>';
        	}

        	Should we reset counter
        	if (isset($_POST['reset_total'])) {
        		update_option('limit_login_lockouts_total', 0);
        		echo '<div id="message" class="updated fade"><p>'
        			. __('Reset lockout count', 'limit-login-attempts')
        			. '</p></div>';
        	}

        	Should we restore current lockouts
        	if (isset($_POST['reset_current'])) {
        		update_option('limit_login_lockouts', array());
        		echo '<div id="message" class="updated fade"><p>'
        			. __('Cleared current lockouts', 'limit-login-attempts')
        			. '</p></div>';
        	}

            if (isset($_POST['update_options'])) {
        		global $limit_login_options;

        		$limit_login_options['client_type'] = $_POST['client_type'];
        		$limit_login_options['allowed_retries'] = $_POST['allowed_retries'];
        		$limit_login_options['lockout_duration'] = $_POST['lockout_duration'] * 60;
        		$limit_login_options['valid_duration'] = $_POST['valid_duration'] * 3600;
        		$limit_login_options['allowed_lockouts'] = $_POST['allowed_lockouts'];
        		$limit_login_options['long_duration'] = $_POST['long_duration'] * 3600;
        		$limit_login_options['notify_email_after'] = $_POST['email_after'];
        		$limit_login_options['cookies'] = (isset($_POST['cookies']) && $_POST['cookies'] == '1');

        		$v = array();
        		if (isset($_POST['lockout_notify_log'])) {
        			$v[] = 'log';
        		}
        		if (isset($_POST['lockout_notify_email'])) {
        			$v[] = 'email';
        		}
        		$limit_login_options['lockout_notify'] = implode(',', $v);


            */

        }

        /*
        $_POST array(12)
        '_wpnonce' => string(10) "35afb3d783"
        '_wp_http_referer' => string(55) "/wp-admin/options-general.php?page=limit-login-attempts"
        'allowed_retries' => string(1) "4"
        'lockout_duration' => string(2) "20"
        'allowed_lockouts' => string(1) "4"
        'long_duration' => string(2) "24"
        'valid_duration' => string(2) "12"
        'client_type' => string(11) "REMOTE_ADDR"
        'cookies' => string(1) "1"
        'lockout_notify_log' => string(3) "log"
        'email_after' => string(1) "4"
        'update_options' => string(14) "Change Options"
        */

    }

    /**
     * When option value is updated
     * do same checks as plugin itself does
     * and log if we match something
     */
    function on_option_limit_login_lockouts_total( $value ) {

        global $limit_login_just_lockedout;

        if ( ! $limit_login_just_lockedout ) {
            return $value;
        }

        $ip = limit_login_get_address();
    	$whitelisted = is_limit_login_ip_whitelisted( $ip );

        // $limit_login_logged = get_option( 'limit_login_logged' );

        $retries = get_option( 'limit_login_retries' );
        if ( ! is_array( $retries ) ) {
    		$retries = array();
    	}

        if ( isset( $retries[$ip] ) && ( ( $retries[$ip] / limit_login_option( 'allowed_retries' ) ) % limit_login_option( 'notify_email_after' ) ) != 0 ) {

            // $this->notice( "user locked out but don't log" );

            //return;
    	}

        /* Format message. First current lockout duration */
        $lockout_type = "";
    	if ( ! isset( $retries[$ip] ) ) {
    		/* longer lockout */
            $lockout_type = "longer";
    		$count = limit_login_option( 'allowed_retries' ) * limit_login_option( 'allowed_lockouts' );
    		$lockouts = limit_login_option( 'allowed_lockouts' );
    		$time = round( limit_login_option( 'long_duration' ) / 3600 );
    		$when = sprintf( _n( '%d hour', '%d hours', $time, 'limit-login-attempts' ), $time );
    	} else {
    		/* normal lockout */
            $lockout_type = "normal";
    		$count = $retries[$ip];
    		$lockouts = floor( $count / limit_login_option( 'allowed_retries' ) );
    		$time = round( limit_login_option( 'lockout_duration' ) / 60 );
    		//$when = sprintf( _n( '%d minute', '%d minutes', $time, 'limit-login-attempts' ), $time );
    	}

        if ( $whitelisted ) {
    		// $subject = __( "Failed login attempts from whitelisted IP", 'limit-login-attempts' );
            $message_key = "failed_login_whitelisted";
    	} else {
    		// $subject = __( "Too many failed login attempts", 'limit-login-attempts' );
            $message_key = "failed_login";
    	}

        #$message = sprintf( __( "%d failed login attempts (%d lockout(s)) from IP: %s", 'limit-login-attempts' ), $count, $lockouts, $ip );

        #if ( $user != '' ) {
    	#	$message .= sprintf( __( "Last user attempted: %s", 'limit-login-attempts' ), $user );
    	#}

    	#if ( $whitelisted ) {
    		#$message .= __( "IP was NOT blocked because of external whitelist.", 'limit-login-attempts' );
    	#} else {
    		#$message .= sprintf( __( "IP was blocked for %s", 'limit-login-attempts' ), $when );
    	#}

        /*
        Subjects
        $subject = __( "Failed login attempts from whitelisted IP", 'limit-login-attempts' );
        $subject = __( "Too many failed login attempts", 'limit-login-attempts' );

        Messages
        $message = sprintf( __( "%d failed login attempts (%d lockout(s)) from IP: %s", 'limit-login-attempts' ), $count, $lockouts, $ip );
        $message .= sprintf( __( "Last user attempted: %s", 'limit-login-attempts' ), $user );
        $message .= __( "IP was NOT blocked because of external whitelist.", 'limit-login-attempts' );
        $message .= sprintf( __( "IP was blocked for %s", 'limit-login-attempts' ), $when );

        */

        $this->noticeMessage( $message_key, array(
            "value" => $value,
            "limit_login_just_lockedout" => $limit_login_just_lockedout,
            "retries" => $retries,
            //"whitelisted" => $whitelisted, // bool, true | false
            //"subject" => $subject,
            //"message" => $message,
            "count" => $count, // num of failed login attempts before block
            "time" => $time, // duration in minutes for block
            "lockouts" => $lockouts,
            "ip" => $ip,
            "lockout_type" => $lockout_type
        ) );

        return $value;

    }

    /*
    When a user is locked out this is set

    long lockout
    $lockouts[$ip] = time() + limit_login_option('long_duration');

    normal lockout
    $lockouts[$ip] = time() + limit_login_option('lockout_duration');

    global $limit_login_options;

    // this is called from admin -> plugin settings and when a lockout is in progress
    $total = get_option('limit_login_lockouts_total');
    global $limit_login_just_lockedout;
    $limit_login_just_lockedout = true;

    */

    /**
     * Add some extra info
     */
    function getLogRowDetailsOutput( $row ) {

        $output = "";

        $context = isset( $row->context ) ? $row->context : array();

        $message_key = $row->context_message_key;

        if ( "failed_login" == $message_key ) {

            $count = $context["count"];
            $lockouts = $context["lockouts"];
            $ip = $context["ip"];
            #$whitelisted = $context["whitelisted"];
            $lockout_type = $context["lockout_type"];
            $time = $context["time"];

            $output .= sprintf(
                __( '%1$d failed login attempts (%2$d lockout(s)) from IP: %1$s', 'limit-login-attempts' ), 
                $count, // 1
                $lockouts,  // 2
                $ip // 3
            );

            if ( "longer" == $lockout_type ) {

                $when = sprintf( _n( '%d hour', '%d hours', $time, 'limit-login-attempts' ), $time );

            } else if ( "normal" == $lockout_type ) {

                $when = sprintf( _n( '%d minute', '%d minutes', $time, 'limit-login-attempts' ), $time );

            }

            #if ( $whitelisted ) {
                $output .= __( 'IP was NOT blocked because of external whitelist.', 'limit-login-attempts' );
            #} else {
                $output .= sprintf( 
                    __( 'IP was blocked for %1$s', 'limit-login-attempts' ), 
                    $when // 1
                );
            #}
        }

        return $output;

    }

}

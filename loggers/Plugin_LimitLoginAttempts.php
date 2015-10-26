<?php

/**
 */
class Plugin_LimitLoginAttempts extends SimpleLogger {

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
			"name" => "Plugin Limit Login Attempts",
			"description" => "",
			"capability" => "manage_options",
			"messages" => array(
				'user_locked_out' => __('User locked out', "simple-history"),
			),
			"labels" => array(
				"search" => array(
					"label" => _x("Limit Login Attempts", "Logger plugin Limit Login Attempts", "simple-history"),
					"options" => array(
						_x("xxxPages not found", "User logger: 404", "simple-history") => array(
							"page_not_found",
						),
					),
				), // end search
			), // end labels
		);

		return $arr_info;

	}

    function loaded() {

        add_filter( "pre_option_limit_login_lockouts_total", array( $this, "on_option_limit_login_lockouts_total" ), 10, 1 );

    }

    function on_option_limit_login_lockouts_total( $value ) {

        global $limit_login_just_lockedout;
		$limit_login_just_lockedout = true;

        $ip = limit_login_get_address();
    	$whitelisted = is_limit_login_ip_whitelisted($ip);

        $limit_login_logged = get_option('limit_login_logged');

        $retries = get_option('limit_login_retries');
        if (!is_array($retries)) {
    		$retries = array();
    	}

        if ( isset($retries[$ip])
    		 && ( ($retries[$ip] / limit_login_option('allowed_retries'))
    			  % limit_login_option('notify_email_after') ) != 0 ) {

            $this->notice("user locked out but don't log");

            #return;
    	}

        /* Format message. First current lockout duration */
    	if (!isset($retries[$ip])) {
    		/* longer lockout */
    		$count = limit_login_option('allowed_retries')
    			* limit_login_option('allowed_lockouts');
    		$lockouts = limit_login_option('allowed_lockouts');
    		$time = round(limit_login_option('long_duration') / 3600);
    		$when = sprintf(_n('%d hour', '%d hours', $time, 'limit-login-attempts'), $time);
    	} else {
    		/* normal lockout */
    		$count = $retries[$ip];
    		$lockouts = floor($count / limit_login_option('allowed_retries'));
    		$time = round(limit_login_option('lockout_duration') / 60);
    		$when = sprintf(_n('%d minute', '%d minutes', $time, 'limit-login-attempts'), $time);
    	}

    	$blogname = is_limit_login_multisite() ? get_site_option('site_name') : get_option('blogname');


        if ($whitelisted) {
    		$subject = sprintf(__("[%s] Failed login attempts from whitelisted IP"
    				      , 'limit-login-attempts')
    				   , $blogname);
    	} else {
    		$subject = sprintf(__("[%s] Too many failed login attempts"
    				      , 'limit-login-attempts')
    				   , $blogname);
    	}

        $message = sprintf(__("%d failed login attempts (%d lockout(s)) from IP: %s"
    			      , 'limit-login-attempts') . "\r\n\r\n"
    			   , $count, $lockouts, $ip);
    	if ($user != '') {
    		$message .= sprintf(__("Last user attempted: %s", 'limit-login-attempts')
    				    . "\r\n\r\n" , $user);
    	}
    	if ($whitelisted) {
    		$message .= __("IP was NOT blocked because of external whitelist.", 'limit-login-attempts');
    	} else {
    		$message .= sprintf(__("IP was blocked for %s", 'limit-login-attempts'), $when);
    	}

        //noticeMessage
        $this->noticeMessage("user_locked_out", array(
            "value" => $value,
            "limit_login_just_lockedout" => $limit_login_just_lockedout,
            "limit_login_logged" => $limit_login_logged,
            "retries" => $retries,
            "whitelisted" => $whitelisted,
            "subject" => $subject,
            "message" => $message
        ));

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

}

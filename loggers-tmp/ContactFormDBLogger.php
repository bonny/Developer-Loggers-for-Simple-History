<?php

/*
Link to posted entry will be

For form overview:
/wp-admin/admin.php?page=CF7DBPluginSubmissions&form_name=Contact%20form%201

For form detail:
http://wp-playground-root.ep/wp-admin/admin.php?page=CF7DBPluginSubmissions&form_name=Contact+form+1&submit_time=1427229448.6991
*/

return;

class ContactFormDBLogger extends SimpleLogger {
}


add_filter('cfdb_form_data', function($cfdb) {

        //$backtrace = debug_backtrace();
        //print_r($backtrace);

        // Detect what plugin that is responsible for the form submission
        $form_plugin = "";
        $form_submit_time = $cfdb->submit_time;
        $form_submit_ip = $cfdb->ip;

        if ( isset( $cfdb->posted_data["_wpcf7"] ) ) {
                $form_plugin = "wpcf7";
        }

        $context = array();
        $context = array_merge($context, $cfdb->posted_data);
        SimpleLogger()->info("Submitted a form", $context);

        /*
        print_r($cfdb);
        stdClass Object
        (
                [title] => Contact form 1
                [posted_data] => Array
                        (
                                [_wpcf7] => 25526
                                [_wpcf7_version] => 4.1.1
                                [_wpcf7_locale] => en_US
                                [_wpcf7_unit_tag] => wpcf7-f25526-p25556-o1
                                [_wpnonce] => e3ca8ed7ba
                                [your-name] => Pär Thernström
                                [your-email] => par.thernstrom@gmail.com
                                [your-subject] =>
                                [your-message] =>
                                [_wpcf7_is_ajax_call] => 1
                        )

                [uploaded_files] => Array
                        (
                        )

                [submit_time] => 1427229581.9775
                [ip] => 127.0.0.1
                [user] => admin
        )
        {"mailSent":true,"into":"#wpcf7-f25526-p25556-o1","captcha":null,"message":"Your message was sent successfully. Thanks."}
        */

});

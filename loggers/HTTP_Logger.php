<?php

 /**
  * Log calls to the HTTP API
  */
 class HTTP_Logger extends SimpleLogger
 {
     public $slug = __CLASS__;

     public function getInfo()
     {
         $arr_info = array(
             'name' => 'HTTP API logger',
             'description' => 'Log all usage of HTTP calls from functions like wp_remote_post() and wp_remote_get()',
             'capability' => 'manage_options',
         );

         return $arr_info;
     }

     public function loaded()
     {

         // pre_http_request is fired close before the actual request
         add_filter('pre_http_request', array($this, 'on_pre_http_request'), 10, 3);

         // http_api_debug is fired directly after the request
         add_action('http_api_debug', array($this, 'on_http_api_debug'), 10, 5);
     }

     public function on_pre_http_request($retval, $r, $url)
     {
         $key = md5($url);

         $GLOBALS["sh_http_log_{$key}"] = microtime(true);

         return $retval;
     }

     public function on_http_api_debug($response, $type, $class, $args, $url)
     {

         $key = md5($url);
         $globals_key = "sh_http_log_{$key}";

         if (empty($GLOBALS[$globals_key])) {
             return;
         }

         $time_taken = microtime(true) - $GLOBALS[$globals_key];

         $request_method = isset($args['method']) ? $args['method'] : 'Unknown';

         $context = [
                 'url' => $url,
                 'time_taken' => $time_taken,
                 'request_method' => $request_method,
                 '_response' => $response,
                 '_type' => $type,
                 '_class' => $type,
                 '_args' => $args,
                 '_url' => $url,
             ];

         $this->debug("http_api_debug: '{request_method}' request to '{url}' (took {time_taken} s)", $context);

     }

 }

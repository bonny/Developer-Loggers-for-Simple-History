<?php

/**
 * Logger class to log WP REST API requests.
 */
class WPRestAPILogger extends SimpleLogger {

	/**
	 * The slug.
	 *
	 * @var string $slug
	 */
	public $slug = __CLASS__;

	/**
	 * Method that returns an array with information about this logger.
	 * Simple History used this method to get info about the logger at various places.
	 */
	public function getInfo() {

		$arr_info = array(
			'name' => 'WordPress REST API',
			'description' => 'Logs calls that WP and plugins make to the WP REST API',
			'messages' => array(
				'wp_rest_api_called' => __( 'WP REST API called for route "{request_route}", handled by class "{handler_callback_class}" and method "{handler_callback_method}"', 'simple-history' ),
			),
		);

		return $arr_info;
	}

	/**
	 * The loaded method is called automagically when Simple History is loaded.
	 * Much of the init-code for a logger goes inside this method. To keep things
	 * simple in this example, we add almost all our code inside this method.
	 */
	public function loaded() {
		// $context = array(
		// 	"to" => $phpmailer->getToAddresses(),
		// 	"from" => $phpmailer->From,
		// 	"fromName" => $phpmailer->FromName,
		// 	"subject" => $phpmailer->Subject,
		// 	"body" => $phpmailer->Body
		// );

		// $this->infoMessage("email_sent", $context );
		add_filter( 'rest_request_after_callbacks', array( $this, 'on_rest_request_after_callbacks' ), 10, 3 );
	}

	/**
	 * Fired when WP REST API call is done.
	 *
	 * @param WP_HTTP_Response $response Result to send to the client. Usually a WP_REST_Response.
	 * @param WP_REST_Server   $handler  ResponseHandler instance (usually WP_REST_Server).
	 * @param WP_REST_Request  $request  Request used to generate the response.
	 *
	 * @return WP_HTTP_Response $response
	 */
	public function on_rest_request_after_callbacks( $response, $handler, $request ) {
		$handler_callback = $handler['callback'];
		$handler_callback_object = get_class( $handler_callback[0] );
		$handler_callback_method = $handler_callback[1];

		$context = array(
			'request_route' => $request->get_route(),
			'request_params' => $request->get_params(),
			'handler_callback_class' => $handler_callback_object,
			'handler_callback_method' => $handler_callback_method,
			'response' => $response,
			'handler' => $handler,
			'request' => print_r( $request, true ),
		);

		$this->infoMessage(
			'wp_rest_api_called',
			$context
		);

		return $response;
	}

}

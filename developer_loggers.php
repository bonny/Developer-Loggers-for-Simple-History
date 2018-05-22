<?php
/**
 * Plugin Name: Developer Loggers for Simple History
 * Plugin URI: https://github.com/bonny/Developer-Loggers-for-Simple-History
 * GitHub URI: https://github.com/bonny/Developer-Loggers-for-Simple-History
 * Description: Loggers for Simple History that are useful for developers and admins
 * Version: 0.5
 * Author: Pär Thernström
 */

if ( version_compare( phpversion(), '5.4', '>=' ) ) {

	include_once( __DIR__ . '/class.php' );

	/**
	 * Init plugin using the add_custom_logger filter
	 *
	 * @param Simple History instance $simpleHistory Simple history instance.
	 */
	function SimpleHistory_DeveloperLoggers_addCustomLogger( $simpleHistory ) {

		$logger = new SimpleHistory_DeveloperLoggers;
		$logger->init( $simpleHistory );

	}

	add_action( 'simple_history/add_custom_logger', 'SimpleHistory_DeveloperLoggers_addCustomLogger' );

	/**
	 * Fallback if Simple History is not installed
	 * Show message about it
	 */
	add_action( 'admin_init', function() {

		if ( ! class_exists( 'SimpleHistory' ) ) {

			add_action( 'admin_notices', function() {
				?>
				<div class="updated error">
					<p><?php esc_html_e( '"Developer Loggers for Simple History" requires that the plugin "Simple History" is installed and activated.', 'simple-history' ); ?></p>
				</div>
				<?php
			} );

		}

	} );

} else {

	// User is running to old version of php, add admin notice about that.
	add_action( 'admin_notices', 'simple_history_developer_loggers_old_version_notice' );

	/**
	 * Show admin notice about old php version.
	 */
	function simple_history_developer_loggers_old_version_notice() {
		?>
		<div class="updated error">
			<p>
				<?php
				printf(
					__( 'Developer Loggers for Simple History requires at least PHP 5.4 (you have version %s).', 'simple-history' ),
					esc_html( phpversion() )
				);
				?>
			</p>
		</div>
		<?php
	}
}

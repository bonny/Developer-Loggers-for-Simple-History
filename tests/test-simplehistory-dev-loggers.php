<?php

class SimpleHistoryDevLoggersTest extends WP_UnitTestCase {

	// https://phpunit.de/manual/current/en/fixtures.html
    public static function setUpBeforeClass() {

    }


	function test_sample() {

		// replace this with some actual testing code
		$this->assertTrue( true );

        $this->assertTrue( class_exists("SimpleHistory_DeveloperLoggers") );
	}

	function test_history_setup() {

		$this->assertTrue( defined("SIMPLE_HISTORY_VERSION") );
		$this->assertTrue( defined("SIMPLE_HISTORY_PATH") );
		$this->assertTrue( defined("SIMPLE_HISTORY_BASENAME") );
		$this->assertTrue( defined("SIMPLE_HISTORY_DIR_URL") );
		$this->assertTrue( defined("SIMPLE_HISTORY_FILE") );

		$this->assertFalse( defined("SIMPLE_HISTORY_DEV") );
		$this->assertFalse( defined("SIMPLE_HISTORY_LOG_DEBUG") );

	}

}

<?php

class SimpleHistoryDevLoggersTest extends WP_UnitTestCase {

	// https://phpunit.de/manual/current/en/fixtures.html
    public static function setUpBeforeClass() {

    }


	function test_plugin() {

		// replace this with some actual testing code
		$this->assertTrue( true );

        $this->assertTrue( class_exists("SimpleHistory_DeveloperLoggers") );
	}

}

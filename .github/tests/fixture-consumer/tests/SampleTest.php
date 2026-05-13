<?php
/**
 * Smoke test that proves PHPUnit + WP_UnitTestCase are wired up correctly inside the wp-env cli container.
 */
class SampleTest extends WP_UnitTestCase {
	public function test_truthy() {
		$this->assertTrue( true );
	}
}

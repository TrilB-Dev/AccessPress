<?php

namespace AccessPress\Test\Unit;

use AccessPress\Includes\Analytics\Analytics;
use PHPUnit\Framework\TestCase;

final class AnalyticsTest extends TestCase {
	public function test_analytics_tracker_exists_and_is_callable(): void {
		$this->assertTrue( class_exists( Analytics::class ) );
		$this->assertTrue( method_exists( Analytics::class, 'track_view' ) );
		$this->assertTrue( is_callable( array( Analytics::class, 'track_view' ) ) );
		$this->assertTrue( method_exists( Analytics::class, 'track_event' ) );
		$this->assertTrue( is_callable( array( Analytics::class, 'track_event' ) ) );
	}

	public function test_analytics_tracker_records_and_reports_events(): void {
		Analytics::track_event( 'user_login', array( 'user_id' => 42, 'source' => 'frontend' ) );
		Analytics::track_event( 'user_login', array( 'user_id' => 42, 'source' => 'frontend' ) );

		$summary = Analytics::get_summary( array( 'event' => 'user_login' ) );
		$this->assertSame( 'user_login', $summary['event'] );
		$this->assertGreaterThanOrEqual( 2, (int) $summary['count'] );
		$this->assertArrayHasKey( 'total', $summary );
	}
}




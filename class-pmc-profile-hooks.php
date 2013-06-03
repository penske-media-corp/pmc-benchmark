<?php

/**
 * Original profiling class by Gabriel Koen http://gabrielkoen.com/2012/07/29/debugging-wordpress-find-slow-filters-and-actions/
 *
 * @since 2012-07-29 Gabriel Koen
 *
 * @version 2013-05-31 Amit Gupta
 * @version 2013-06-02 Amit Gupta
 * @version 2013-06-03 Amit Gupta
 */

class PMC_Profile_Hooks {

	protected static $_threshold_to_hide = 0.1;

	protected static $_start_time = 0;
	protected static $_end_time = 0;

	public static function get_threshold_to_hide() {
		return static::$_threshold_to_hide;
	}

	public static function set_threshold_to_hide( $time ) {
		static::$_threshold_to_hide = (float) $time;
	}

	public static function set_start_time() {
		if( static::$_start_time !== 0 ) {
			return;
		}

		static::$_start_time = microtime();
	}

	public static function set_end_time() {
		static::$_end_time = microtime();
	}

	public static function get_execution_duration() {
		if( static::$_start_time == 0 ) {
			return 0;
		}

		if( static::$_end_time == 0 ) {
			static::$_end_time = microtime();
		}

		list( $start_usec, $start_sec ) = explode( " ", static::$_start_time );
		$start_sec = floatval( $start_sec );
		$start_usec = floatval( $start_usec );
		$start_time = $start_sec + $start_usec;

		list( $end_usec, $end_sec ) = explode( " ", static::$_end_time );
		$end_sec = floatval( $end_sec );
		$end_usec = floatval( $end_usec );
		$end_time = $end_sec + $end_usec;

		return round( floatval( $end_time - $start_time ), 3 );
	}

	public static function record_action( $name ) {
		static::set_start_time();
		static::set_end_time();

		$backtrace = debug_backtrace(false);

		if( ! isset( $backtrace[2]['args'] ) ) {
			return;
		}

		foreach( $backtrace[2]['args'] as &$arg ) {
			$arg = ( is_string( $arg ) ) ? stripslashes( $arg ) : $arg;
		}

		$GLOBALS['pmc_benchmark_action_timer'][ microtime() ][] = array(
			'name' => $name,
			'backtrace' => var_export( $backtrace[2], true ),
		);
	}

	public static function get_recorded_actions() {
		$output = '<pre>' . "\n";

		$timetotals = array();
		$eol = '<br />';
		$detail_list = '<div class="pmc-benchmark-list"><p class="pmc-benchmark-section-header">';
		$detail_list .= '<strong>DETAILED ACTION LIST</strong>';

		if( static::get_threshold_to_hide() > 0.0 ) {
			$detail_list .= $eol . 'Hiding items faster than <strong>' . static::get_threshold_to_hide() . '</strong> seconds.';
		}

		$detail_list .= '</p><p class="pmc-benchmark-section-content">';

		reset( $GLOBALS['pmc_benchmark_action_timer'] );
		$previous_item = array( 'timestamp' => array( 'sec' => 0.0, 'usec' => 0.0 ), 'items' => array() );

		do {
			if ( ! isset( $timestamp ) ) {
				$timestamp = microtime();
			}

			if ( ! isset( $items ) ) {
				$items = array();
			}

			list( $usec, $sec ) = explode( " ", $timestamp );
			$sec = floatval( $sec );
			$usec = floatval( $usec );
			$timestamp_float = $sec + $usec;

			if( $timestamp && ! isset( $start_time ) ) {
				$start_time = $timestamp_float;
			} else {
				$end_time = $timestamp_float;
			}

			if( empty( $previous_items['items'] ) ) {
				$diff = 0.0;
				$previous_items['items'][] = array( 'name' => 'nothing', 'backtrace' => '' );
			} else {
				$diff = $timestamp_float - ( $previous_items['timestamp']['sec'] + $previous_items['timestamp']['usec'] );
			}

			if( $diff > static::get_threshold_to_hide() ) {
				$previous = array();

				if( ! empty( $previous_items['items'] ) ) {
					foreach( $previous_items['items'] as $previous_item ) {
						$previous[] = $previous_item['name'];
					}
				}

				foreach( $items as $item ) {
					$detail_list .= $eol . '<strong>' . round( $diff, 3 ) . '</strong> seconds between starting <strong>' . iG_Utility::to_sentence( $previous, ', ', '</strong> and <strong>', '</strong> and <strong>' ) . '</strong> and starting <strong>' . $item['name'] . '</strong>' . $eol;

					if( ! empty( $previous_items['items'] ) ) {
						foreach( $previous_items['items'] as $previous_item ) {
							$detail_list .= $previous_item['backtrace'] . $eol;
						}
					}

					$detail_list .= $item['backtrace'] . $eol;
				}
			}

			foreach( $items as $key => $item ) {
				$timetotals[ $item['name'] ][] = $diff;
			}

			$previous_items['timestamp']['sec'] = $sec;
			$previous_items['timestamp']['usec'] = $usec;
			$previous_items['items'] = $items;
		} while( list( $timestamp, $items ) = each( $GLOBALS['pmc_benchmark_action_timer'] ) );

		$detail_list .= '</p></div>';

		$summary_list = '<div class="pmc-benchmark-list"><p class="pmc-benchmark-section-header">';
		$summary_list .= '<strong>ACTION OVERVIEW:</strong> Aggregate Totals.';

		if( static::get_threshold_to_hide() > 0.0 ) {
			$summary_list .= $eol . 'Hiding items faster than <strong>' . static::get_threshold_to_hide() . '</strong> seconds.';
		}

		$summary_list .= '</p><p class="pmc-benchmark-section-content">';

		do {
			$total_time = 0.0;

			if( isset( $times ) ) {
				foreach( $times as $time ) {
					$total_time += $time;
				}
			}

			if( $total_time > static::get_threshold_to_hide() ) {
				$summary_list .= '<strong>' . $tag . '</strong> took approximately <strong>' . round( $total_time, 3 ) . '</strong> seconds' . $eol;
			}
		} while ( list( $tag, $times ) = each( $timetotals ) );

		$summary_list .= '</p></div>';

		if( $end_time < $start_time ) {
			$end_time = $start_time;
		}

		$output .= '<div class="pmc-benchmark-overview">Request took <strong>' . static::get_execution_duration() . '</strong> seconds from start to finish</div>' . "\n";
		$output .= '<hr />' . "\n";
		$output .= $summary_list . "\n";
		$output .= '<hr />' . "\n";
		$output .= $detail_list . "\n";
		$output .= '</pre>' . "\n";

		return $output;
	}

//end of class
}

PMC_Profile_Hooks::record_action( 'PMC_Benchmark__loaded' );

//EOF
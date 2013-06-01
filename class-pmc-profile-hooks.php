<?php

/**
 * Original profiling class by Gabriel Koen http://gabrielkoen.com/2012/07/29/debugging-wordpress-find-slow-filters-and-actions/
 *
 * @since 2012-07-29 Gabriel Koen
 *
 * @version 2013-05-31 Amit Gupta
 * @version 2013-06-02 Amit Gupta
 */

class PMC_Profile_Hooks {

	protected static $_threshold_to_hide = 0.1;

	public static function get_threshold_to_hide() {
		return static::$_threshold_to_hide;
	}

	public static function set_threshold_to_hide( $time ) {
		static::$_threshold_to_hide = (float) $time;
	}

	public static function record_action( $name ) {
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
		$break = '<hr />';
		$eol = '<br />';
		$detail_list = $eol . $eol . $break . $eol . $eol;
		$detail_list .= '<strong>Detailed Action List:</strong>' . $eol;

		if( static::get_threshold_to_hide() > 0.0 ) {
			$detail_list .= 'Hiding items faster than <strong>' . static::get_threshold_to_hide() . '</strong> seconds.' . $eol;
		}

		$detail_list .= $eol . $break . $eol;

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
					$detail_list .= $eol . '<strong>' . round( $diff, 3 ) . '</strong> seconds between starting <strong>' . implode(', ', $previous) . '</strong> and starting <strong>' . $item['name'] . '</strong>' . $eol;

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

		$detail_list .= $eol . $break . $eol;

		$summary_list = $eol . $eol . $break . $eol . $eol;
		$summary_list .= '<strong>Action Overview:</strong> aggregate totals.' . $eol;

		if( static::get_threshold_to_hide() > 0.0 ) {
			$summary_list .= 'Hiding items faster than <strong>' . static::get_threshold_to_hide() . '</strong> seconds.' . $eol;
		}

		$summary_list .= $eol . $break . $eol;

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

		$summary_list .= $eol . $break . $eol;

		if( $end_time < $start_time ) {
			$end_time = $start_time;
		}

		$output .= 'Request took <strong>' . ( $end_time - $start_time ) . '</strong> seconds from start to finish.' . $eol . "\n";
		$output .= $summary_list . "\n";
		$output .= $detail_list . "\n";
		$output .= '</pre>' . "\n";

		return $output;
	}

//end of class
}

PMC_Profile_Hooks::record_action( 'PMC_Benchmark__loaded' );

//EOF
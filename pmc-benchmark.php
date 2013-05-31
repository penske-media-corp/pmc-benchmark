<?php
/*
Plugin Name: PMC Benchmark
Plugin URI: https://github.com/Penske-Media-Corp/pmc-benchmark
Description: This plugin helps determine which hooks are slow in execution. It is for debugging and performance optimization only & should only be run in a development environment.
Version: 1.0 alpha
Author: Amit Gupta, pmcdotcom
Author URI: http://blog.igeek.info/
License: GPL v2
*/

/*
 * WordPress doesn't allow $_GET so a workaround to get the var from querystring
 */
if( ( isset( $_SERVER['QUERY_STRING'] ) && stripos( $_SERVER['QUERY_STRING'], 'benchmark=yes' ) !== false ) || ( defined('PMC_BENCHMARK') && PMC_BENCHMARK === true ) ) {
	pmc_benchmark_loader();	//lock & load
}

function pmc_benchmark_loader() {
	require_once( __DIR__ . '/class-pmc-benchmark.php' );

	add_action( 'all', function(){
		PMC_Benchmark::record_action( current_filter() );
	}, 1 );

	add_action( 'admin_footer', 'pmc_benchmark_output', 9999 );
	add_action( 'wp_footer', 'pmc_benchmark_output', 9999 );
}

function pmc_benchmark_output() {
	PMC_Benchmark::record_action( 'done' );
	PMC_Benchmark::print_recorded_actions();
}

//EOF
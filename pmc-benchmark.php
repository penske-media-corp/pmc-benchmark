<?php
/*
Plugin Name: PMC Benchmark
Description: This plugin helps determine which hooks are slow in execution. It is for debugging and performance optimization only & should only be run in a development environment. This plugin needs <strong>Debug Bar</strong> plugin to be installed & activated, it adds a panel in it.
Version: 1.0
Author: Amit Gupta
Author URI: http://igeek.info/
License: GPL v2
*/


define( 'PMC_BENCHMARK_VERSION', '1.0' );

/**
 * Loader function for the hook profiler, this needs to be up & running ASAP!
 */
function pmc_benchmark_profile_hooks_loader() {
	require_once( __DIR__ . '/class-pmc-profile-hooks.php' );

	add_action( 'all', function(){
		PMC_Profile_Hooks::record_action( current_filter() );	//record all possible hooks
	}, 1 );
}
pmc_benchmark_profile_hooks_loader();	//lock & load

/**
 * Add the panel into Debug Bar
 */
function pmc_benchmark_add_panel( $panels ) {
	require_once( __DIR__ . '/class-pmc-benchmark.php' );

	$panels[] = new PMC_Benchmark();
	return $panels;
}
add_filter( 'debug_bar_panels', 'pmc_benchmark_add_panel' );


//EOF

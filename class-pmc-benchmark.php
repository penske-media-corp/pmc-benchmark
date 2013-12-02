<?php

/**
 * Class which adds PMC Benchmark panel in Debug Bar and shows hook benchmark data in it
 *
 * @author Amit Gupta
 * @since 2013-06-01 Amit Gupta
 *
 * @version 2013-06-02 Amit Gupta
 */

class PMC_Benchmark extends Debug_Bar_Panel {

	const plugin_id = 'pmc-benchmark';

	/**
	 * Initialize class
	 *
	 * @return void
	 */
	public function init() {
		//setup output in page footer
		add_action( 'admin_footer', array( $this, 'output_results' ), 9999 );
		add_action( 'wp_footer', array( $this, 'output_results' ), 9999 );

		//name our panel in Debug Bar
		$this->title( 'PMC Benchmark' );

		//enqueue our scripts/styles etc
		$this->enqueue();
	}

	/**
	 * Enqueue scripts/styles etc
	 *
	 * @return  void
	 */
	public function enqueue() {
		wp_enqueue_style( self::plugin_id . '-css', plugins_url( 'css/styles.css', __FILE__ ), array(), PMC_BENCHMARK_VERSION );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( self::plugin_id . '-encoder', plugins_url( 'js/encoder.js', __FILE__ ), array(), PMC_BENCHMARK_VERSION );
	}

	/**
	 * Add our panel in Debug Bar panel menu
	 *
	 * @return  void
	 */
	public function prerender() {
		$this->set_visible( true );
	}

	/**
	 * Show the contents of the panel

	 * @return  void
	 */
	public function render() {
?>
		<h2>PMC Benchmark</h2>
		<div id="pmc-benchmark-panel">Hot damn!! Javascript got screwed somewhere!</div>
<?php
	}

	/**
	 * Output the JS in footer which inserts the benchmark results in our panel.
	 * This workaround is needed as we measure all hooks till the footer and
	 * Debug Bar outputs the panels in the head itself.

	 * @return  void
	 */
	public function output_results() {
		PMC_Profile_Hooks::record_action( 'done' );
?>
		<script type="text/javascript">
			jQuery('#pmc-benchmark-panel').html( Encoder.htmlDecode( "<?php echo esc_js( PMC_Profile_Hooks::get_recorded_actions() ); ?>" ) );
		</script>
<?php
	}
} //end of class


//EOF

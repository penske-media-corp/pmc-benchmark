PMC Benchmark
=============

**License:** [GNU GPL v2](http://www.gnu.org/licenses/gpl-2.0.html)

## Description ##

**PMC Benchmark** is a benchmarking plugin for WordPress to profile slow hooks. It requires [Debug Bar](http://wordpress.org/plugins/debug-bar/) plugin to be installed and activated. The hook execution benchmarks are displayed in a panel in Debug Bar.

This plugin is for use in development environment only and not meant for use in a production environment.

**Requirements**: This plugin requires PHP 5.3. It has been tested with WordPress 3.5.1 and might or might not work with an earlier version.

On WordPress.org - http://wordpress.org/plugins/pmc-benchmark/

## Usage ##

Using this plugin is very straight forward. Make sure you have __Debug Bar__ plugin installed and activated. Activate **PMC Benchmark** plugin and on any page you can click on the "Debug" menu in the admin bar on top to open _Debug Bar_ panels. In that there will be a panel labelled _PMC Benchmark_ and clicking that will show the hook execution benchmarks.

## Frequently Asked Questions ##

##### This plugin does not show hooks run in all the plugins. What's the deal?

It is a limitation at present. The plugin will profile only those hooks which are executed after this plugin is loaded. So some plugins will be missed out but all hooks executed in the current theme will be captured. You can get around this limitation by loading this plugin via mu-plugins which are loaded before plugins. This limitation will be resolved in a future release.

##### Why is there so much data on some pages?

Both the summary and detailed view are shown. This is after all a debugging plugin for use in development environment, to help you improve performance of your plugin(s)/theme(s). What good that be without verbose data! ;)


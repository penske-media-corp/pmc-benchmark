=== PMC Benchmark ===
Contributors: amit, pmcdotcom
Tags: hooks, debug, profiling, benchmark, debug bar, performance
Requires at least: 3.5
Tested up to: 3.5.1
Stable tag: trunk
License: GNU GPLv2

A benchmarking plugin to profile slow hooks.

== Description ==

This plugin is to lookup which hooks are slow in execution in plugins and current theme.

This plugin needs [Debug Bar](http://wordpress.org/plugins/debug-bar/) plugin to be installed and activated. It adds a panel labelled "PMC Benchmark" in it.

This plugin is for use in development environment only and not meant for use in a production environment.

Github: https://github.com/Penske-Media-Corp/pmc-benchmark


== Installation ==

Just put it in the plugins directory like any other normal plugin & activate it, that's all, no configuration etc. to mess around with. :)

== Frequently Asked Questions ==

= This plugin does not show hooks run in all the plugins. What's the deal? =

It is a limitation at present. The plugin will profile only those hooks which are executed after this plugin is loaded. So some plugins will be missed out but all hooks executed in the current theme will be captured. You can get around this limitation by loading this plugin via mu-plugins which are loaded before plugins. This limitation will be resolved in a future release.

= Why is there so much data on some pages? =

Both the summary and detailed view are shown. This is after all a debugging plugin for use in development environment, to help you improve performance of your plugin(s)/theme(s). What good that be without verbose data! ;)

== Screenshots ==

1. PMC Benchmark panel in Debug Bar

== Changelog ==

= 1.1 =
* Initial public release

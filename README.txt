=== Output Buffer Tester ===
Contributors: nextendweb
Tags: debug, debugging tool, output buffer
Requires at least: 4.0
Tested up to: 4.9
Stable tag: 1.0.1
Requires PHP: 5.2
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin helps to developers to find which plugin or theme closed their opened full page output buffers.

== Description ==
Output Buffer Tester is for debugging purpose only. Please deactivate and remove if you do not need it anymore!

### Usage
Install and activate the plugin. Append ?ob-test=1 to any url on your page and see the result. If everything goes well, you are fine. If there is an error, you will see an error message and a call stack, so you will be able to find out who closed the output buffer.


== Changelog ==

= 1.0.1 - 28. March 2018. =
* Added function param to help the search.
* Display the template_redirect actions if the plugin was not able to add the output buffers.

= 1.0.0 - 16. February 2018. =

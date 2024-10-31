<?php
/*
Plugin Name: Output Buffer Tester
Plugin URI: https://nextendweb.com/
Description: This plugin helps to identify full page output buffer issues
Version: 1.0.1
Author: Nextend
Author URI: https://nextendweb.com
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

add_action('admin_notices', 'NextendOutputBufferTesterAdminNotice');

function NextendOutputBufferTesterAdminNotice() {
    ?>
    <div class="notice notice-info is-dismissible">
        <p>Output Buffer Tester is for debugging purpose only. Please deactivate and remove if you do not need it anymore!</p>
        <p><a target="_blank" href="<?php echo esc_attr(add_query_arg('ob-test', '1', site_url())); ?>">Click here to test your homepage</a></p>
    </div>
    <?php
}

if (isset($_GET['ob-test'])) {

    class NextendOutputBufferTester {

        private static $debugCallStack = array();

        public static function init() {

            self::startStage1();

            add_action('template_redirect', 'NextendOutputBufferTester::startStage2', -1000000000);
            add_action('template_redirect', 'NextendOutputBufferTester::startStage3', 1000000000);
            add_filter('template_include', 'NextendOutputBufferTester::startStage4', 1000000000);


            add_action('shutdown', 'NextendOutputBufferTester::do_test', -1000000000);
        }

        public static function do_test() {
            $handlers = ob_list_handlers();

            if (!in_array('NextendOutputBufferTester::endStage1', $handlers)) {
                self::error(1, 'Stage 1 buffer failed. Someone closed the buffer after this plugin loaded!');
            } else if (!in_array('NextendOutputBufferTester::endStage2', $handlers)) {
                self::error(2, 'Stage 2 buffer failed. Someone closed the buffer after <em>template_redirect</em> action with priority -1000000000!');
            } else if (!in_array('NextendOutputBufferTester::endStage3', $handlers)) {
                self::error(3, 'Stage 3 buffer failed. Someone closed the buffer after <em>template_redirect</em> action with priority 1000000000!');
            } else if (!in_array('NextendOutputBufferTester::endStage4', $handlers)) {
                self::error(4, 'Stage 4 buffer failed. Someone closed the buffer after <em>template_include</em> filter!');
            }
        }

        public static function startStage1() {
            ob_start('NextendOutputBufferTester::endStage1');
        }

        public static function endStage1($content) {
            self::$debugCallStack[1] = debug_backtrace();

            return $content;
        }

        public static function startStage2() {

            ob_start('NextendOutputBufferTester::endStage2');
        }

        public static function endStage2($content) {
            self::$debugCallStack[2] = debug_backtrace();

            return $content;
        }

        public static function startStage3() {
            ob_start('NextendOutputBufferTester::endStage3');
        }

        public static function endStage3($content) {
            self::$debugCallStack[3] = debug_backtrace();

            return $content;
        }

        public static function startStage4($ret) {
            ob_start('NextendOutputBufferTester::endStage4');

            return $ret;
        }

        public static function endStage4($content) {

            self::$debugCallStack[4] = debug_backtrace();

            return $content;
        }

        private static function error($stage, $message) {
            echo '<div style="position:fixed;width:100%;height:100%;padding:10%;left:0;top:0;z-index: 2147483647;background:#fff;color:#f00;overflow:scroll;">' . $message . '<br>Try to disable plugins or switch theme to find which one.<br>';
            echo '<pre>';
            $i = 1;
            if (!empty(self::$debugCallStack[$stage])) {
                foreach (self::$debugCallStack[$stage] AS $call) {
                    echo '#' . $i++ . (isset($call['class']) ? ' Class: ' . $call['class'] : '') . ' Function: ' . $call['function'] . (isset($call['file']) ? ' File:' . $call['file'] . ' Line: ' . $call['line'] : '') . "\n";
                }
            } else {
                echo "Probably the current ouput buffer's action is not called!\n";
                if (isset($_GET['function'])) {
                    $reflFunc = new ReflectionFunction($_GET['function']);
                    echo $reflFunc->getFileName() . ':' . $reflFunc->getStartLine() . "\n\n";
                }

                global $wp_filter;
                echo "template_redirect action\n\n";
                var_dump($wp_filter['template_redirect']);
            }
            echo '</pre>';
            echo '</div>';
        }
    }

    NextendOutputBufferTester::init();
}
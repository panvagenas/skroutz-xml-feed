<?php
/**
 * HtmlFormatter.php description
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     TODO ${VERSION}
 * @package   Pan\XmlGenerator\Logger\Handlers
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */

namespace Pan\XmlGenerator\Logger\Handlers;

if ( ! defined( 'WPINC' ) ) {
    die;
}

use Monolog\Formatter\HtmlFormatter as MonologHtmlFormatter;
use Monolog\Logger as MonoLogger;

/**
 * Class HtmlFormatter
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     TODO ${VERSION}
 * @package   Pan\XmlGenerator\Logger\Handlers
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */
class HtmlFormatter extends MonologHtmlFormatter {

    private $logLevelsClassNames = array(
        MonoLogger::ALERT     => 'log-alert alert alert-default',
        MonoLogger::DEBUG     => 'log-debug alert alert-default',
        MonoLogger::INFO      => 'log-info alert alert-info',
        MonoLogger::NOTICE    => 'log-notice alert alert-warning',
        MonoLogger::WARNING   => 'log-warning alert alert-warning',
        MonoLogger::ERROR     => 'log-error alert alert-danger',
        MonoLogger::CRITICAL  => 'log-critical alert alert-danger',
        MonoLogger::EMERGENCY => 'log-emergency alert alert-danger',
    );

    /**
     * Formats a log record.
     *
     * @param  array $record A record to format
     *
     * @return mixed The formatted record
     */
    public function format( array $record ) {
        $output = '<div class="' . $this->logLevelsClassNames[ $record['level'] ] . '" role="alert">';
        $output .= $record['level_name'] . ': ' . $record['message'];
        $output .= '</div>';

        return $output/*.'</table>'.'</div>'*/
            ;
    }
}
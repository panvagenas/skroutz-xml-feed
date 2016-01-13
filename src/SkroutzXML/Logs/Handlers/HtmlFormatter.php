<?php
/**
 * HtmlFormatter.php description
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     TODO ${VERSION}
 * @package   Pan\SkroutzXML\Logs\Handlers
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */

namespace Pan\SkroutzXML\Logs\Handlers;

use Monolog\Formatter\HtmlFormatter as MonologHtmlFormatter;
use Monolog\Logger as MonoLogger;

/**
 * Class HtmlFormatter
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     TODO ${VERSION}
 * @package   Pan\SkroutzXML\Logs\Handlers
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */
class HtmlFormatter extends MonologHtmlFormatter{

    private $logLevelsClassNames = array(
        MonoLogger::DEBUG     => 'log-debug',
        MonoLogger::INFO      => 'log-info alert alert-info',
        MonoLogger::NOTICE    => 'log-notice alert alert-warning',
        MonoLogger::WARNING   => 'log-warning alert alert-danger',
        MonoLogger::ERROR     => 'log-error',
        MonoLogger::CRITICAL  => 'log-critical',
        MonoLogger::ALERT     => 'log-alert',
        MonoLogger::EMERGENCY => 'log-emergency',
    );

    /**
     * Formats a log record.
     *
     * @param  array $record A record to format
     * @return mixed The formatted record
     */
    public function format(array $record)
    {
        $output = '<div class="alert alert-success '.$this->logLevelsClassNames[$record['level']].'" role="alert">';
        $output .= $record['level_name'] . ': ' . $record['message'];
        $output .= '</div>';

        return $output/*.'</table>'.'</div>'*/;
    }
}
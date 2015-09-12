<?php

/**
 * Project: anosiapharmacy
 * File: HtmlFormater.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 5/9/2015
 * Time: 4:40 μμ
 * Since: TODO ${VERSION}
 * Copyright: 2015 Panagiotis Vagenas
 */

namespace SkroutzXML\Log\Handlers;

use Monolog\Formatter\HtmlFormatter;
use Monolog\Logger as MonoLogger;

class HtmlFormater extends HtmlFormatter{
	/**
	 * Translates Monolog log levels to html color priorities.
	 */
	private $logLevels = array(
		MonoLogger::DEBUG     => '#cccccc',
		MonoLogger::INFO      => '#468847',
		MonoLogger::NOTICE    => '#3a87ad',
		MonoLogger::WARNING   => '#c09853',
		MonoLogger::ERROR     => '#f0ad4e',
		MonoLogger::CRITICAL  => '#FF7708',
		MonoLogger::ALERT     => '#C12A19',
		MonoLogger::EMERGENCY => '#000000',
	);

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
	 * Creates an HTML table row
	 *
	 * @param  string $th       Row header content
	 * @param  string $td       Row standard cell content
	 * @param  bool   $escapeTd false if td content must not be html escaped
	 * @return string
	 */
	private function addRow($th, $td = ' ', $escapeTd = false)
	{
		$th = htmlspecialchars($th, ENT_NOQUOTES, 'UTF-8');
		if ($escapeTd) {
			$td = '<pre>'.htmlspecialchars($td, ENT_NOQUOTES, 'UTF-8').'</pre>';
		}

		return "<tr style=\"padding: 4px;spacing: 0;text-align: left;\">\n<th style=\"background: #cccccc\" width=\"100px\">$th:</th>\n<td style=\"padding: 4px;spacing: 0;text-align: left;background: #eeeeee\">".$td."</td>\n</tr>";
	}

	/**
	 * Create a HTML h1 tag
	 *
	 * @param  string  $title Text to be in the h1
	 * @param  integer $level Error level
	 * @return string
	 */
	private function addTitle($title, $level)
	{
		$title = htmlspecialchars($title, ENT_NOQUOTES, 'UTF-8');

		return '<h1 style="background: '.$this->logLevels[$level].';color: #ffffff;padding: 5px;" class="monolog-output">'.$title.'</h1>';
	}
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



//		$output = '<div class="skz-log-row '.$this->logLevelsClassNames[$record['level']].'">'.$this->addTitle($record['level_name'], $record['level']);
//		$output .= '<table cellspacing="1" width="100%" class="monolog-output">';
//
//		$output .= $this->addRow('Message', (string) $record['message']);
//		$output .= $this->addRow('Time', $record['datetime']->format($this->dateFormat));
//		$output .= $this->addRow('Channel', $record['channel']);
//		if ($record['context']) {
//			$embeddedTable = '<table cellspacing="1" width="100%">';
//			foreach ($record['context'] as $key => $value) {
//				$embeddedTable .= $this->addRow($key, $this->convertToString($value));
//			}
//			$embeddedTable .= '</table>';
//			$output .= $this->addRow('Context', $embeddedTable, false);
//		}
//		if ($record['extra']) {
//			$embeddedTable = '<table cellspacing="1" width="100%">';
//			foreach ($record['extra'] as $key => $value) {
//				$embeddedTable .= $this->addRow($key, $this->convertToString($value));
//			}
//			$embeddedTable .= '</table>';
//			$output .= $this->addRow('Extra', $embeddedTable, false);
//		}

		return $output/*.'</table>'.'</div>'*/;
	}
}
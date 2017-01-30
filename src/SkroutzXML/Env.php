<?php
/**
 * Env.php description
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     170126
 * @package   Pan\SkroutzXML
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */

namespace Pan\SkroutzXML;

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Class Env
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-12-27
 * @since     170126
 * @package   Pan\SkroutzXML
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */
class Env {
    /**
     * @static * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  170126
     */
    public static function maximize_time_memory_limits() {
        $timeLimitSet = set_time_limit( 0 );

        $limit = WP_MAX_MEMORY_LIMIT;

        if ( is_admin() && current_user_can( 'manage_options' ) ) {
            $limit = apply_filters( 'admin_memory_limit', $limit );
        }

        ini_set( 'memory_limit', $limit );

        return ini_get( 'memory_limit' ) == $limit && $timeLimitSet && ini_get( 'max_execution_time' ) === '0';
    }

    /**
     * Acquires information about memory usage.
     *
     * @return string `Memory x MB :: Real Memory x MB :: Peak Memory x MB :: Real Peak Memory x MB`.
     */
    public function memory_details() {
        $memory           = $this->bytes_abbr( (float) memory_get_usage() );
        $real_memory      = $this->bytes_abbr( (float) memory_get_usage( true ) );
        $peak_memory      = $this->bytes_abbr( (float) memory_get_peak_usage() );
        $real_peak_memory = $this->bytes_abbr( (float) memory_get_peak_usage( true ) );

        $details = 'Memory ' . $memory .
                   ' :: Real Memory ' . $real_memory .
                   ' :: Peak Memory ' . $peak_memory .
                   ' :: Real Peak Memory ' . $real_peak_memory;

        return $details;
    }

    /**
     * @param     $bytes
     * @param int $precision
     *
     * @return string
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  170126
     */
    public function bytes_abbr( $bytes, $precision = 2 ) {
        $precision = ( $precision >= 0 ) ? $precision : 2;
        $units     = array( 'bytes', 'kbs', 'MB', 'GB', 'TB' );

        $bytes = ( $bytes > 0 ) ? $bytes : 0;
        $power = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );

        $abbr_bytes = round( $bytes / pow( 1024, $power ), $precision );
        $abbr       = $units[ min( $power, count( $units ) - 1 ) ];

        if ( $abbr_bytes === (float) 1 && $abbr === 'bytes' ) {
            $abbr = 'byte';
        } // Quick fix here.

        else if ( $abbr_bytes === (float) 1 && $abbr === 'kbs' ) {
            $abbr = 'kb';
        } // Quick fix here.

        return $abbr_bytes . ' ' . $abbr;
    }
}
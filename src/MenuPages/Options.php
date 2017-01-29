<?php
/**
 * Options.php description
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-11-20
 * @since     170126
 * @package   Pan\MenuPages
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */

namespace Pan\MenuPages;


/**
 * Class Options
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-11-20
 * @since     170126
 * @package   Pan\MenuPages
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */
class Options {
    /**
     * This is the key of the options array that is used to store page specific options.
     * These options differ from user defined options and are automatically set from the lib.
     */
    const PAGE_OPT = 'pageOptions';

    /**
     * Key of a page option that stores obj statuses
     */
    const PAGE_OPT_STATE = 'state';

    /**
     * The basename of the options. This is used to uniquely identify plugin options,
     * as well as the array name that is stored in DB
     *
     * @var string
     */
    protected $optionsBaseName;
    /**
     * Holds default values for all options. No input field can be declared if a default value is not set for it.
     *
     * @var array
     */
    protected $defaults;
    /**
     * Options as stored in DB.
     * If options are not present or specific options are missing then these
     * take the default values as defined in {@link Options::$defaults}.
     *
     * @var array
     */
    protected $options;

    /**
     * Options constructor.
     *
     * @param string $optionsBaseName The options base name, see {@link Options::$optionsBaseName}
     * @param array  $defaults        Default values for all options, see {@link Options::$defaults}
     *
     * @throws \InvalidArgumentException If $optionsBaseName isn't a string or is empty
     * @since  170126
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     */
    protected function __construct( $optionsBaseName, array $defaults ) {
        if ( ! is_string( $optionsBaseName ) || empty( $optionsBaseName ) ) {
            throw new \InvalidArgumentException( 'Options basename cannot be empty' );
        }

        $this->optionsBaseName = $optionsBaseName;
        $this->defaults        = $defaults;

        if ( ! ( isset( $this->defaults[ self::PAGE_OPT ] ) && is_array( $this->defaults[ self::PAGE_OPT ] ) ) ) {
            $this->defaults[ self::PAGE_OPT ] = [ ];
        }

        $options = get_option( $this->optionsBaseName );

        if ( $options === false ) {
            $this->options = $this->defaults;
            $this->save();
        } else {
            $this->options = (array) $options;
        }

        $this->options = array_merge( $this->defaults, $this->options );
    }

    /**
     * Always call this to get an instance of {@link Options} obj
     *
     * @param string $optionsBaseName See {@link Options::__construct}
     * @param array  $defaults        See {@link See {@link Options::__construct}}
     *
     * @return $this
     * @see    Options::__construct
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  170126
     */
    public static function getInstance( $optionsBaseName, array $defaults = [ ] ) {
        static $instance = [ ];
        if ( ! isset( $instance[ $optionsBaseName ] ) ) {
            $instance[ $optionsBaseName ] = new static( $optionsBaseName, $defaults );
        }

        return $instance[ $optionsBaseName ];
    }

    /**
     * Save options that are currently stored in {@link Options::$options}
     *
     * @return bool
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  170126
     */
    protected function save() {
        /**
         * Filter options array before saving to DB
         *
         * @param array $options Options to be saved
         *
         * @since 170126
         */
        $this->options = apply_filters( "MenuPages\\Options::save@{$this->optionsBaseName}", $this->options );

        return update_option( $this->optionsBaseName, $this->options );
    }

    /**
     * Get the option value
     *
     * @param string $name The name of the option
     *
     * @return mixed
     * @throws \ErrorException
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  170126
     */
    public function get( $name ) {
        if ( $this->exists( $name ) ) {
            /**
             * Filters the value to be returned by Options obj
             *
             * @param mixed $optionName The value to be returned by Options obj
             *
             * @since 170126
             */
            return apply_filters( "MenuPages\\Options::get@{$this->optionsBaseName}", $this->options[ $name ] );
        }
        throw new \ErrorException( 'Invalid option in ' . __METHOD__ );
    }

    /**
     * Checks if an option exists
     *
     * @param string $name The name of the option
     *
     * @return bool
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  170126
     */
    public function exists( $name ) {
        /**
         * Checks if a value is set in options array
         *
         * @param bool $optionExists True if exists, false otherwise
         *
         * @since 170126
         */
        return apply_filters( "MenuPages\\Options::exists@{$this->optionsBaseName}", isset( $this->options[ $name ] ) );
    }

    /**
     * Sets the value of an option. ** No validation ** taking place here so be careful.
     *
     * @param string $name  The name of the option
     * @param mixed  $value The option value
     *
     * @return bool The result of {@link Options::save()}
     * @throws \ErrorException If the option don't exist
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  170126
     */
    public function set( $name, $value ) {
        if ( $this->exists( $name ) ) {
            $this->options[ $name ] = $value;

            return $this->save();
        }
        throw new \ErrorException( 'Invalid option in ' . __METHOD__ );
    }

    /**
     * Uses an assoc array to set option values. Any option in the array that don't exist will be unset.
     *
     * @param array $newOptions Assoc array `[ $optionName => $optionValue ]`
     *
     * @return bool The result of {@link Options::save()}
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  170126
     */
    public function setArray( array $newOptions ) {
        foreach ( $newOptions as $name => $value ) {
            if ( ! $this->exists( $name ) ) {
                unset( $newOptions[ $name ] );
            }
        }

        $this->options = array_merge( $this->options, $newOptions );

        return $this->save();
    }

    public function addOptions($data){
        foreach ( $data as $key => $defaultValue ) {
            if(!isset($this->options[$key])){
                $this->options[$key] = $defaultValue;
            }if(!isset($this->defaults[$key])){
                $this->defaults[$key] = $defaultValue;
            }
        }
        $this->save();
    }

    /**
     * Get the default value of an option.
     *
     * @param string $name The name of the option
     *
     * @return mixed
     * @throws \ErrorException If the option don't exist
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @since  170126
     */
    public function def( $name ) {
        if ( $this->exists( $name ) ) {
            return $this->defaults[ $name ];
        }
        throw new \ErrorException( 'Invalid option in ' . __METHOD__ );
    }

    /**
     * @return array
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @see    Options::$defaults
     * @since  170126
     * @codeCoverageIgnore
     */
    public function getDefaults() {
        return $this->defaults;
    }

    /**
     * @return array
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @see    Options::$options
     * @since  170126
     * @codeCoverageIgnore
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @return string
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @see    Options::$optionsBaseName
     * @since  170126
     * @codeCoverageIgnore
     */
    public function getOptionsBaseName() {
        return $this->optionsBaseName;
    }

    /**
     * prevent the instance from being cloned
     *
     * @return void
     */
    protected function __clone() {
    }

    /**
     * prevent from being un-serialized
     *
     * @return void
     */
    protected function __wakeup() {
    }
}


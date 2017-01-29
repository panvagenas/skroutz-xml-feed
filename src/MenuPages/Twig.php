<?php
/**
 * Twig.php description
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-11-20
 * @since     170126
 * @package   Pan\MenuPages\Templates
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */

namespace Pan\MenuPages;


/**
 * Class Twig
 *
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @date      2015-11-20
 * @since     170126
 * @package   Pan\MenuPages\Templates
 * @copyright Copyright (c) 2015 Panagiotis Vagenas
 */
class Twig {
    /**
     * @var \Twig_Environment
     */
    protected $twigEnvironment;
    /**
     * @var \Twig_Loader_Filesystem
     */
    protected $twigLoader;
    /**
     * @var string
     */
    protected $defaultPaths = [ ];
    /**
     * @var string
     */
    protected $cachePath;

    const DEV = 1;

    public function __construct( $templatesDir ) {
        $this->defaultPaths[] = $templatesDir;

        $sysTmpDir = sys_get_temp_dir();

        if ( file_exists( $sysTmpDir ) && is_writable( $sysTmpDir ) ) {
            $this->cachePath = trailingslashit( $sysTmpDir ) . 'twig/cache';
        }

        $twigOptions = [ ];

        if ( $this->cachePath ) {
            $twigOptions['cache'] = $this->cachePath;
        }

        if ( self::DEV ) {
            $twigOptions['debug']            = true;
            $twigOptions['auto_reload']      = true;
            $twigOptions['strict_variables'] = true;
        }

        $this->twigLoader      = new \Twig_Loader_Filesystem( $this->defaultPaths );
        $this->twigEnvironment = new \Twig_Environment( $this->twigLoader, $twigOptions );

        if ( self::DEV ) {
            $this->twigEnvironment->addExtension( new \Twig_Extension_Debug() );
        }
    }

    public function loadAndRender($template, $vars){
        if(!preg_match('/\.twig$/', $template)){
            $template .= '.twig';
        }
        return $this->twigEnvironment->render($template, $vars);
    }

    /**
     * @return \Twig_Environment
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @see    Twig::$twigEnvironment
     * @since  170126
     * @codeCoverageIgnore
     */
    public function getTwigEnvironment() {
        return $this->twigEnvironment;
    }

    /**
     * @return \Twig_Loader_Filesystem
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @see    Twig::$twigLoader
     * @since  170126
     * @codeCoverageIgnore
     */
    public function getTwigLoader() {
        return $this->twigLoader;
    }

    /**
     * @return string
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @see    Twig::$defaultPaths
     * @since  170126
     * @codeCoverageIgnore
     */
    public function getDefaultPaths() {
        return $this->defaultPaths;
    }

    /**
     * @return string
     * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
     * @see    Twig::$cachePath
     * @since  170126
     * @codeCoverageIgnore
     */
    public function getCachePath() {
        return $this->cachePath;
    }
}
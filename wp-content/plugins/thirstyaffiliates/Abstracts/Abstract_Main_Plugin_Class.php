<?php
namespace ThirstyAffiliates\Abstracts;

use ThirstyAffiliates\Interfaces\Model_Interface;

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Abstract class that the main plugin class needs to extend.
 *
 * @since 3.0.0
 */
abstract class Abstract_Main_Plugin_Class {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    /**
     * Property that houses an array of all the "regular models" of the plugin.
     *
     * @since 3.0.0
     * @access protected
     * @var array
     */
    protected $__all_models = array();

    /**
     * Property that houses an array of all "public regular models" of the plugin.
     * Public models can be accessed and utilized by external entities via the main plugin class.
     *
     * @since 3.0.0
     * @access public
     * @var array
     */
    public $models = array();

    /**
     * Property that houses an array of all "public helper classes" of the plugin.
     *
     * @since 3.0.0
     * @access public
     * @var array
     */
    public $helpers = array();




    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Add a "regular model" to the main plugin class "all models" array.
     *
     * @since 3.0.0
     * @access public
     *
     * @param Model_Interface $model Regular model.
     */
    public function add_to_all_plugin_models( Model_Interface $model ) {

        $class_reflection = new \ReflectionClass( $model );
        $class_name       = $class_reflection->getShortName();

        if ( !array_key_exists( $class_name , $this->__all_models ) )
            $this->__all_models[ $class_name ] = $model;

    }

    /**
     * Add a "regular model" to the main plugin class "public models" array.
     *
     * @since 3.0.0
     * @access public
     *
     * @param Model_Interface $model Regular model.
     */
    public function add_to_public_models( Model_Interface $model ) {

        $class_reflection = new \ReflectionClass( $model );
        $class_name       = $class_reflection->getShortName();
        
        if ( !array_key_exists( $class_name , $this->models ) )
            $this->models[ $class_name ] = $model;

    }

    /**
     * Add a "helper class instance" to the main plugin class "public helpers" array.
     *
     * @since 3.0.0
     * @access public
     *
     * @param object $helper Helper class instance.
     */
    public function add_to_public_helpers( $helper ) {

        $class_reflection = new \ReflectionClass( $helper );
        $class_name       = $class_reflection->getShortName();

        if ( !array_key_exists( $class_name , $this->helpers ) )
            $this->helpers[ $class_name ] = $helper;

    }

}

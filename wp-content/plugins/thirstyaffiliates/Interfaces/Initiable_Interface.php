<?php
namespace ThirstyAffiliates\Interfaces;

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Abstraction that provides contract relating to initialization.
 * Any model that needs some sort of initialization must implement this interface.
 *
 * @since 3.0.0
 */
interface Initiable_Interface {

    /**
     * Contruct for initialization.
     *
     * @since 3.0.0
     * @access public
     */
    public function initialize();

}
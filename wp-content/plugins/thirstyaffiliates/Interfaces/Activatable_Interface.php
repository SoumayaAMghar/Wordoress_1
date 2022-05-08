<?php
namespace ThirstyAffiliates\Interfaces;

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Abstraction that provides contract relating to activation.
 * Any model that needs some sort of activation must implement this interface.
 *
 * @since 3.0.0
 */
interface Activatable_Interface {

    /**
     * Contruct for activation.
     *
     * @since 3.0.0
     * @access public
     */
    public function activate();

}
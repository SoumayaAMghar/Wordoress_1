<?php

spl_autoload_register( function ( $class ) {

    $namespace = 'MailerLiteForms\\';
    $path      = 'src';

    // Bail if the class is not in our namespace.
    if ( 0 !== strpos( $class, $namespace ) ) {
        return;
    }

    // Remove the namespace.
    $class = str_replace( $namespace, '', $class );

    // Build the filename.
    $file = realpath( __DIR__ . "/{$path}" );
    $file = $file . DIRECTORY_SEPARATOR . str_replace( '\\', DIRECTORY_SEPARATOR, $class ) . '.php';

    // If the file exists for the class name, load it.
    if ( is_readable( $file ) ) {
        require_once( $file );
    }

} );

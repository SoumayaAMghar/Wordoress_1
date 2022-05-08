import * as taimage from "./ta-image";

const { registerBlockType } = wp.blocks;

/**
 * Register gutenberg blocks.
 * 
 * @since 3.6
 */
export default function registerBlocks() {

    [
        taimage
    ].forEach( ( block ) => {
        if ( ! block ) return;

        const { name , settings } = block;
        registerBlockType( name , settings );
    } );
}
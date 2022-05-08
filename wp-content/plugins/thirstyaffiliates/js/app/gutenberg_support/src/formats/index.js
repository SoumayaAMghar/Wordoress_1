import { taLink } from "./ta-link";

const { registerFormatType } = wp.richText;

/**
 * Register custom formats.
 * 
 * @since 3.6
 */
export default function registerFormats() {

    [
        taLink
    ].forEach( ( { name , ...settings } ) => registerFormatType( name , settings ) );
}
const WORDPRESS_EMBED_BLOCK = 'core-embed/wordpress';

const { includes } = lodash;
const { renderToString } = wp.element;
const { createBlock } = wp.blocks;

/***
 * Creates a more suitable embed block based on the passed in props
 * and attributes generated from an embed block's preview.
 *
 * We require `attributesFromPreview` to be generated from the latest attributes
 * and preview, and because of the way the react lifecycle operates, we can't
 * guarantee that the attributes contained in the block's props are the latest
 * versions, so we require that these are generated separately.
 * See `getAttributesFromPreview` in the generated embed edit component.
 *
 * @param {Object}            props                 The block's props.
 * @param {Object}            attributesFromPreview Attributes generated from the block's most up to date preview.
 * @return {Object|undefined} A more suitable embed block if one exists.
 */
export const createUpgradedEmbedBlock = ( props, attributesFromPreview ) => {
	const { preview, name } = props;
	const { url } = props.attributes;

	if ( ! url ) {
		return;
	}

	const matchingBlock = findBlock( url );

	// WordPress blocks can work on multiple sites, and so don't have patterns,
	// so if we're in a WordPress block, assume the user has chosen it for a WordPress URL.
	if ( WORDPRESS_EMBED_BLOCK !== name && DEFAULT_EMBED_BLOCK !== matchingBlock ) {
		// At this point, we have discovered a more suitable block for this url, so transform it.
		if ( name !== matchingBlock ) {
			return createBlock( matchingBlock, { url } );
		}
	}

	if ( preview ) {
		const { html } = preview;

		// We can't match the URL for WordPress embeds, we have to check the HTML instead.
		if ( isFromWordPress( html ) ) {
			// If this is not the WordPress embed block, transform it into one.
			if ( WORDPRESS_EMBED_BLOCK !== name ) {
				return createBlock(
					WORDPRESS_EMBED_BLOCK,
					{
						url,
						// By now we have the preview, but when the new block first renders, it
						// won't have had all the attributes set, and so won't get the correct
						// type and it won't render correctly. So, we pass through the current attributes
						// here so that the initial render works when we switch to the WordPress
						// block. This only affects the WordPress block because it can't be
						// rendered in the usual Sandbox (it has a sandbox of its own) and it
						// relies on the preview to set the correct render type.
						...attributesFromPreview,
					}
				);
			}
		}
	}
};

export const isFromWordPress = ( html ) => {
	return includes( html, 'class="wp-embedded-content" data-secret' );
};
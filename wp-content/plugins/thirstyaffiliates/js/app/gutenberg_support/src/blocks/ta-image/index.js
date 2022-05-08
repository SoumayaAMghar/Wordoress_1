import classnames from "classnames";

const { Fragment } = wp.element;
const { __ } = wp.i18n;
const { getPhrasingContentSchema } = wp.dom.getPhrasingContentSchema ? wp.dom : wp.blocks;
const { RichText } = wp.blockEditor ? wp.blockEditor : wp.editor;
const { Path , SVG } = wp.components;

import edit from "./edit";

export const name = 'ta/image';

const blockAttributes = {
	url: {
		type: 'string',
		source: 'attribute',
		selector: 'img',
		attribute: 'src',
	},
	alt: {
		type: 'string',
		source: 'attribute',
		selector: 'img',
		attribute: 'alt',
		default: '',
	},
	caption: {
		type: 'string',
		source: 'html',
		selector: 'figcaption',
	},
	id: {
		type: 'number',
	},
	align: {
		type: 'string',
	},
	width: {
		type: 'number',
	},
	height: {
		type: 'number',
	},
	linkid: {
		type: 'number',
	},
	href: {
		type: 'string',
		source: 'attribute',
		selector: 'ta',
		attribute: 'href'
	},
	affiliateLink: {
		type: 'object'
	}
};

const imageSchema = {
	img: {
		attributes: [ 'src', 'alt' ],
		classes: [ 'alignleft', 'aligncenter', 'alignright', 'alignnone', /^wp-image-\d+$/ ],
	},
};

const schema = {
	figure: {
		require: [ 'ta' , 'img' ],
		children: {
			ta: {
				attributes: [ 'href', 'linkid' ],
				children: imageSchema,
			},
			figcaption: {
				children: getPhrasingContentSchema(),
			},
		},
	},
};

function getFirstAnchorAttributeFormHTML( html, attributeName ) {
	const { body } = document.implementation.createHTMLDocument( '' );

	body.innerHTML = html;

	const { firstElementChild } = body;

	if (
		firstElementChild &&
		firstElementChild.nodeName === 'A'
	) {
		return firstElementChild.getAttribute( attributeName ) || undefined;
	}
}

export const settings = {
	title: __( 'ThirstyAffiliates Image' ),

	description: __( 'Insert an image with an affiliate link to make a visual statement.' ),

	icon: <SVG viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><Path d="M0,0h24v24H0V0z" fill="none" /><Path d="m19 5v14h-14v-14h14m0-2h-14c-1.1 0-2 0.9-2 2v14c0 1.1 0.9 2 2 2h14c1.1 0 2-0.9 2-2v-14c0-1.1-0.9-2-2-2z" /><Path d="m14.14 11.86l-3 3.87-2.14-2.59-3 3.86h12l-3.86-5.14z" /></SVG>,

	category: 'common',

	keywords: [
		'img', // "img" is not translated as it is intended to reflect the HTML <img> tag.
		__( 'photo' ),
		__( 'affiliate' )
	],

	attributes: blockAttributes,

	getEditWrapperProps( attributes ) {
		const { align, width } = attributes;
		if ( 'left' === align || 'center' === align || 'right' === align || 'wide' === align || 'full' === align ) {
			return { 'data-align': align, 'data-resized': !! width };
		}
	},

	edit,

	save( { attributes } ) {
		const {
			url,
			alt,
			caption,
			align,
			width,
			height,
			id,
			linkid,
			href
		} = attributes;

		const classes = classnames( {
			[ `align${ align }` ]: align,
			'is-resized': width || height,
		} );

		const image = (
			<img
				src={ url }
				alt={ alt }
				className={ id ? `wp-image-${ id }` : null }
				width={ width }
				height={ height }
			/>
		);

		const figure = (
			<Fragment>
				<ta linkid={ linkid } href={ href }>
				{ image }
				</ta>
				<RichText.Content tagName="figcaption" value={ caption } />
			</Fragment>
		);

		if ( 'left' === align || 'right' === align || 'center' === align ) {
			return (
				<div className='wp-block-image'>
					<figure className={ classes }>
						{ figure }
					</figure>
				</div>
			);
		}

		return (
			<figure className={ `wp-block-image ${classes}` }>
				{ figure }
			</figure>
		);
	}
};

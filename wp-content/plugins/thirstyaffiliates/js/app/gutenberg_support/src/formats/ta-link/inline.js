import classnames from "classnames";
import { isValidHref } from './utils';
import ThirstyURLPopover from './url-popover';
import ThirstyURLInput from './url-input';

const { __ } = wp.i18n;
const { Component , createRef } = wp.element;
const { ExternalLink , ToggleControl , IconButton , withSpokenMessages } = wp.components;
const { LEFT, RIGHT, UP, DOWN, BACKSPACE, ENTER } = wp.keycodes;
const { prependHTTP , safeDecodeURI , filterURLForDisplay } = wp.url;
const { create , insert , isCollapsed , applyFormat , getTextContent , slice } = wp.richText;

const stopKeyPropagation = ( event ) => event.stopPropagation();

/**
 * Generates the format object that will be applied to the link text.
 *
 * @since 3.6
 *
 * @param {string}  url    The href of the link.
 * @param {boolean} linkid Affiliate link ID.
 * @param {Object}  text   The text that is being hyperlinked.
 *
 * @return {Object} The final format object.
 */
function createLinkFormat( { url , linkid , text } ) {
	const format = {
		type: "ta/link",
		attributes: {
			url,
			linkid : linkid.toString()
		},
	};

	return format;
}

/**
 * Check if input is being show.
 *
 * @since 3.6
 *
 * @param {Object} props Component props.
 * @param {Object} state Component state.
 */
function isShowingInput( props , state ) {
	return props.addingLink || state.editLink;
}

/**
 * Affiliate Link editor JSX element.
 *
 * @since 3.6
 *
 * @param {Object} param0 Component props (destructred).
 */
const LinkEditor = ( { value , onChangeInputValue , onKeyDown , submitLink, invalidLink , resetInvalidLink , autocompleteRef } ) => (
	// Disable reason: KeyPress must be suppressed so the block doesn't hide the toolbar
	/* eslint-disable jsx-a11y/no-noninteractive-element-interactions */
	<form
		className="editor-format-toolbar__link-container-content block-editor-format-toolbar__link-container-content ta-link-search-popover"
		onKeyPress={ stopKeyPropagation }
		onKeyDown={ onKeyDown }
		onSubmit={ submitLink }
	>
		<ThirstyURLInput
			value={ value }
			onChange={ onChangeInputValue }
			autocompleteRef={ autocompleteRef }
			invalidLink={ invalidLink }
			resetInvalidLink={ resetInvalidLink }
		/>
		<IconButton icon="editor-break" label={ __( 'Apply' ) } type="submit" />
	</form>
	/* eslint-enable jsx-a11y/no-noninteractive-element-interactions */
);

/**
 * Affiliate link url viewer JSX element.
 *
 * @param {*} param0 Component props (destructred).
 */
const LinkViewerUrl = ( { url } ) => {
	const prependedURL = prependHTTP( url );
	const linkClassName = classnames( 'editor-format-toolbar__link-container-value block-editor-format-toolbar__link-container-value', {
		'has-invalid-link': ! isValidHref( prependedURL ),
	} );

	if ( ! url ) {
		return <span className={ linkClassName }></span>;
	}

	return (
		<ExternalLink
			className={ linkClassName }
			href={ url }
		>
			{ filterURLForDisplay( safeDecodeURI( url ) ) }
		</ExternalLink>
	);
};

/**
 * Affiliate link viewer JSX element.
 *
 * @param {*} param0 Component props (destructred).
 */
const LinkViewer = ( { url, editLink } ) => {
	return (
		// Disable reason: KeyPress must be suppressed so the block doesn't hide the toolbar
		/* eslint-disable jsx-a11y/no-static-element-interactions */
		<div
			className="editor-format-toolbar__link-container-content block-editor-format-toolbar__link-container-content"
			onKeyPress={ stopKeyPropagation }
		>
			<LinkViewerUrl url={ url } />
			<IconButton icon="edit" label={ __( 'Edit' ) } onClick={ editLink } />
		</div>
		/* eslint-enable jsx-a11y/no-static-element-interactions */
	);
};

/**
 * Inline affiliate link UI Component.
 *
 * @since 3.6
 *
 * @param {*} param0 Component props (destructred).
 */
class InlineAffiliateLinkUI extends Component {
	constructor() {
		super( ...arguments );

		this.editLink = this.editLink.bind( this );
		this.submitLink = this.submitLink.bind( this );
		this.onKeyDown = this.onKeyDown.bind( this );
		this.onChangeInputValue = this.onChangeInputValue.bind( this );
		this.onClickOutside = this.onClickOutside.bind( this );
		this.resetState = this.resetState.bind( this );
		this.autocompleteRef = createRef();
		this.resetInvalidLink = this.resetInvalidLink.bind( this );

		this.state = {
			inputValue : '',
			linkid : 0,
			post : null,
			invalidLink : false
		};
	}

	/**
	 * Stop the key event from propagating up to ObserveTyping.startTypingInTextField.
	 *
	 * @since 3.6
	 *
	 * @param {Object} event Event object.
	 */
	onKeyDown( event ) {
		if ( [ LEFT, DOWN, RIGHT, UP, BACKSPACE, ENTER ].indexOf( event.keyCode ) > -1 ) {
			event.stopPropagation();
		}
	}

	/**
	 * Callback to set state when input value is changed.
	 *
	 * @since 3.6
	 *
	 * @param {*} inputValue
	 * @param {*} post
	 */
	onChangeInputValue( inputValue , post = null ) {
		const linkid = post ? post.id : 0;
		this.setState( { inputValue , linkid , post } );
	}

	/**
	 * Callback to set state when edit affiliate link (already inserted) is triggered.
	 *
	 * @since 3.6
	 *
	 * @param {*} event
	 */
	editLink( event ) {
		this.setState( { editLink: true } );
		event.preventDefault();
	}

	/**
	 * Callback to apply the affiliate link format to the selected text or position in the active block.
	 *
	 * @since 3.6
	 *
	 * @param {*} event
	 */
	submitLink( event ) {
		const { isActive, value, onChange, speak } = this.props;
		const { inputValue, linkid , post } = this.state;
		const url = prependHTTP( inputValue );
		const selectedText = getTextContent( slice( value ) );
		const format = createLinkFormat( {
			url,
			linkid,
			text: selectedText,
		} );

		event.preventDefault();

		if ( ! linkid || ! post ) {
			this.setState( { invalidLink : true } )
			return;
		}

		if ( isCollapsed( value ) && ! isActive ) {
			const toInsert = applyFormat( create( { text: post.title } ), format, 0, url.length );
			onChange( insert( value, toInsert ) );
		} else {
			onChange( applyFormat( value, format ) );
		}

		this.resetState();

		if ( ! isValidHref( url ) ) {
			speak( __( 'Warning: the link has been inserted but may have errors. Please test it.' ), 'assertive' );
		} else if ( isActive ) {
			speak( __( 'Link edited.' ), 'assertive' );
		} else {
			speak( __( 'Link inserted' ), 'assertive' );
		}
	}

	/**
	 * Callback to run when users clicks outside the popover UI.
	 *
	 * @since 3.6
	 *
	 * @param {*} event
	 */
	onClickOutside( event ) {
		// The autocomplete suggestions list renders in a separate popover (in a portal),
		// so onClickOutside fails to detect that a click on a suggestion occured in the
		// LinkContainer. Detect clicks on autocomplete suggestions using a ref here, and
		// return to avoid the popover being closed.
		const autocompleteElement = this.autocompleteRef.current;
		if ( autocompleteElement && autocompleteElement.contains( event.target ) ) {
			return;
		}

		this.resetState();
	}

	/**
	 * Reset state callback.
	 *
	 * @since 3.6
	 */
	resetState() {
		this.props.stopAddingLink();
		this.setState( { inputValue : '' , editLink: false } );
		this.resetInvalidLink();
	}

	/**
	 * Reset invalid link state callback. Separated as we need to run this independently from resetState() callback.
	 *
	 * @since 3.6
	 */
	resetInvalidLink() {
		this.setState( { invalidLink : false } );
	}

	/**
	 * Component render method.
	 *
	 * @since 3.6
	 */
	render() {
		const { isActive, activeAttributes: { url , linkid }, addingLink, anchorRect, value, onChange } = this.props;

		if ( ! isActive && ! addingLink ) {
			return null;
		}

		const { inputValue , invalidLink } = this.state;
		const showInput = isShowingInput( this.props, this.state );

		return (
			<ThirstyURLPopover
				onClickOutside={ this.onClickOutside }
				onClose={ this.resetState }
				focusOnMount={ showInput ? 'firstElement' : false }
				invalidLink={ invalidLink }
                anchorRect={ anchorRect }
			>
				{ showInput ? (
					<LinkEditor
						value={ inputValue }
						onChangeInputValue={ this.onChangeInputValue }
						onKeyDown={ this.onKeyDown }
						submitLink={ this.submitLink }
						autocompleteRef={ this.autocompleteRef }
						updateLinkId= { this.updateLinkId }
						invalidLink={ invalidLink }
						resetInvalidLink={ this.resetInvalidLink }
					/>
				) : (
					<LinkViewer
						url={ url }
						editLink={ this.editLink }
					/>
				) }
			</ThirstyURLPopover>
		);
	}
}

export default withSpokenMessages( InlineAffiliateLinkUI );

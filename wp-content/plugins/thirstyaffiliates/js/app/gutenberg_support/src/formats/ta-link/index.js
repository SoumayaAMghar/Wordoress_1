import InlineAffiliateLinkUI from './inline';

const { __ } = wp.i18n;
const { Component , Fragment } = wp.element;
const { withSpokenMessages } = wp.components;
const { getTextContent , applyFormat , removeFormat , slice } = wp.richText;
const { isURL } = wp.url;
const { RichTextToolbarButton , RichTextShortcut } = wp.blockEditor ? wp.blockEditor : wp.editor;
const { Path , SVG } = wp.components;
const { getRectangleFromRange } = wp.dom;

const name = "ta/link";
let lastAnchorRect = null;

/**
 * Custom Affiliate link format. When applied will wrap selected text with <ta href="" linkid=""></ta> custom element.
 * Custom element is implemented here as we are not allowed to use <a> tag due to Gutenberg limitations.
 * Element is converted to normal <a> tag on frontend via PHP script filtered on 'the_content'.
 *
 * @since 3.6
 */
export const taLink = {
    name,
    title      : __( "Affiliate Link" ),
    tagName    : "ta",
    className  : null,
    attributes : {
		url    : "href",
		target : "target"
    },
    edit : withSpokenMessages( class LinkEdit extends Component {

		/**
		 * Component constructor.
		 *
		 * @since 3.6
		 */
		constructor() {
			super( ...arguments );

			this.addLink = this.addLink.bind( this );
			this.stopAddingLink = this.stopAddingLink.bind( this );
			this.onRemoveFormat = this.onRemoveFormat.bind( this );
			this.state = {
				addingLink: false,
			};
		}

		/**
		 * Callback to set state to adding link status.
		 *
		 * @since 3.6
		 */
		addLink() {

			const { value, onChange } = this.props;
			const text = getTextContent( slice( value ) );

			if ( text && isURL( text ) ) {
				onChange( applyFormat( value, { type: name, attributes: { url: text } } ) );
			} else {
				this.setState( { addingLink: true } );
			}
		}

		/**
		 * Callback to set state to stop adding link status.
		 *
		 * @since 3.6
		 */
		stopAddingLink() {
			this.setState( { addingLink: false } );
		}

		/**
		 * Remove format event callback.
		 *
		 * @since 3.6
		 */
		onRemoveFormat() {
			const { value , onChange , speak } = this.props;

			onChange( removeFormat( value , name ) );
			speak( __( "Affiliate Link removed." ), "assertive" );
		}

		/**
		 * Get selected anchor text.
		 *
		 * @since 3.9
		 */
		getAnchorRect() {

			const selection = window.getSelection();
			const range = selection.rangeCount > 0 ? selection.getRangeAt( 0 ) : null;

			if ( ! range ) return;

			// Fix for the link UI jumping around the screen on WP 5.6
			if(selection.anchorNode.nodeType !== window.Node.TEXT_NODE) {
			    return lastAnchorRect;
            }

			let rect = null;

			if ( this.state.addingLink ) {
				rect = getRectangleFromRange( range );
			} else {

				let element = range.startContainer;

				// If the caret is right before the element, select the next element.
				element = element.nextElementSibling || element;

				while ( element.nodeType !== window.Node.ELEMENT_NODE ) {
					element = element.parentNode;
				}

				const closest = element.closest( 'ta' );
				if ( closest ) {
					rect = closest.getBoundingClientRect();
				}

			}

            lastAnchorRect = rect;

			return rect;
		}

		/**
		 * Component render method.
		 *
		 * @since 3.6
		 */
		render() {
			const { isActive , activeAttributes , value , onChange } = this.props;

			const anchorRect = this.getAnchorRect();

			return (
				<Fragment>
					<RichTextShortcut
						type="access"
						character="s"
						onUse={ this.onRemoveFormat }
					/>
					<RichTextShortcut
						type="primary"
						character="l"
						onUse={ this.addLink }
					/>
					<RichTextShortcut
						type="primaryShift"
						character="l"
						onUse={ this.onRemoveFormat }
					/>
					{ isActive && <RichTextToolbarButton
						icon="editor-unlink"
						title={ __( 'Remove Affiliate Link' ) }
						className="ta-unlink-button"
						onClick={ this.onRemoveFormat }
						isActive={ isActive }
						shortcutType="primaryShift"
						shortcutCharacter="l"
					/> }
					{ ! isActive && <RichTextToolbarButton
						icon={ <SVG xmlns="http://www.w3.org/2000/svg" width="16.688" height="9.875" viewBox="0 0 16.688 9.875"><Path id="TA.svg" fill="black" class="cls-1" d="M2.115,15.12H4.847L6.836,7.7H9.777l0.63-2.381H1.821L1.177,7.7H4.118Zm4.758,0H9.829l1.177-1.751h3.782l0.238,1.751h2.858L16.357,5.245H13.7Zm5.5-3.866,1.835-2.816,0.35,2.816H12.378Z" transform="translate(-1.188 -5.25)"/></SVG> }
						title={ __( 'Affiliate Link' ) }
						className="ta-link-button"
						onClick={ this.addLink }
						shortcutType="primary"
						shortcutCharacter="l"
					/> }
					<InlineAffiliateLinkUI
						addingLink={ this.state.addingLink }
						stopAddingLink={ this.stopAddingLink }
						isActive={ isActive }
						activeAttributes={ activeAttributes }
                        anchorRect={ anchorRect }
						value={ value }
						onChange={ onChange }
					/>
				</Fragment>
			);
		}
    } )
}

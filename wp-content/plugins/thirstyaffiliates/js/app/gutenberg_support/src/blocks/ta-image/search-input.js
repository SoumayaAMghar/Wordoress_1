import classnames from "classnames";

const { __ } = wp.i18n;
const { Component , createRef } = wp.element;
const { Spinner, withSpokenMessages, Popover , TextControl } = wp.components;
const { withInstanceId } = wp.compose;

class ThirstyURLInput extends Component {

    /**
     * Component constructor method.
     * 
     * @since 3.6
     * 
     * @param {*} param0 
     */
	constructor( { autocompleteRef } ) {
		super( ...arguments );

		// this.onChange = this.onChange.bind( this );
		// this.onKeyDown = this.onKeyDown.bind( this );
		this.autocompleteRef = autocompleteRef || createRef();
		this.inputRef = createRef();
		this.searchAffiliateLinks = this.searchAffiliateLinks.bind( this );
		// this.updateSuggestions = throttle( this.updateSuggestions.bind( this ), 200 );

		this.suggestionNodes = [];

		this.state = {
			posts : [],
			showSuggestions : false,
			selectedSuggestion : null,
			loading : false
		};
	}

	/**
     * Component did update method.
     * 
     * @since 3.6
     */
	componentDidUpdate() {
		const { showSuggestions, selectedSuggestion } = this.state;
		// only have to worry about scrolling selected suggestion into view
		// when already expanded
		if ( showSuggestions && selectedSuggestion !== null && ! this.scrollingIntoView ) {
			this.scrollingIntoView = true;
			scrollIntoView( this.suggestionNodes[ selectedSuggestion ], this.autocompleteRef.current, {
				onlyScrollIfNeeded: true,
			} );

			setTimeout( () => {
				this.scrollingIntoView = false;
			}, 100 );
		}
	}

	/**
     * Component unmount method.
     * 
     * @since 3.6
     */
	componentWillUnmount() {
		delete this.suggestionsRequest;
	}

	/**
     * Bind suggestion to node.
     * 
     * @param {*} index 
     */
	bindSuggestionNode( index ) {
		return ( ref ) => {
			this.suggestionNodes[ index ] = ref;
		};
	}
	
	searchAffiliateLinks( value ) {

		// Show the suggestions after typing at least 2 characters=
		if ( value.length < 2 ) {
			this.setState( {
				showSuggestions: false,
				selectedSuggestion: null,
				loading: false,
			} );

			return;
		}

		this.setState({
			showSuggestions : true,
			selectedSuggestion : null,
			loading : true
		});

		const formData = new FormData();
        formData.append( "action" , "search_affiliate_links_query" );
        formData.append( "keyword" , value );
        formData.append( "paged" , 1 );
		formData.append( "gutenberg" , true );
		formData.append( "with_images" , true );
		
		// We are getting data via the WP AJAX instead of rest API as it is not possible yet
        // to filter results with category value. This is to prepare next update to add category filter.
        const request = fetch( ajaxurl , {
            method : "POST",
            body   : formData
		} );
		
		request
        .then( response => response.json() )
        .then( ( response ) => {

            if ( ! response.affiliate_links ) return;

            const posts = response.affiliate_links;

			// A fetch Promise doesn't have an abort option. It's mimicked by
			// comparing the request reference in on the instance, which is
			// reset or deleted on subsequent requests or unmounting.
			if ( this.suggestionsRequest !== request ) {
				return;
            }

			this.setState( {
				posts,
				loading: false,
            } );

			if ( !! posts.length ) {
				this.props.debouncedSpeak( sprintf( _n(
					'%d result found, use up and down arrow keys to navigate.',
					'%d results found, use up and down arrow keys to navigate.',
					posts.length
				), posts.length ), 'assertive' );
			} else {
				this.props.debouncedSpeak( __( 'No results.' ), 'assertive' );
            }

		} ).catch( () => {
			if ( this.suggestionsRequest === request ) {
				this.setState( {
					loading: false,
				} );
			}
		} );

		this.suggestionsRequest = request;
	}

	/**
     * Set state when an affiliate link is selected.
     * 
     * @since 3.6
     * 
     * @param {*} post 
     */
	selectLink( post ) {
		// this.props.onChange( post.link, post );
		this.setState( {
			selectedSuggestion: post,
			showSuggestions: false,
		} );

		this.props.updateImageSelection( post.images , post );
	}

	/**
     * Callback handler for when affiliate link is selected.
     * 
     * @param {*} post 
     */
	handleOnClick( post ) {
		this.selectLink( post );
		// Move focus to the input field when a link suggestion is clicked.
		// this.inputRef.current.focus();
	}

    render() {
        const { value = '', autoFocus = true, instanceId } = this.props;
        const { showSuggestions , posts, selectedSuggestion , loading } = this.state;
        
        return (
            <div class="edit-search-affiliate-links">
				<form
					className="editor-format-toolbar__link-container-content block-editor-format-toolbar__link-container-content ta-link-search-popover"
					onSubmit={ this.displayAffiliateImages }
				>
					<TextControl
						type="text"
						className="ta-search-affiliate-links"
						placeholder={ __( "Type to search affiliate links" ) }
						onChange={ this.searchAffiliateLinks }
						autocomplete="off"
					/>

					{ ( loading ) && <Spinner /> }

					{ showSuggestions && !! posts.length && 
						<Popover position="bottom" focusOnMount={ false }>
							<div class="affilate-links-suggestions">
								{ posts.map( ( post, index ) => (
									<button
										key={ post.id }
										role="option"
										tabIndex="-1"
										id={ `editor-url-input-suggestion-${ instanceId }-${ index }` }
										ref={ this.bindSuggestionNode( index ) }
										className={ classnames( 'editor-url-input__suggestion block-editor-url-input__suggestion', {
											'is-selected': index === selectedSuggestion,
										} ) }
										onClick={ () => this.handleOnClick( post ) }
										aria-selected={ index === selectedSuggestion }
									>
										{ post.title || __( '(no title)' ) }
									</button>
								) ) }
							</div>
						</Popover>
					}
				</form>
            </div>
        );
    }
}

export default withSpokenMessages( withInstanceId( ThirstyURLInput ) );

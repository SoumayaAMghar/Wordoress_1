const { __ } = wp.i18n;
const { Component } = wp.element;
const { Popover , IconButton } = wp.components;

/**
 * Custom URL Popover component.
 * 
 * @since 3.6
 */
class ThirstyURLPopover extends Component {

    /**
     * Component constructor method.
     * 
     * @since 3.6
     */
	constructor() {
        super( ...arguments );
        
        this.toggleSettingsVisibility = this.toggleSettingsVisibility.bind( this );

		this.state = {
			isSettingsExpanded: false,
		};
    }
    
    /**
     * Component constructor.
     * 
     * @since 3.6
     */
    toggleSettingsVisibility() {
		this.setState( {
			isSettingsExpanded: ! this.state.isSettingsExpanded,
		} );
	}

    /**
     * Component render method.
     * 
     * @since 3.6
     */
	render() {
		const {
			children,
			renderSettings,
            invalidLink,
			position = 'bottom center',
			focusOnMount = 'firstElement',
			...popoverProps
		} = this.props;

		const {
			isSettingsExpanded,
        } = this.state;
        
        const showSettings = !! renderSettings && isSettingsExpanded;

		return (
			<Popover
				className="ta-url-popover editor-url-popover block-editor-url-popover"
				focusOnMount={ focusOnMount }
				position={ position }
				{ ...popoverProps }
			>
				<div className="editor-url-popover__row">
                    { children }
                    { !! renderSettings && (
						<IconButton
							className="editor-url-popover__settings-toggle"
							icon="ellipsis"
							label={ __( 'Link Settings' ) }
							onClick={ this.toggleSettingsVisibility }
							aria-expanded={ isSettingsExpanded }
						/>
                    ) }
				</div>
                { showSettings && (
					<div className="editor-url-popover__row editor-url-popover__settings">
						{ renderSettings() }
					</div>
                ) }
                { invalidLink && <div class="ta-invalid-link">{ __( 'Invalid affiliate link' ) }</div> }
			</Popover>
		);
	}
}

export default ThirstyURLPopover;
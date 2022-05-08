
(function (blocks, components, element, editor, i18n, ajax) {

    const icon = element.createElement('svg',
        {
            width: 21,
            height: 21,
            viewBox: '0 0 21 21'
        },
        element.createElement( 'g',
            {
                id: 'Page-1',
                stroke: 'none',
                strokeWidth: 1,
                fill: 'none',
                fillRule: 'evenodd'
            },
            element.createElement( 'g',
                {
                    id: 'mailerlitelogo',
                    transform: 'translate(0.198319, 0.325455)',
                    fill: '#09C269',
                    fillRule: 'nonzero'
                },
                element.createElement( 'path',
                    {
                        id: 'Shape-path',
                        d: "M17.2807581,0.115646258 L2.78853487,0.115646258 C1.28807741,0.115646258 0.0437956203,1.34864717 0.0437956203,2.8355012 L0.0437956203,11.9016843 L0.0437956203,13.6786562 L0.0437956203,20.1156463 L3.83153579,16.3985111 L17.2990564,16.3985111 C18.7995138,16.3985111 20.0437956,15.1655103 20.0437956,13.6786562 L20.0437956,2.8355012 C20.0254974,1.3305148 18.7995138,0.115646258 17.2807581,0.115646258 Z"
                    }
                )
            )
        )
    );

    const {
        __
    } = i18n;

    blocks.registerBlockType( 'mailerlite/form-block', {

        title: __('MailerLite signup form', 'mailerlite'),
        icon: icon,
        category: 'widgets',
        attributes: {
            form_id: {
                type: 'string',
                default: '0'
            },
            editMode: {
                type: 'boolean',
                default: false,
            },
            forms: {
                type: 'array',
                default: []
            },
            selected_form: {
                type: 'string',
                default: ''
            },
            loaded: {
                type: 'boolean',
                default: false
            },
            forms_link: {
                type: 'string',
                default: ''
            },
            preview_html: {
                type: 'string',
                default: ''
            },
            edit_link: {
                type: 'string',
                default: ''
            }
        },

        edit: function( props ) {

            function showSpinner() {

                return (
                    element.createElement(
                        components.Spinner
                    )
                );
            }

            function loadForms() {

                ajax.post('mailerlite_gutenberg_forms', {ml_nonce: mailerlite_vars.ml_nonce}).then(response => {

                    if (response.count) {
                        props.setAttributes({
                            forms: response.forms,
                            selected_form: response.forms[0].value,
                            loaded: true,
                            forms_link: response.forms_link
                        })
                    } else {
                        props.setAttributes({
                            forms: [],
                            loaded: true,
                            forms_link: response.forms_link
                        })
                    }
                });
            }

            function init() {

                loadForms();

                return showSpinner();
            }

            function renderEditWithForms() {

                let {forms, selected_form} = props.attributes;

                return (
                    element.createElement(
                        element.Fragment,
                        null,
                        element.createElement(
                            components.SelectControl,
                            {
                                value: selected_form,
                                onChange: function(value) {

                                    props.setAttributes({
                                        selected_form: value
                                    })
                                },
                                options: forms
                            }
                        ),
                        element.createElement(
                            components.Button,
                            {
                                text: __('Select', 'mailerlite'),
                                icon: 'yes',
                                label: __('Use the selected form.', 'mailerlite'),
                                onClick: function() {

                                    props.setAttributes({
                                        preview_html: '',
                                        form_id: selected_form,
                                        editMode: false
                                    })
                                },
                            },
                        )
                    )
                );
            }

            function renderEditWithoutForms() {

                return (
                    element.createElement(
                        element.Fragment,
                        null,
                        element.createElement(
                            'div',
                            null,
                            __('Create a custom signup form or add a form created using MailerLite.', 'mailerlite')
                        ),
                        element.createElement(
                            'p',
                            null,
                            element.createElement(
                                'a',
                                {
                                    href: props.attributes.forms_link,
                                    className: "button button-hero button-primary"
                                },
                                __('Add signup form', 'mailerlite')
                            )
                        )
                    )
                );
            }

            function renderEdit() {

                const {forms, loaded} = props.attributes;

                return (
                    element.createElement(
                        components.Placeholder,
                        {
                            label: element.createElement(
                                'h3',
                                null,
                                __('MailerLite signup form', 'mailerlite')
                            )
                        },
                        ! loaded ? showSpinner() : forms.length !== 0 ? renderEditWithForms() : renderEditWithoutForms()
                    )
                );
            }

            function renderPreview() {

                const {form_id, preview_html, edit_link} = props.attributes;

                if (preview_html === '' ) {

                    ajax.post('mailerlite_gutenberg_form_preview', {form_id, ml_nonce: mailerlite_vars.ml_nonce}).then(response => {

                        props.setAttributes({
                            preview_html: response.html,
                            edit_link: response.edit_link
                        });

                        // If the form is not found
                        if (response.html === false) {

                            props.setAttributes({
                                editMode: true,
                                form_id: 0
                            });
                        }
                    });

                    return showSpinner();
                }

                return (
                    element.createElement(
                        element.Fragment,
                        null,
                        element.createElement(
                            editor.InspectorControls,
                            {
                                key: "inspector"
                            },
                            element.createElement(
                                components.PanelBody,
                                null,
                                element.createElement(
                                    'br',
                                    null
                                ),
                                element.createElement(
                                    'a',
                                    {
                                        href: edit_link,
                                        target: "_blank",
                                        className: "button button-primary"
                                    },
                                    __('Edit form', 'mailerlite')
                                ),
                                element.createElement(
                                    components.BaseControl,
                                    {
                                        help: __('Create a custom signup form or add a form created using MailerLite.', 'mailerlite')
                                    }
                                )
                            )
                        ),
                        preview_html !== false ? element.createElement(
                            element.RawHTML,
                            null,
                            preview_html
                        ) : renderInvalid()
                    )
                );
            }

            function renderInvalid() {

                loadForms();

                return (
                    element.createElement(
                        'div',
                        null,
                        element.createElement(
                            'div',
                            {
                                className: 'mailerlite-form'
                            },
                            element.createElement(
                                'p',
                                null,
                                'The form you have selected does not exist.'
                            )
                        )
                    )
                );
            }

            if( ! props.attributes.loaded) {

                return init();
            }else{

                return (
                    element.createElement(
                        element.Fragment,
                        null,
                        element.createElement(
                            editor.BlockControls,
                            null,
                            element.createElement(
                                components.Toolbar, {
                                    label: __('Edit'),
                                },
                                element.createElement(
                                    components.ToolbarButton,
                                    {
                                        icon: 'edit',
                                        title: __('Edit'),
                                        onClick: function () {
                                            props.setAttributes({
                                                editMode: !props.attributes.editMode
                                            });
                                        },
                                        isActive: props.attributes.editMode
                                    }
                                )
                            )
                        ),
                        props.attributes.editMode ? renderEdit() : renderPreview()
                    )
                );
            }
        },

        save: function( props ) {

            return element.createElement(
                element.Fragment,
                null,
                '[mailerlite_form form_id=' + props.attributes.form_id + ']',
            );
        },

    } );

})(
    wp.blocks,
    wp.components,
    wp.element,
    wp.blockEditor,
    wp.i18n,
    wp.ajax
);
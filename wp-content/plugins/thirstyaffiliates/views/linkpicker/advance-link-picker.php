<html>

<head>
<?php
    do_action('admin_print_styles');
	do_action('admin_print_scripts');
	do_action('admin_head');
?>
</head>

<body>

    <div id="advanced_add_affiliate_link">

        <div class="search-panel">
            <label>
                <span class="search-label"><?php _e( 'Search:' , 'thirstyaffiliates' ); ?></span>
                <input type="text" id="thirstylink-search" class="thirstylink-search-field form-control" placeholder="<?php esc_attr_e( 'Please enter 3 or more characters...' ) ?>" autocomplete="off">
                <span class="spinner"></span>
            </label>
            <select id="thirstylink-category" class="selectize-select" style="width:200px; display:inline-block">
                <option value=""><?php _e( 'Select category' , 'thirstyaffiliates' ); ?></option>
                <option value="all" selected><?php _e( 'All Categories' , 'thirstyaffiliates' ); ?></option>
                <?php foreach ( $this->_helper_functions->get_all_category_as_options( true ) as $term_id => $label ) : ?>
                    <option value="<?php echo esc_attr( $term_id ); ?>"><?php echo esc_html( $label ); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="results-panel">
            <ul class="results-list" data-htmleditor="<?php echo $html_editor; ?>">
                <?php echo $result_markup; ?>
            </ul>
            <a class="load-more-results" href="#">
                <span class="spinner"><i style="background-image: url(<?php echo $this->_constants->IMAGES_ROOT_URL() . 'spinner.gif'; ?>)"></i> <?php _e( 'Fetching...' , 'thirstyaffiliates' ); ?></span>
                <span class="button-text"><i class="dashicons dashicons-update"></i> <?php _e( 'Load more' , 'thirstyaffiliates' ); ?></span>
            </a>
        </div>
    </div>
    <script>

    // global var
    Options = {
        post_id        : <?php echo $post_id; ?>,
        searching_text : '<?php echo esc_js( __( 'Searching...', 'thirstyaffiliates' ) ); ?>',
        spinner_image  : '<?php echo esc_url_raw( $this->_constants->IMAGES_ROOT_URL() . 'spinner.gif' ); ?>'
    };

    jQuery( document ).ready( function($) {

        $( "body" ).on( 'DOMNodeInserted' , function() {

            $( "#advanced_add_affiliate_link .actions .button" ).tipTip({
                "attribute"       : "data-tip",
                "defaultPosition" : "bottom",
                "fadeIn"          : 50,
                "fadeOut"         : 50,
                "delay"           : 200
            });

        } ).trigger( 'DOMNodeInserted' );

        $( "#thirstylink-category" ).selectize();
        $( "#thirstylink-category" ).on( "change" , function() {
            $( "#thirstylink-search" ).trigger( "thirstysearch" );
        } );
    });
    </script>

</body>

</html>

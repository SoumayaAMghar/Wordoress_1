var thirstyReports;

jQuery( document ).ready( function($){

    // Functions
    thirstyReports = {

        /**
         * Main chart series variable
         *
         * @since 3.0.0
         */
        series : [],

        /**
         * Local browser's timezone.
         *
         * @since 3.2.2
         */
        timezone : Intl.DateTimeFormat().resolvedOptions().timeZone,

        /**
         * First chart series variable (General all links)
         *
         * @since 3.0.0
         */
        firstSeries : {
            label           : report_details.label,
            slug            : report_details.slug,
            data            : report_data.click_counts,
            yaxis           : 1,
            color           : '#3498db',
            points          : { show: true , radius: 6 , lineWidth: 4 , fillColor: '#fff' , fill: true },
            lines           : { show: true , lineWidth: 5, fill: false },
            shadowSize      : 0,
            prepend_tooltip : "&#36;"
        },

        /**
         * Initialize date picker function
         *
         * @since 3.0.0
         */
        rangeDatepicker : function() {

            var from        = $custom_date_form.find( '.range_datepicker.from' ),
                to          = $custom_date_form.find( '.range_datepicker.to' );

            from.datepicker({
                maxDate    : 0,
                dateFormat : 'yy-mm-dd'
            }).on( "change" , function() {
                to.datepicker( "option" , "minDate" , thirstyReports.getDate( this ) );
            } );

            to.datepicker({
                maxDate : 0,
                dateFormat : 'yy-mm-dd'
            }).on( "change" , function() {
                from.datepicker( "option", "maxDate", thirstyReports.getDate( this ) );
            } );
        },

        /**
         * Get the date value of the datepicker element.
         *
         * @since 3.0.0
         */
        getDate : function( element ) {

            var date;

			try {
				date = $.datepicker.parseDate( date_format, element.value );
			} catch( error ) {
				date = null;
			}

			return date;
        },

        /**
         * Initialize report graph.
         *
         * @since 3.2.2
         */
        initReport : function() {

            var $input = $chart_sidebar.find( '#add-report-data' );

            // show overlay
            $report_block.find( '.overlay' ).css( 'height' , $report_block.height() ).show();

            $.post( window.ajaxurl, {
                action     : 'ta_init_first_report',
                timezone   : thirstyReports.timezone,
                range      : $input.data( 'range' ),
                start_date : $input.data( 'start-date' ),
                end_date   : $input.data( 'end-date' )
            }, function( response ) {

                if ( response.status == 'success' ) {

                    // redraw the graph
                    thirstyReports.firstSeries.data = response.flot_data;
                    thirstyReports.series = [];
                    thirstyReports.series.push( thirstyReports.firstSeries );
                    thirstyReports.drawGraph();

                    // update total click count
                    $chart_sidebar.find( 'ul.chart-legend li:first-child em.count' ).text( response.total_clicks );

                } else {
                    // TODO: Handle error here
                }

                // hide overlay.
                $report_block.find( '.overlay' ).hide();

            } , 'json' );
        },

        /**
         * Display the report graph.
         *
         * @since 3.0.0
         */
        drawGraph : function() {

            if ( thirstyReports.series.length < 1 )
                thirstyReports.series.push( thirstyReports.firstSeries );

            main_chart = $.plot(
                $chart_placeholder,
                thirstyReports.series,
                {
                    legend : {
                        show : false,
                    },
                    grid : {
                        color       : '#aaa',
                        borderColor : 'transparent',
                        borderWidth : 0,
                        hoverable   : true,
                        markings: [ { xaxis: { from: 1.25, to: 1.25 }, color: "black" } ]
                    },
                    xaxis: {
                        show        : true,
                        color       : '#aaa',
                        position    : 'bottom',
                        tickColor   : 'transparent',
                        mode        : "time",
                        timeformat  : report_details.timeformat,
                        monthNames  : [ "Jan" , "Feb" , "Mar" , "Apr" , "May" , "Jun" , "Jul" , "Aug" , "Sep" , "Oct" , "Nov" , "Dec" ],
                        timezone    : "browser",
                        tickLength  : 1,
                        minTickSize : report_details.minTickSize,
                        font        : { color: '#000' }
                    },
                    yaxis: {
                        show         : true,
                        min          : 0,
                        minTickSize  : 1,
                        tickDecimals : 0,
                        color        : '#d4d9dc',
                        timezone     : "browser",
                        font         : { color: '#000' }
                    }
                }
            );

            $chart_placeholder.resize();
        },

        /**
         * Event function to display tooltip when datapoint is hovered.
         *
         * @since 3.0.0
         */
        plotTooltip : function() {

            var prev_data_point = null;

            $chart_placeholder.bind( 'plothover', function ( event, pos, item ) {

                if ( item ) {

                    if ( prev_data_point !== item.datapoint ) {

                        prev_data_point = item.datapoint;
                        $( '.chart-tooltip' ).remove();

                        var tooltip = report_details.clicksLabel + item.datapoint[1];

                        thirstyReports.showTooltip( item.pageX , item.pageY , tooltip );

                    }

                } else {
                    prev_data_point = null;
                    $( '.chart-tooltip' ).remove();
                }
            } );
        },

        /**
         * Append tooltip content.
         *
         * @since 3.0.0
         */
        showTooltip : function( x , y , contents ) {

            var xoffset = ( ( x + 100 ) > $( window ).width() ) ? x - 20 : x + 20,
                yoffset = ( ( x + 100 ) > $( window ).width() ) ? y - 35 : y - 16;

            $( '<div class="chart-tooltip">' + contents + '</div>' ).css( {
    			top: yoffset,
    			left: xoffset
    		}).appendTo( 'body' ).fadeIn( 200 );
        },

        /**
         * Search affiliate link to display in the report
         *
         * @since 3.0.0
         */
        searchAffiliateLink : function() {

            // ajax search affiliate links on keyup event.
            $chart_sidebar.on( 'keyup' , '#add-report-data' , function() {

                var $input = $(this);

                // clear results list
                $results_list.html('').hide();
                $input.data( 'linkid' , '' ).attr( 'data-linkid' , '' );

                if ( $input.val().length < 3 )
                    return;

                if ( last_searched === $input.val() ) {

                    $results_list.html( search_cache ).show();
                    return;
                }

                last_searched = $input.val();

                $.post( window.ajaxurl, {
                    action  : 'search_affiliate_links_query',
                    keyword : $input.val()
                }, function( response ) {

                    if ( response.status == 'success' ) {

                        search_cache = response.search_query_markup;
                        $results_list.html( response.search_query_markup ).show();

                    } else {
                        // TODO: Handle error here
                    }

                } , 'json' );

            } );

            // apply link data to search input on click of single search result
            $results_list.on( 'click' , 'li' , function(e) {

                e.preventDefault();

                var $link = $(this),
                    $input = $link.closest( '.input-wrap' ).find( 'input' );

                $input.val( $link.text() )
                      .attr( 'data-linkid' , $link.data( 'link-id' ) )
                      .data( 'linkid' , $link.data( 'link-id' ) );

                $results_list.hide();
            } );
        },

        /**
         * Fetch link report data and redraw the graph
         *
         * @since 3.0.0
         */
        fetchLinkReport : function() {

            $chart_sidebar.on( 'click' , 'button#fetch-link-report' , function() {

                var $input  = $(this).closest( '.add-legend' ).find( '#add-report-data' ),
                    series;

                if ( ! $input.data( 'linkid' ) ) {

                    // TODO: change to vex dialog
                    alert( 'Invalid affiliate link selected.' );
                    return;
                }

                // show overlay
                $report_block.find( '.overlay' ).css( 'height' , $report_block.height() ).show();

                $.post( window.ajaxurl, {
                    action     : 'ta_fetch_report_by_linkid',
                    link_id    : $input.data( 'linkid' ),
                    range      : $input.data( 'range' ),
                    start_date : $input.data( 'start-date' ),
                    end_date   : $input.data( 'end-date' ),
                    timezone   : thirstyReports.timezone
                }, function( response ) {

                    if ( response.status == 'success' ) {

                        series = {
                            label           : response.label,
                            data            : response.report_data,
                            yaxis           : 1,
                            color           : '#e74c3c',
                            points          : { show: true , radius: 6 , lineWidth: 4 , fillColor: '#fff' , fill: true },
                            lines           : { show: true , lineWidth: 5, fill: false },
                            shadowSize      : 0,
                            prepend_tooltip : "&#36;"
                        };

                        // add new legend
                        $chart_sidebar.find( 'ul li.single-link' ).remove();
                        $chart_sidebar.find( 'ul.chart-legend' )
                            .append(
                                $( '<li class="single-link" style="border-color:#e74c3c;">' )
                                    .text( response.label )
                                    .append( $( '<em class="count">' ).text( response.total_clicks ) )
                                    .append( $( '<span>' ).text( response.slug ) )
                            );

                        // redraw the graph
                        thirstyReports.series = [];
                        thirstyReports.series.push( thirstyReports.firstSeries );
                        thirstyReports.series.push( series );
                        thirstyReports.drawGraph();

                        // clear form
                        $chart_sidebar.find( 'input#add-report-data' ).val( '' ).data( 'link_id' , '' );

                        // hide overlay
                        $report_block.find( '.overlay' ).hide();

                    } else {

                        // TODO: change to vex dialog
                        alert( response.error_msg );
                    }

                } , 'json' );
            } );

            if ( $chart_sidebar.find( '#add-report-data' ).data( 'linkid' ) )
                $chart_sidebar.find( 'button#fetch-link-report' ).trigger( 'click' );
        }

    };

    var $custom_date_form  = $( 'form#custom-date-range' ),
        $report_block      = $( '.link-performance-report' ),
        $chart_placeholder = $( '.report-chart-placeholder' ),
        $chart_sidebar     = $( '.chart-sidebar' ),
        $results_list      = $( '.report-chart-wrap .add-legend .link-search-result' ),
        date_format        = 'yy-mm-dd',
        last_searched, search_cache;

    // init range date picker
    thirstyReports.rangeDatepicker();

    // init jQuery flot graph
    thirstyReports.initReport();

    // init plot tooltip events
    thirstyReports.plotTooltip();

    // init search affiliate link event
    thirstyReports.searchAffiliateLink();

    // init fetch link report event
    thirstyReports.fetchLinkReport();
} );

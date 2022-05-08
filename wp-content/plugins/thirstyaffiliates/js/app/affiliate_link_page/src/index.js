import $ from "jquery";
import affiliate_link_attachment_init from "./affiliate-link-attachment";
import affiliate_link_urls_init from "./affiliate-link-urls";
import link_category_show_in_slug from "./link-category-show-in-slug";
import link_insert_scanner from "./link-insert-scanner";

import "./assets/styles/index.scss";

$( document ).ready( function() {

    affiliate_link_attachment_init();
    affiliate_link_urls_init();
    link_category_show_in_slug();
    link_insert_scanner();
    
} );

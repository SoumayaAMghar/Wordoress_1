/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

(function($) {

    // Site title and description.
    wp.customize('blogname', function(value) {
        value.bind(function(to) {
            $('.site-title a').text(to);
        });
    });
    wp.customize('blogdescription', function(value) {
        value.bind(function(to) {
            $('.site-description').text(to);
        });
    });

    // Header text color.
    wp.customize('header_textcolor', function(value) {
        value.bind(function(to) {
            if ('blank' === to) {
                $('.site-title a, .site-description').css({
                    'clip': 'rect(1px, 1px, 1px, 1px)',
                    'position': 'absolute'
                });
            } else {
                $('.site-title a, .site-description').css({
                    'clip': 'auto',
                    'position': 'relative'
                });
                $('.site-title a, .site-description').css({
                    'color': to
                });
            }
        });
    });
})(jQuery);

/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
(function() {
    var container, button, menu, links, i, len;

    container = document.getElementById('site-navigation');
    if (!container) {
        return;
    }

    button = container.getElementsByTagName('button')[0];
    if ('undefined' === typeof button) {
        return;
    }

    menu = container.getElementsByTagName('ul')[0];

    // Hide menu toggle button if menu is empty and return early.
    if ('undefined' === typeof menu) {
        button.style.display = 'none';
        return;
    }

    menu.setAttribute('aria-expanded', 'false');
    if (-1 === menu.className.indexOf('nav-menu')) {
        menu.className += ' nav-menu';
    }

    button.onclick = function() {
        if (-1 !== container.className.indexOf('toggled')) {
            container.className = container.className.replace(' toggled', '');
            button.setAttribute('aria-expanded', 'false');
            menu.setAttribute('aria-expanded', 'false');
        } else {
            container.className += ' toggled';
            button.setAttribute('aria-expanded', 'true');
            menu.setAttribute('aria-expanded', 'true');
        }
    };

    // Get all the link elements within the menu.
    links = menu.getElementsByTagName('a');

    // Each time a menu link is focused or blurred, toggle focus.
    for (i = 0, len = links.length; i < len; i++) {
        links[i].addEventListener('focus', toggleFocus, true);
        links[i].addEventListener('blur', toggleFocus, true);
    }

    /**
     * Sets or removes .focus class on an element.
     */
    function toggleFocus() {
        var self = this;

        // Move up through the ancestors of the current link until we hit .nav-menu.
        while (-1 === self.className.indexOf('nav-menu')) {

            // On li elements toggle the class .focus.
            if ('li' === self.tagName.toLowerCase()) {
                if (-1 !== self.className.indexOf('focus')) {
                    self.className = self.className.replace(' focus', '');
                } else {
                    self.className += ' focus';
                }
            }

            self = self.parentElement;
        }
    }

    /**
     * Toggles `focus` class to allow submenu access on tablets.
     */
    (function(container) {
        var touchStartFn, i,
            parentLink = container.querySelectorAll('.menu-item-has-children > a, .page_item_has_children > a');

        if ('ontouchstart' in window) {
            touchStartFn = function(e) {
                var menuItem = this.parentNode,
                    i;

                if (!menuItem.classList.contains('focus')) {
                    e.preventDefault();
                    for (i = 0; i < menuItem.parentNode.children.length; ++i) {
                        if (menuItem === menuItem.parentNode.children[i]) {
                            continue;
                        }
                        menuItem.parentNode.children[i].classList.remove('focus');
                    }
                    menuItem.classList.add('focus');
                } else {
                    menuItem.classList.remove('focus');
                }
            };

            for (i = 0; i < parentLink.length; ++i) {
                parentLink[i].addEventListener('touchstart', touchStartFn, false);
            }
        }
    }(container));
})();
/**
 * File skip-link-focus-fix.js.
 *
 * Helps with accessibility for keyboard only users.
 *
 * Learn more: https://git.io/vWdr2
 */
(function() {
    var isIe = /(trident|msie)/i.test(navigator.userAgent);

    if (isIe && document.getElementById && window.addEventListener) {
        window.addEventListener('hashchange', function() {
            var id = location.hash.substring(1),
                element;

            if (!(/^[A-z0-9_-]+$/.test(id))) {
                return;
            }

            element = document.getElementById(id);

            if (element) {
                if (!(/^(?:a|select|input|button|textarea)$/i.test(element.tagName))) {
                    // element.tabIndex = -1;
                }

                element.focus();
            }
        }, false);
    }
})();
/**
 * Theme Custom JS File
 *
 * @package Travel_Log
 *
 * @since 1.0.0
 */

jQuery(function($) {

    function showPage() {

        var loaderState = travel_log.loader_disable

        if (true != loaderState) {

            document.getElementById("onload").style.display = "none";

        }
    }
    showPage();

    // Toggle Social Share

    $('.share-toggle').click(function(event) {

        $(this).siblings('.social-reveal').toggleClass('social-reveal-active');

        $(this).toggleClass('share-expanded');

    });

});

jQuery(function($) {

    var rtl = false;

    if (travel_log.locale_dir) {
        var rtl = true;
    }

    console.log(rtl);

    $(".travel-banner").slick({
        autoplay: true,
        autoplaySpeed: 4500,
        dots: true,
        arrows: true,
        infinite: true,
        cssEase: 'ease-out',
        useTransform: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        speed: travel_log.home_slider_speed,
        rtl: rtl,
    });

    // init Isotope
    var $grid = $('#tourlist').isotope({
        // options
        itemSelector: '.filtr-item',
        layoutMode: 'fitRows'
    });
    // filter items on button click
    $('.filter').on('click', function() {
        var filterValue = $(this).attr('data-filter');
        $grid.isotope({ filter: filterValue });
    });

    //Simple filter controls
    $('.post-filter-controls li').click(function() {
        $('.post-filter-controls li span').removeClass('active');
        $(this).children('span').addClass('active');
    });



    $(".blog-slide, .recomended-lists").slick({

        autoplay: false,
        autoplaySpeed: 4500,
        dots: false,
        arrows: true,
        infinite: true,
        cssEase: 'ease-out',
        useTransform: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        responsive: [{
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    infinite: true,
                    dots: true
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 481,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ],
        speed: travel_log.home_latest_posts_speed,
        rtl: rtl,
    });

    $(".testimonial-wrapper .testimonial").slick({
        autoplay: true,
        autoplaySpeed: 4500,
        dots: true,
        arrows: true,
        infinite: true,
        cssEase: 'linear',
        useTransform: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        speed: travel_log.home_testimonial_speed,
        fade: true,
        rtl: rtl,
    });

    //sider menu min mobile
    $('#simple-menu').sidr({
        name: 'sidr-main',
        side: travel_log.sidr_side,
        source: '.sidr.right'
    });
    $.sidr('close', 'sidr-main');
    //append a span to dropdown sidr menu and click action
    $(".sidr-class-menu-item-has-children").append("<span class='dropdown-children'></span>");
    $('.sidr-class-sub-menu').slideUp();
    $('.dropdown-children').addClass('before')
    $('.dropdown-children').click(function() {
        $(this).prev('ul').slideToggle();
        $(this).toggleClass('before after', 15000);
    });
    //collapse other dropdown menu
    $('.dropdown-children').click(function() {
        $(this).parent('li').siblings('li').find('ul').slideUp();
        $(this).parent('li').siblings('li').find('span').removeClass('after').addClass('before');
    });
    //hide sidr menu if click in body 
    $("#page").on("click", function(e) {
        $.sidr('close', 'sidr-main');
    });
    $("#sidr-main").on("click", function(e) {
        e.stopPropagation();
    });

    //end

    //mobile menu append top header when device width is less than 992px
    var topHeaderSmallScreen = function() {
        if (window.matchMedia('(max-width: 991px)').matches) {
            $('.top-header').appendTo('.sidr .sidr-inner');
            $('.top-header col-sm-6').css('width', 'auto');
            $('.sidr-inner').find('.container').addClass('supply-container').removeClass('container');
            $('.sidr-inner').find('.row').addClass('supply-row').removeClass('row');
            $('.sidr-inner').find('.col-sm-6').removeClass('col-sm-6').addClass('top-header-row');
        } else {

            $('.top-header').insertAfter('a.skip-link');
            $('.top-header col-sm-6').css('width', '');
            $('.supply-container').addClass('container').removeClass('supply-container');
            $('.supply-row').addClass('row').removeClass('supply-row');
            $('.top-header:nth-child(3)').addClass('col-sm-6').removeClass('top-header-row');

        }
    }
    topHeaderSmallScreen();
    $(window).resize(topHeaderSmallScreen);

    //hide sidr menu if click in body
    $(document).ready(function() {
        $("#page").on("click", function(e) {
            $.sidr('close', 'sidr-right');
        });
        $("#simple-menu").on("click", function(e) {
            e.stopPropagation();
        });
    });
    //end

    // ===== Scroll to Top ====
    $(window).scroll(function() {
        if ($(this).scrollTop() >= 100) { // If page is scrolled more than 50px
            $('#return-to-top').fadeIn(200); // Fade in the arrow
        } else {
            $('#return-to-top').fadeOut(200); // Else fade out the arrow
        }
    });
    $('#return-to-top').click(function(e) {

        e.preventDefault();

        // When arrow is clicked
        $('body,html').animate({
            scrollTop: 0 // Scroll to top of body
        }, 500);
    });


    // ===== Equel Height JS ====
    equalheight = function(container) {

        var currentTallest = 0,
            currentRowStart = 0,
            rowDivs = new Array(),
            $el,
            topPosition = 0;
        $(container).each(function() {

            $el = $(this);
            $($el).height('auto')
            topPostion = $el.position().top;

            if (currentRowStart != topPostion) {
                for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
                    rowDivs[currentDiv].height(currentTallest);
                }
                rowDivs.length = 0; // empty the array
                currentRowStart = topPostion;
                currentTallest = $el.height();
                rowDivs.push($el);
            } else {
                rowDivs.push($el);
                currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
            }
            for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
                rowDivs[currentDiv].height(currentTallest);
            }
        });
    }

    $(window).load(function() {
        equalheight('.blog-slide .blog-latest-post .post-content');
    });


    $(window).resize(function() {
        equalheight('.blog-slide .blog-latest-post .post-content');
    });

    //search-form

    $('a[href="#search-form"]').on('click', function(event) {

        event.preventDefault();

        $('#header-search #search-form').addClass('open');

        // $('#search-form input[type="search"]').focus();

    });

    $('#header-search #search-form span.close').click(function(event) {

        $('#header-search #search-form').removeClass('open');

    });

    var $links = $('.widget_media_gallery .gallery-icon a');

    $links.on('click', function() {

        $.fancybox.open($links, {
            // Custom options
        }, $links.index(this));

        return false;
    });

});
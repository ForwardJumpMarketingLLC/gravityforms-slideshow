;(function($) {
    'use strict';

    var $slickSlides = $('.slick-slides');
    var $sliderFor = $('.slider-for');
    var $sliderNav = $('.slider-nav');
    var $lightboxSettings = {
        caption: function (element) {
            return $(element).next(".slide-caption").text();
        },
        src: 'src',
        itemSelector: '.slick-slide img'
    };

    $slickSlides.slick();

    $slickSlides.slickLightbox($lightboxSettings);

    $sliderFor.slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        asNavFor: '.slider-nav'
    });

    $sliderNav.slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        asNavFor: '.slider-for',
        arrows: false,
        centerMode: true,
        focusOnSelect: true
    });

    $sliderFor.slickLightbox($lightboxSettings);
})(jQuery);

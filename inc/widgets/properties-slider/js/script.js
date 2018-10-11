(function($) {
    'use strict';

    if ($('.sow-properties-slider').length > 0){
        $('.sow-properties-slider').slick({
            slidesToShow: 3,
            slidesToScroll: 1,
            prevArrow: $('.horizontal-properties-slider .slick-prev'),
            nextArrow: $('.horizontal-properties-slider .slick-next'),
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 900,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 700,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: false,
                    }
                }
                ]
        });
    }

})(jQuery)

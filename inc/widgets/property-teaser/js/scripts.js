(function($) {
    'use strict';

    $(document).ready(function(){
        $('.js-teaser-more').on('click', function(){
            $('.js-teaser-less').removeClass('hide');
            $('.teaser-more-info').removeClass('hide');
            $(this).addClass('hide');
            return false;
        });

        $('.js-teaser-less').on('click', function(){
            $('.js-teaser-less').addClass('hide');
            $('.teaser-more-info').addClass('hide');
            $('.js-teaser-more').removeClass('hide');
            return false;
        });

        $('.teaser-more-info a').on('click', function(){
            $(this).parents('.sow-property-teaser').find('.es-property-thumbnail img').attr('src', $(this).attr('href'));
            $(this).parents('.sow-property-teaser').find('.es-property-thumbnail img').attr('srcset', '');
            console.log(  $(this).parents('.sow-property-teaser').find('.es-property-thumbnail img'))
            return false;

        });

    });
})(jQuery);

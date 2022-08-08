define([
    'jquery',
    'underscore',
    'stickUp',
    'tiltJs'
], function ($, _) {
    'use strict';

    $.widget('Zemez.themeOptions', {

        options: {
            isStickyMenu: false,
            isToTopButton: false,
            isTiltJs: false,
            stickUpSelector: '.sm-header-nav-wrap',
            stickParams: { },
            tiltParams: { }
        },

        _create: function() {
            this._stickUp();
            this._toTop();
            this._tiltJs();
        },

        _stickUp: function() {
            var stickUpSelector = $(this.options.stickUpSelector)
            if(this.options.isStickyMenu && stickUpSelector.length){
                stickUpSelector.stickUp(this.options.stickParams);
            }
        },

        _toTop: function(){
            if (this.options.isToTopButton) {
                $(window).scroll(function(){
                    if ($(this).scrollTop() > 400) {
                        $('.scrollToTop').stop(true).fadeIn();
                    } else {
                        $('.scrollToTop').stop(true).fadeOut();
                    }
                });

                $('.scrollToTop').click(function(){
                    $('html, body').stop(true).animate({scrollTop : 0},800);
                    return false;
                });
            }
        },

        _tiltJs: function() {
            if (this.options.isTiltJs) {
                $('.product-item-info').tilt(this.options.tiltParams);
            }
        }



    });

    return $.Zemez.themeOptions;
});
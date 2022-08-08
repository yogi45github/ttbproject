/**
 * Copyright © 2018. All rights reserved.
 */

;(function($, window, document, undefined) {
    $.fn.mobileMenuClick = function(action) {
        if (!('ontouchstart' in window) &&
            !navigator.msMaxTouchPoints &&
            !navigator.userAgent.toLowerCase().match(/windows phone os 7/i)) return false;
        if (action === 'unbind') {
            this.each(function() {
                $(this).off();
                $(document).off('click touchstart MSPointerDown', handleTouch);
            });
        } else {
            this.each(function() {
                $(this).on('click', function(e) {
                    if ($(this).children('.submenu').attr('aria-expanded') === 'false') {
                        e.preventDefault();
                    }
                });
            });
        }
        return this;
    };
})(jQuery, window, document);
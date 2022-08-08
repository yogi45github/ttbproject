define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    $.widget('Zemez.productListingGallery', {

        options: {},

        _create: function() {
            var main_href = $(this.element).closest('a').attr('href');
            $(this.element).closest('a')
                .append("<div class='product-item-photo product-item-photo-fix' href='"+ main_href +"'></div>")
                .find('.product-item-photo-fix')
                .css({'display':'none'});
            $(this.element).closest('a').contents().unwrap();
            $(this.element).on('fotorama:load', function (e, fotorama) {
                $('.fotorama__wrap--toggle-arrows', this.element).addClass('fotorama__wrap--no-controls');
            });
        },

    });

    return $.Zemez.productListingGallery;
});
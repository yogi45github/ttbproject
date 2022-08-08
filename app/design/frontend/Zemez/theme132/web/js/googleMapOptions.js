define([
    'jquery',
    'jquery/ui',
    'googleMapPagePlugin'
], function($, mageTemplate ,pluginTemplate) {
    'use strict';

    $.widget('Zemez.googleMapOptions', $.Zemez.googleMapPagePlugin, {

        options: {
            contactSelector: '.contact-us_googlemap__wrapper'
        },

        _create: function() {
            this._super();
        }

    });

    return $.Zemez.googleMapOptions;
});
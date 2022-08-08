define([
    "jquery",
    "menu"
], function($) {
    "use strict";
    $.widget('Zemez.megamenu', $.mage.menu, {
        options: {
            responsive: true,
            delay: 100,
            mediaBreakpoint: '(max-width: 767px)'
        },
        isMobile: false,

        _create: function() {
            this.element.find('.price-box').removeClass('price-box');
            this._super();
            this.element.find('.megamenu-wrapper').parent().addClass('parent megamenu-wrapper-parent');
        },

        _toggleMobileMode: function() {
            this.isMobile = true;
            $(this.element).off('mouseenter mouseleave');
            this._on({
                'click .ui-menu-item:has(a)': function(event) {
                    if ($(event.target).hasClass('ui-menu-icon')) {
                        event.preventDefault();
                    }
                }
            });
        },

        _toggleDesktopMode: function() {
            this.isMobile = false;
            this._super();
            this.element.find( this.options.menus ).removeAttr('style');
        },

        expand: function(event) {
            var target = $(event.target),
                subMenu = target.closest('.parent').children('.ui-menu');
            if (subMenu.is(':visible') || !target.hasClass('ui-menu-icon')) {
                return;
            }
            this.active.siblings().children('.ui-state-active').removeClass('ui-state-active');
            this._open(subMenu);
            // Delay so Firefox will not hide activedescendant change in expanding submenu from AT
            this._delay(function() {
                this.focus(event, subMenu.children('.ui-menu-item').first());
            });
        },

        select: function(event) {
            var ui;
            this.active = this.active || $(event.target).closest('.ui-menu-item');
            ui = {
                item: this.active
            };
            this._close( $(event.target).closest('.ui-menu-item') );
            this._trigger('select', event, ui);
        },

        _open: function(submenu) {
            clearTimeout(this.timer);
            var uiMenu = this.element.find(".ui-menu").not(submenu.parents(".ui-menu"));
            if (this.isMobile) {
                uiMenu.slideUp(200);
            }
            uiMenu.attr("aria-hidden", "true").prev().removeClass("ui-state-active");

            if (this.isMobile) {
                var element = this.element;
                submenu.slideDown(200, function() {
                    if((submenu.parent().offset().top - element.parent().offset().top) < 0) {
                        element.parent().animate({
                            scrollTop: submenu.parent().offset().top - element.offset().top
                        }, 200);
                    }
                });
            }
            submenu
                .removeAttr("aria-hidden")
                .attr("aria-expanded", "true");
        },

        _close: function(startMenu) {
            if (!startMenu) {
                startMenu = this.active ? this.active.parent() : this.element;
            }
            var uiMenu = startMenu.find(".ui-menu");
            if (this.isMobile) {
                uiMenu.slideUp(200);
            }
            uiMenu
                .attr("aria-hidden", "true")
                .attr("aria-expanded", "false")
                .end()
                .find("a.ui-state-active")
                .removeClass("ui-state-active");
        }
    });
    return $.Zemez.megamenu;
});

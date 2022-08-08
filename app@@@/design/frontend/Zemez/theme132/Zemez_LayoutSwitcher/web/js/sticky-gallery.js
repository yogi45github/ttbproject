/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'fotorama/fotorama',
    'underscore',
    'matchMedia',
    'mage/template',
    'text!mage/gallery/gallery.html',
    'uiClass',
    'mage/translate'
], function($, fotorama, _, mediaCheck, template, galleryTpl, Class, $t) {
    'use strict';
    /**
     * Retrieves index if the main item.
     * @param {Array.<Object>} data - Set of gallery items.
     */
    var getMainImageIndex = function(data) {
            var mainIndex;
            if (_.every(data, function(item) {
                return _.isObject(item);
            })
            ) {
                mainIndex = _.findIndex(data, function(item) {
                    return item.isMain;
                });
            }
            return mainIndex > 0 ? mainIndex : 0;
        },
        /**
         * @param {*} str
         * @return {*}
         * @private
         */
        _toNumber = function(str) {
            var type = typeof str;
            if (type === 'string') {
                return parseInt(str); //eslint-disable-line radix
            }
            return str;
        };
    return Class.extend({
        defaults: {
            settings: {},
            config: {},
            startConfig: {}
        },
        /**
         * Initializes gallery.
         * @param {Object} config - Gallery configuration.
         * @param {String} element - String selector of gallery DOM element.
         */
        initialize: function(config, element) {
            this._super();
            config.options.width = _toNumber(config.options.width);
            config.options.height = _toNumber(config.options.height);
            config.options.swipe = true;
            this.config = config;
            this.settings = {
                $element: $(element),
                $pageWrapper: $('body>.page-wrapper'),
                currentConfig: config,
                defaultConfig: _.clone(config),
                activeBreakpoint: {},
                fotoramaApi: null,
                api: null,
                data: _.clone(config.data),
                isMobile: false,
                $desktopGallery: $(config.desktopGallery),
                currentData: _.clone(config.data),
                leftColumn: document.getElementById(config.leftColumn),
                rightColumn: document.getElementById(config.rightColumn),
                rightColumnWrap: document.getElementById(config.rightColumnWrap),
                stylesOptions: {}
            };
            config.options.ratio = config.options.width / config.options.height;
            config.options.height = null;
            $.extend(true, this.startConfig, config);
            this.initGallery();
            this.initApi();
            this.setupBreakpoints();
        },
        /**
         * Initializes gallery with configuration options.
         */
        initGallery: function() {
            var settings = this.settings,
                config = this.config,
                tpl = template(galleryTpl, {
                    next: $t('Next'),
                    previous: $t('Previous')
                }),
                mainImageIndex;
            _.extend(config, config.options);
            config.options = undefined;
            config.click = false;
            settings.currentConfig = config;
            settings.$element.html(tpl);
            settings.$elementF = $(settings.$element.children()[0]);
            settings.$elementF.fotorama(config);
            settings.fotoramaApi = settings.$elementF.data('fotorama');
            $.extend(true, config, this.startConfig);
            mainImageIndex = getMainImageIndex(config.data);
            if (mainImageIndex) {
                this.settings.fotoramaApi.show({
                    index: mainImageIndex,
                    time: 0
                });
            }
        },
        /**
         * Creates breakpoints for gallery.
         */
        setupBreakpoints: function() {
            var settings = this.settings,
                config = this.config,
                self = this;
            mediaCheck({
                media: '(max-width: 767px)',
                /**
                 * Is triggered when breakpoint enties.
                 */
                entry: function() {
                    settings.isMobile = true;
                    settings.rightColumn.removeAttribute("style");
                    settings.$desktopGallery.empty();
                },
                /**
                 * Is triggered when breakpoint exits.
                 */
                exit: function() {
                    if (settings.isMobile) {
                        self.updateDesktopData(settings.currentData);
                        settings.isMobile = false;
                    }
                    self.updateRightBlock();
                    self.rBlockPosition();
                    window.onscroll = function(e) {
                        self.rBlockPosition();
                    };
                    $(window).on('resize', function() {
                        self.updateRightBlock();
                        self.rBlockPosition();
                    });
                }
            });
        },
        /**
         * Creates gallery's API.
         */
        initApi: function() {
            var settings = this.settings,
                config = this.config,
                self= this,
                api = {
                    /**
                     * Contains fotorama's API methods.
                     */
                    fotorama: settings.fotoramaApi,
                    /**
                     * Displays the last image on preview.
                     */
                    last: function() {
                        settings.fotoramaApi.show('>>');
                    },
                    /**
                     * Displays the first image on preview.
                     */
                    first: function() {
                        settings.fotoramaApi.show('<<');
                    },
                    /**
                     * Displays previous element on preview.
                     */
                    prev: function() {
                        settings.fotoramaApi.show('<');
                    },
                    /**
                     * Displays next element on preview.
                     */
                    next: function() {
                        settings.fotoramaApi.show('>');
                    },
                    /**
                     * Displays image with appropriate count number on preview.
                     * @param {Number} index - Number of image that should be displayed.
                     */
                    seek: function(index) {
                        if (_.isNumber(index) && index !== 0) {
                            if (index > 0) {
                                index -= 1;
                            }
                            settings.fotoramaApi.show(index);
                        }
                    },
                    /**
                     * Destroy fotorama gallery.
                     */
                    destroy: function() {
                        settings.fotoramaApi.destroy();
                    },
                    /**
                     * Updates gallery with new set of options.
                     * @param {Object} configuration - Standart gallery configuration object.
                     * @param {Boolean} isInternal - Is this function called via breakpoints.
                     */
                    updateOptions: function(configuration, isInternal) {
                        var $selectable = $('a[href], area[href], input, select, ' +
                            'textarea, button, iframe, object, embed, *[tabindex], *[contenteditable]')
                                .not('[tabindex=-1], [disabled], :hidden'),
                            $focus = $(':focus'),
                            index;
                        if (_.isObject(configuration)) {

                            //Saves index of focus
                            $selectable.each(function(number) {
                                if ($(this).is($focus)) {
                                    index = number;
                                }
                            });
                            configuration.click = false;
                            $.extend(true, settings.currentConfig.options, configuration);
                            settings.fotoramaApi.setOptions(settings.currentConfig.options);
                            if (_.isNumber(index)) {
                                $selectable.eq(index).focus();
                            }
                        }
                    },
                    /**
                     * Updates gallery with specific set of items.
                     * @param {Array.<Object>} data - Set of gallery items to update.
                     */
                    updateData: function(data) {
                        if (_.isArray(data)) {
                            if (!settings.isMobile) {
                                self.updateDesktopData(data);
                            }
                            settings.currentData = data;
                            settings.fotoramaApi.load(data);
                            $.extend(false, settings, {
                                data: data,
                                defaultConfig: data
                            });
                            $.extend(false, config, {
                                data: data
                            });
                        }
                    },
                    /**
                     * Returns current images list
                     *
                     * @returns {Array}
                     */
                    returnCurrentImages: function() {
                        var images = [];
                        _.each(this.fotorama.data, function(item) {
                            images.push(_.omit(item, '$navThumbFrame', '$navDotFrame', '$stageFrame', 'labelledby'));
                        });
                        return images;
                    },
                    /**
                     * Updates gallery data partially by index
                     * @param {Number} index - Index of image in data array to be updated.
                     * @param {Object} item - Standart gallery image object.
                     *
                     */
                    updateDataByIndex: function(index, item) {
                        settings.fotoramaApi.spliceByIndex(index, item);
                    }
                };
            settings.$element.data('gallery', api);
            settings.api = settings.$element.data('gallery');
            settings.$element.trigger('gallery:loaded');
        },
        updateDesktopData: function(data) {
            var newData = "",
                config = this.config;
            data.forEach(function(item) {
                if (item.videoUrl) {
                    newData += "<div class='thumb video' style='padding-bottom: " + (100 / config.options.ratio) + "%'><iframe width='100%' height='100%' src='" + item.videoUrl + "' title='' frameborder='0' allowfullscreen></iframe></div>";
                } else {
                    newData += "<div class='thumb'><img src='" + item.full + "' alt=''></div>";
                }
            });
            this.settings.$desktopGallery.html(newData);
            this.reloadRBlock();
        },
        getYC: function(obj) {
            if (!obj) return 0;
            var docElem, win,
                rect,
                doc = obj.ownerDocument;
            if (!doc) return 0;
            docElem = doc.documentElement;
            rect = obj.getBoundingClientRect();
            win = doc === doc.window ? doc : (doc.nodeType === 9 ? doc.defaultView || doc.parentWindow : false);
            return rect.top + (win.pageYOffset || docElem.scrollTop) - (docElem.clientTop || 0);
        },
        compareStyles: function(st, newSt) {
            var obj1 = {}, obj2 = {};
            for (var key in st) {
                obj1[key] = Math.round(st[key]);
            }
            for (var key in newSt) {
                obj2[key] = Math.round(newSt[key]);
            }
            return JSON.stringify(obj1) === JSON.stringify(obj2);
        },
        rBlockPosition: function() {
            var rCol = this.settings.rightColumn,
                lCol = this.settings.leftColumn;
            if (this.settings.isMobile) return;
            var wh = document.documentElement.clientHeight || 0,
                st = Math.min(window.pageYOffset || document.documentElement.scrollTop, Math.max(
                    document.body.scrollHeight, document.documentElement.scrollHeight,
                    document.body.offsetHeight, document.documentElement.offsetHeight,
                    document.body.clientHeight, document.documentElement.clientHeight
                ) - wh),
                headH = 80, rColH = rCol.offsetHeight, pageH = lCol.offsetHeight, pagePos = this.getYC(lCol), rColMB = 15,
                rColBottom = st + wh - pageH - pagePos - rColMB, rColPT = pagePos - headH, rColPos = this.getYC(rCol),
                lastSt = this.settings.stylesOptions.lastSt || 0, lastStyles = this.settings.stylesOptions.lastStyles || {}, styles, needFix = false,
                smallEnough = headH + rColMB + rColH + Math.max(0, rColBottom) <= wh;
            if (st - 1 < rColPT && !(smallEnough && rColPos < headH) || (rColH >= pageH)) {
                styles = {marginTop: 0}
            } else if (st - 1 < Math.min(lastSt, rColPos - headH) || smallEnough) {
                styles = {top: headH};
                needFix = true;
            } else if (st + 1 > Math.max(lastSt, rColPos + rColH + rColMB - wh) && rColBottom < 0) {
                styles = {bottom: rColMB};
                needFix = true;
            } else {
                styles = {marginTop: (rColBottom >= 0) ? pageH - rColH : Math.min(rColPos - pagePos, pageH - rColH + rColPT)}
            }
            if (!this.compareStyles(styles, lastStyles)) {
                for (var key in lastStyles) {
                    lastStyles[key] = null;
                }
                for (var key in styles) {
                    lastStyles[key] = styles[key];
                }
                for (var key in lastStyles) {
                    if (lastStyles[key] !== null) {
                        rCol.style[key] = lastStyles[key] + 'px';
                    } else {
                        rCol.style[key] = null;
                    }
                }
                this.settings.stylesOptions.lastStyles = styles;
            }
            if (needFix !== rCol.classList.contains("fixed")) {
                if (needFix) {
                    rCol.classList.add("fixed");
                } else {
                    rCol.classList.remove("fixed");
                }
            }
            this.settings.stylesOptions.lastSt = st;
        },
        updateRightBlock: function() {
            if (this.settings.isMobile) return;
            this.settings.rightColumn.style.width = this.settings.rightColumnWrap.clientWidth + "px";
            this.settings.rightColumn.style.left = this.settings.rightColumnWrap.getBoundingClientRect().left + "px";
        },
        reloadRBlock: function() {
            this.settings.rightColumn.style.marginTop = 0;
            $(window).scrollTop(0);
            this.rBlockPosition();
        }
    });
});
if(typeof(VISIONCallbacks) == "undefined"){

  var VISIONCallbacks = function () {
      this.list = [];
      this.fireStack = [];
      this.isFiring = false;
      this.isDisabled = false;
  
      /**
       * Calls all added callbacks
       *
       * @private
       * @param args
       */
      this.fire = function (args) {
          var context = args[0],
              deferred = args[1],
              callbackArguments = args[2];
          this.isFiring = true;
  
          for (var i = 0, l = this.list.length; i < l; i++) {
              if (false === this.list[i].fn.apply(context, callbackArguments)) {
                  deferred.reject();
  
                  break;
              }
          }
  
          this.isFiring = false;
  
          deferred.resolve();
  
          if (this.fireStack.length) {
              this.fire(this.fireStack.shift());
          }
      };
  
      /**
       * Returns index of the callback in the list in a similar way as
       * the indexOf function.
       *
       * @param callback
       * @param {number} index index to start the search from
       * @returns {number}
       */
      this.inList = function (callback, index) {
          index = index || 0;
  
          for (var i = index, length = this.list.length; i < length; i++) {
              if (this.list[i].fn === callback || (callback.guid && this.list[i].fn.guid && callback.guid === this.list[i].fn.guid)) {
                  return i;
              }
          }
  
          return -1;
      };
  
      return this;
  };
  
  VISIONCallbacks.prototype = {
      /**
       * Adds a callback
       *
       * @param callback
       * @returns {VISIONCallbacks}
       * @param priority
       */
      add: function (callback, priority) {
          var callbackObject = {fn: callback, priority: priority};
  
          priority = priority || 0;
  
          for (var i = 0, length = this.list.length; i < length; i++) {
              if (priority > this.list[i].priority) {
                  this.list.splice(i, 0, callbackObject);
  
                  return this;
              }
          }
  
          this.list.push(callbackObject);
  
          return this;
      },
  
      /**
       * Removes a callback
       *
       * @param callback
       * @returns {VISIONCallbacks}
       */
      remove: function (callback) {
          var index = 0;
  
          while (( index = this.inList(callback, index) ) > -1) {
              this.list.splice(index, 1);
          }
  
          return this;
      },
  
      /**
       * Checks if callback is added
       *
       * @param callback
       * @returns {*}
       */
      has: function (callback) {
          return (this.inList(callback) > -1);
      },
  
  
      /**
       * Calls callbacks with a context
       *
       * @param context
       * @param args
       * @returns {object|void}
       */
      fireWith: function (context, args) {
          var deferred = jQuery.Deferred();
  
          if (this.isDisabled) {
              return deferred.reject();
          }
  
          args = args || [];
          args = [ context, deferred, args.slice ? args.slice() : args ];
  
          if (this.isFiring) {
              this.fireStack.push(args);
          } else {
              this.fire(args);
          }
  
          return deferred;
      },
  
      /**
       * Disable firing of new events
       */
      disable: function () {
          this.isDisabled = true;
      },
  
      /**
       * Enable firing of new events
       */
      enable: function () {
          this.isDisabled = false;
      }
  };
  }
  
  define([
      'jquery'
  ], function () {
  (function($) {
  
    'use strict';
  
    var UNDETERMINED_SCROLLOFFSET = -1;
  
    var VISION = function($element, options) {
      this.itemsContainerSelector = options.container;
      this.itemSelector = options.item;
      this.nextSelector = options.next;
      this.paginationSelector = options.pagination;
      this.$scrollContainer = $element;
      this.$container = (window === $element.get(0) ? $(document) : $element);
      this.defaultDelay = options.delay;
      this.negativeMargin = options.negativeMargin;
      this.nextUrl = null;
      this.isBound = false;
      this.isPaused = false;
      this.isInitialized = false;
      this.jsXhr = false;
      this.listeners = {
        next:     new VISIONCallbacks($),
        load:     new VISIONCallbacks($),
        loaded:   new VISIONCallbacks($),
        render:   new VISIONCallbacks($),
        rendered: new VISIONCallbacks($),
        scroll:   new VISIONCallbacks($),
        noneLeft: new VISIONCallbacks($),
        ready:    new VISIONCallbacks($)
      };
      this.extensions = [];
  
      /**
       * Scroll event handler
       *
       * Note: calls to this functions should be throttled
       *
       * @private
       */
      this.scrollHandler = function() {
        // the throttle method can call the scrollHandler even thought we have called unbind()
        if (!this.isBound || this.isPaused) {
          return;
        }
  
        var currentScrollOffset = this.getCurrentScrollOffset(this.$scrollContainer),
            scrollThreshold = this.getScrollThreshold()
        ;
  
        // invalid scrollThreshold. The DOM might not have loaded yet...
        if (UNDETERMINED_SCROLLOFFSET == scrollThreshold) {
          return;
        }
  
        this.fire('scroll', [currentScrollOffset, scrollThreshold]);
  
        if (currentScrollOffset >= scrollThreshold && ($('#load_more_prd').attr('data-load') == 'true')) {
          this.next();
          $('#load_more_prd').attr('data-load',false);
          $('#load_more_prd').removeClass('load-data');
        }
      };
  
      /**
       * Returns the items container currently in the DOM
       *
       * @private
       * @returns {object}
       */
      this.getItemsContainer = function() {
        return $(this.itemsContainerSelector, this.$container);
      };
  
      /**
       * Returns the last item currently in the DOM
       *
       * @private
       * @returns {object}
       */
      this.getLastItem = function() {
        return $(this.itemSelector, this.getItemsContainer().get(0)).last();
      };
  
      /**
       * Returns the first item currently in the DOM
       *
       * @private
       * @returns {object}
       */
      this.getFirstItem = function() {
        return $(this.itemSelector, this.getItemsContainer().get(0)).first();
      };
  
      /**
       * Returns scroll threshold. This threshold marks the line from where
       * VISION should start loading the next page.
       *
       * @private
       * @param negativeMargin defaults to {this.negativeMargin}
       * @return {number}
       */
      this.getScrollThreshold = function(negativeMargin) {
        var $lastElement;
  
        negativeMargin = negativeMargin || this.negativeMargin;
        negativeMargin = (negativeMargin >= 0 ? negativeMargin * -1 : negativeMargin);
  
        $lastElement = this.getLastItem();
  
        // if the don't have a last element, the DOM might not have been loaded,
        // or the selector is invalid
        if (0 === $lastElement.length) {
          return UNDETERMINED_SCROLLOFFSET;
        }
  
        return ($lastElement.offset().top + $lastElement.height() + negativeMargin);
      };
  
      /**
       * Returns current scroll offset for the given scroll container
       *
       * @private
       * @param $container
       * @returns {number}
       */
      this.getCurrentScrollOffset = function($container) {
        var scrollTop = 0,
            containerHeight = $container.height();
  
        if (window === $container.get(0))  {
          scrollTop = $container.scrollTop();
        } else {
          scrollTop = $container.offset().top;
        }
  
        // compensate for iPhone
        if (navigator.platform.indexOf("iPhone") != -1 || navigator.platform.indexOf("iPod") != -1) {
          containerHeight += 500;
        }
  
        return (scrollTop + containerHeight);
      };
  
      /**
       * Returns the url for the next page
       *
       * @private
       */
      this.getNextUrl = function(container) {
        container = container || this.$container;
  
        // always take the last matching item
        return $(this.nextSelector, container).last().attr('href');
      };
  
      /**
       * Loads a page url
       *
       * @param url
       * @param callback
       * @param delay
       * @returns {object}        jsXhr object
       */
      this.load = function(url, callback, delay) {
        var self = this,
            $itemContainer,
            items = [],
            timeStart = +new Date(),
            timeDiff;
  
        delay = delay || this.defaultDelay;
  
        var loadEvent = {
          url: url,
          ajaxOptions: {
            dataType: 'html'
          }
        };
  
        self.fire('load', [loadEvent]);
  
        function xhrDoneCallback(data) {
          $itemContainer = $(this.itemsContainerSelector, data).eq(0);
          if (0 === $itemContainer.length) {
            $itemContainer = $(data).filter(this.itemsContainerSelector).eq(0);
          }
  
          if ($itemContainer) {
            $itemContainer.find(this.itemSelector).each(function() {
              items.push(this);
            });
          }
  
          self.fire('loaded', [data, items]);
  
          if (callback) {
            timeDiff = +new Date() - timeStart;
            if (timeDiff < delay) {
              setTimeout(function() {
                callback.call(self, data, items);
              }, delay - timeDiff);
            } else {
              callback.call(self, data, items);
            }
          }
        }
  
        this.jsXhr = $.ajax(loadEvent.url, loadEvent.ajaxOptions)
          .done($.proxy(xhrDoneCallback, self));
  
        return this.jsXhr;
      };
  
      /**
       * Renders items
       *
       * @param callback
       * @param items
       */
      this.render = function(items, callback) {
        var self = this,
            $lastItem = this.getLastItem(),
            count = 0;
  
        var promise = this.fire('render', [items]);
  
        promise.done(function() {
          $(items).hide(); // at first, hide it so we can fade it in later
  
          $lastItem.after(items);
  
          $(items).fadeIn(400, function() {
            // complete callback get fired for each item,
            // only act on the last item
            if (++count < items.length) {
              return;
            }
  
            self.fire('rendered', [items]);
  
            if (callback) {
              callback();
            }
          });
        });
  
        promise.fail(function() {
          if (callback) {
            callback();
          }
        });
      };
  
      /**
       * Hides the pagination
       */
      this.hidePagination = function() {
        if (this.paginationSelector) {
          $(this.paginationSelector, this.$container).hide();
        }
      };
  
      /**
       * Restores the pagination
       */
      this.restorePagination = function() {
        if (this.paginationSelector) {
          $(this.paginationSelector, this.$container).show();
        }
      };
  
      /**
       * Throttles a method
       *
       * Adopted from Ben Alman's jQuery throttle / debounce plugin
       *
       * @param callback
       * @param delay
       * @return {object}
       */
      this.throttle = function(callback, delay) {
        var lastExecutionTime = 0,
            wrapper,
            timerId
        ;
  
        wrapper = function() {
          var that = this,
              args = arguments,
              diff = +new Date() - lastExecutionTime;
  
          function execute() {
            lastExecutionTime = +new Date();
            callback.apply(that, args);
          }
  
          if (!timerId) {
            execute();
          } else {
            clearTimeout(timerId);
          }
  
          if (diff > delay) {
            execute();
          } else {
            timerId = setTimeout(execute, delay);
          }
        };
  
        if ($.guid) {
          wrapper.guid = callback.guid = callback.guid || $.guid++;
        }
  
        return wrapper;
      };
  
      /**
       * Fires an event with the ability to cancel further processing. This
       * can be achieved by returning false in a listener.
       *
       * @param event
       * @param args
       * @returns {*}
       */
      this.fire = function(event, args) {
        return this.listeners[event].fireWith(this, args);
      };
  
      /**
       * Pauses the scroll handler
       *
       * Note: internal use only, if you need to pause VISION use `unbind` method.
       *
       * @private
       */
      this.pause = function() {
        this.isPaused = true;
      };
  
      /**
       * Resumes the scroll handler
       *
       * Note: internal use only, if you need to resume VISION use `bind` method.
       *
       * @private
       */
      this.resume = function() {
        this.isPaused = false;
      };
  
      return this;
    };
  
    /**
     * Initialize VISION
     *
     * Note: Should be called when the document is ready
     *
     * @public
     */
    VISION.prototype.initialize = function() {
      if (this.isInitialized) {
        return false;
      }
  
      var supportsOnScroll = (!!('onscroll' in this.$scrollContainer.get(0))),
          currentScrollOffset = this.getCurrentScrollOffset(this.$scrollContainer),
          scrollThreshold = this.getScrollThreshold();
  
      // bail out when the browser doesn't support the scroll event
      if (!supportsOnScroll) {
        return false;
      }
  
      this.hidePagination();
      this.bind();
  
      this.nextUrl = this.getNextUrl();
  
      if (!this.nextUrl) {
        this.fire('noneLeft', [this.getLastItem()]);
      }
  
      // start loading next page if content is shorter than page fold
      if (this.nextUrl && currentScrollOffset >= scrollThreshold) {
        this.next();
  
        // flag as initialized when rendering is completed
        this.one('rendered', function() {
          this.isInitialized = true;
  
          this.fire('ready');
        });
      } else {
        this.isInitialized = true;
  
        this.fire('ready');
      }
  
      return this;
    };
  
    /**
     * Reinitializes VISION, for example after an ajax page update
     *
     * @public
     */
    VISION.prototype.reinitialize = function () {
      this.isInitialized = false;
  
      this.unbind();
      this.initialize();
    };
  
    /**
     * Binds VISION to DOM events
     *
     * @public
     */
    VISION.prototype.bind = function() {
      if (this.isBound) {
        return;
      }
  
      this.$scrollContainer.on('scroll', $.proxy(this.throttle(this.scrollHandler, 150), this));
  
      for (var i = 0, l = this.extensions.length; i < l; i++) {
        this.extensions[i].bind(this);
      }
  
      this.isBound = true;
      this.resume();
    };
  
    /**
     * Unbinds VISION to events
     *
     * @public
     */
    VISION.prototype.unbind = function() {
      if (!this.isBound) {
        return;
      }
  
      this.$scrollContainer.off('scroll', this.scrollHandler);
  
      // notify extensions about unbinding
      for (var i = 0, l = this.extensions.length; i < l; i++) {
        if (typeof this.extensions[i]['unbind'] != 'undefined') {
          this.extensions[i].unbind(this);
        }
      }
  
      this.isBound = false;
    };
  
    /**
     * Destroys VISION instance
     *
     * @public
     */
    VISION.prototype.destroy = function() {
      try {
        this.jsXhr.abort();
      } catch (e) {}
  
      this.unbind();
  
      this.$scrollContainer.data('vision', null);
    };
  
    /**
     * Registers an eventListener
     *
     * Note: chainable
     *
     * @public
     * @returns VISION
     */
    VISION.prototype.on = function(event, callback, priority) {
      if (typeof this.listeners[event] == 'undefined') {
        throw new Error('There is no event called "' + event + '"');
      }
  
      priority = priority || 0;
  
      this.listeners[event].add($.proxy(callback, this), priority);
  
      // ready is already fired, before on() could even be called, so
      // let's call the callback right away
      if (this.isInitialized) {
        if (event === 'ready') {
          $.proxy(callback, this)();
        }
        // same applies to noneLeft
        else if (event === 'noneLeft' && !this.nextUrl) {
          $.proxy(callback, this)();
        }
      }
  
      return this;
    };
  
    /**
     * Registers an eventListener which only gets
     * fired once.
     *
     * Note: chainable
     *
     * @public
     * @returns VISION
     */
    VISION.prototype.one = function(event, callback) {
      var self = this;
  
      var remover = function() {
        self.off(event, callback);
        self.off(event, remover);
      };
  
      this.on(event, callback);
      this.on(event, remover);
  
      return this;
    };
  
    /**
     * Removes an eventListener
     *
     * Note: chainable
     *
     * @public
     * @returns VISION
     */
    VISION.prototype.off = function(event, callback) {
      if (typeof this.listeners[event] == 'undefined') {
        throw new Error('There is no event called "' + event + '"');
      }
  
      this.listeners[event].remove(callback);
  
      return this;
    };
  
    /**
     * Load the next page
     *
     * @public
     */
    VISION.prototype.next = function() {
      var url = this.nextUrl,
          self = this;
  
      if (!url) {
        return false;
      }
  
      if(url.indexOf("ajax=1") != -1){
          url = url.replace("ajax=1",'');
      }
  
      this.pause();
  
      var promise = this.fire('next', [url]);
  
      promise.done(function() {
        self.load(url, function(data, items) {
          self.render(items, function() {
            self.nextUrl = self.getNextUrl(data);
  
            if (!self.nextUrl) {
              self.fire('noneLeft', [self.getLastItem()]);
            }
  
            self.resume();
          });
        });
      });
  
      promise.fail(function() {
        self.resume();
      });
  
      return true;
    };
  
    /**
     * Adds an extension
     *
     * @public
     */
    VISION.prototype.extension = function(extension) {
      if (typeof extension['bind'] == 'undefined') {
        throw new Error('Extension doesn\'t have required method "bind"');
      }
  
      if (typeof extension['initialize'] != 'undefined') {
        extension.initialize(this);
      }
  
      this.extensions.push(extension);
  
      if (this.isBound) {
        this.reinitialize();
      }
  
      return this;
    };
  
    /**
     * Shortcut. Sets the window as scroll container.
     *
     * @public
     * @param option
     * @returns {*}
     */
    $.vision = function(option) {
      var $window = $(window);
  
      return $window.vision.apply($window, arguments);
    };
  
    /**
     * jQuery plugin initialization
     *
     * @public
     * @param option
     * @returns {*} the last VISION instance will be returned
     */
    $.fn.vision = function(option) {
      var args = Array.prototype.slice.call(arguments);
      var retval = this;
  
      this.each(function() {
        var $this = $(this),
            instance = $this.data('vision'),
            options = $.extend({}, $.fn.vision.defaults, $this.data(), typeof option == 'object' && option)
            ;
  
        // set a new instance as data
        if (!instance) {
          $this.data('vision', (instance = new VISION($this, options)));
  
          if (options.initialize) {
            $(document).ready($.proxy(instance.initialize, instance));
          }
        }
  
        // when the plugin is called with a method
        if (typeof option === 'string') {
          if (typeof instance[option] !== 'function') {
            throw new Error('There is no method called "' + option + '"');
          }
  
          args.shift(); // remove first argument ('option')
          instance[option].apply(instance, args);
        }
  
        retval = instance;
      });
  
      return retval;
    };
  
    /**
     * Plugin defaults
     *
     * @public
     * @type {object}
     */
    $.fn.vision.defaults = {
      item: '.item',
      container: '.listing',
      next: '.next',
      pagination: false,
      delay: 600,
      negativeMargin: 10,
      initialize: true
    };
  })(jQuery);
  });
  if(typeof(VISIONHistoryExtension) == "undefined"){
  
  
  var VISIONHistoryExtension = function (options) {
      options = jQuery.extend({}, this.defaults, options);
  
      this.vision = null;
      this.prevSelector = options.prev;
      this.prevUrl = null;
      this.listeners = {
          prev: new VISIONCallbacks()
      };
  
      /**
       * @private
       * @param pageNum
       * @param scrollOffset
       * @param url
       */
      this.onPageChange = function (pageNum, scrollOffset, url) {
          var state = {};
  
          if (!window.history || !window.history.replaceState) {
              return;
          }
  
          history.replaceState(state, document.title, url);
      };
  
      /**
       * @private
       * @param currentScrollOffset
       * @param scrollThreshold
       */
      this.onScroll = function (currentScrollOffset, scrollThreshold) {
          var firstItemScrollThreshold = this.getScrollThresholdFirstItem();
  
          if (!this.prevUrl) {
              return;
          }
  
          currentScrollOffset -= this.vision.$scrollContainer.height();
  
          if (currentScrollOffset <= firstItemScrollThreshold) {
              this.prev();
          }
      };
  
      /**
       * Returns the url for the next page
       *
       * @private
       */
      this.getPrevUrl = function (container) {
          if (!container) {
              container = this.vision.$container;
          }
  
          // always take the last matching item
          var prev_url = jQuery(this.prevSelector, container).last().attr('href');
          if(typeof(prev_url) != 'undefined') {
              prev_url += '&ajaxscroll=1';
          } else {
              prev_url = '';
          }
          return prev_url;
      };
  
      /**
       * Returns scroll threshold. This threshold marks the line from where
       * VISION should start loading the next page.
       *
       * @private
       * @return {number}
       */
      this.getScrollThresholdFirstItem = function () {
          var $firstElement;
  
          $firstElement = this.vision.getFirstItem();
  
          // if the don't have a first element, the DOM might not have been loaded,
          // or the selector is invalid
          if (0 === $firstElement.size()) {
              return -1;
          }
  
          return ($firstElement.offset().top);
      };
  
      /**
       * Renders items
       *
       * @private
       * @param items
       * @param callback
       */
      this.renderBefore = function (items, callback) {
          var vision = this.vision,
              $firstItem = vision.getFirstItem(),
              count = 0;
  
          vision.fire('render', [items]);
  
          jQuery(items).hide(); // at first, hide it so we can fade it in later
  
          $firstItem.before(items);
  
          jQuery(items).fadeIn(400, function () {
              if (++count < items.length) {
                  return;
              }
  
              vision.fire('rendered', [items]);
  
              if (callback) {
                  callback();
              }
          });
      };
  
      return this;
  };
  
  /**
   * @public
   */
  VISIONHistoryExtension.prototype.initialize = function (vision) {
      var self = this;
  
      this.vision = vision;
  
      // expose the extensions listeners
      jQuery.extend(vision.listeners, this.listeners);
  
      // expose prev method
      vision.prev = function() {
          return self.prev();
      };
  
      this.prevUrl = this.getPrevUrl();
  };
  
  /**
   * Bind to events
   *
   * @public
   * @param vision
   */
  VISIONHistoryExtension.prototype.bind = function (vision) {
      var self = this;
  
      vision.on('pageChange', jQuery.proxy(this.onPageChange, this));
      vision.on('scroll', jQuery.proxy(this.onScroll, this));
      vision.on('ready', function () {
          var currentScrollOffset = vision.getCurrentScrollOffset(vision.$scrollContainer),
              firstItemScrollThreshold = self.getScrollThresholdFirstItem();
  
          currentScrollOffset -= vision.$scrollContainer.height();
  
          if (currentScrollOffset <= firstItemScrollThreshold) {
              self.prev();
          }
      });
  };
  
  VISIONHistoryExtension.prototype.unbind = function(a) {
      a.off("pageChange", this.onPageChange), a.off("scroll", this.onScroll), a.off("ready", this.onReady)
  };
  /**
   * Load the prev page
   *
   * @public
   */
  VISIONHistoryExtension.prototype.prev = function () {
      var url = this.prevUrl,
          self = this,
          vision = this.vision;
  
      if (!url) {
          return false;
      }
  
      vision.unbind();
  
      var promise = vision.fire('prev', [url]);
  
      promise.done(function () {
          vision.load(url, function (data, items) {
              self.renderBefore(items, function () {
                  self.prevUrl = self.getPrevUrl(data);
  
                  vision.bind();
  
                  if (self.prevUrl) {
                      self.prev();
                  }
              });
          });
      });
  
      promise.fail(function () {
          vision.bind();
      });
  
      return true;
  };
  
  /**
   * @public
   */
  VISIONHistoryExtension.prototype.defaults = {
      prev: ".prev"
  };
  }
  if(typeof(VISIONNoneLeftExtension) == "undefined"){
  
  
  var VISIONNoneLeftExtension = function(options) {
      options = jQuery.extend({}, this.defaults, options);
  
      this.vision = null;
      this.uid = (new Date()).getTime();
      this.html = (options.html).replace('{text}', options.text);
  
      /**
       * Shows none left message
       */
      this.showNoneLeft = function() {
          var $element = jQuery(this.html).attr('id', 'vision_noneleft_' + this.uid),
              $lastItem = this.vision.getLastItem();
  
          $lastItem.after($element);
          $element.fadeIn();
          jQuery('#load_more_prd').hide();
      };
  
      return this;
  };
  
  /**
   * @public
   */
  VISIONNoneLeftExtension.prototype.bind = function(vision) {
      this.vision = vision;
  
      vision.on('noneLeft', jQuery.proxy(this.showNoneLeft, this));
  };
  
  VISIONNoneLeftExtension.prototype.unbind = function(a) {
      a.off("noneLeft", this.showNoneLeft)
  };
  /**
   * @public
   */
  VISIONNoneLeftExtension.prototype.defaults = {
      text: 'You reached the end.',
      html: '<div class="vision-noneleft" style="text-align: center;">{text}</div>'
  };
  }
  if(typeof(VISIONPagingExtension) == "undefined"){
  
  var VISIONPagingExtension = function() {
      this.vision = null;
      this.pagebreaks = [[0, document.location.toString()]];
      this.lastPageNum = 1;
      this.enabled = true;
      this.listeners = {
          pageChange: new VISIONCallbacks()
      };
  
      /**
       * Fires pageChange event
       *
       * @param currentScrollOffset
       * @param scrollThreshold
       */
      this.onScroll = function(currentScrollOffset, scrollThreshold) {
          if (!this.enabled) {
              return;
          }
  
          var vision = this.vision,
              currentPageNum = this.getCurrentPageNum(currentScrollOffset),
              currentPagebreak = this.getCurrentPagebreak(currentScrollOffset),
              urlPage;
  
          if (this.lastPageNum !== currentPageNum) {
              urlPage = currentPagebreak[1];
  
              vision.fire('pageChange', [currentPageNum, currentScrollOffset, urlPage]);
          }
  
          this.lastPageNum = currentPageNum;
      };
  
      /**
       * Keeps track of pagebreaks
       *
       * @param url
       */
      this.onNext = function(url) {
          var currentScrollOffset = this.vision.getCurrentScrollOffset(this.vision.$scrollContainer);
  
          this.pagebreaks.push([currentScrollOffset, url]);
  
          // trigger pageChange and update lastPageNum
          var currentPageNum = this.getCurrentPageNum(currentScrollOffset) + 1;
  
          this.vision.fire('pageChange', [currentPageNum, currentScrollOffset, url]);
  
          this.lastPageNum = currentPageNum;
      };
  
      /**
       * Keeps track of pagebreaks
       *
       * @param url
       */
      this.onPrev = function(url) {
          var self = this,
              vision = self.vision,
              currentScrollOffset = vision.getCurrentScrollOffset(vision.$scrollContainer),
              prevCurrentScrollOffset = currentScrollOffset - vision.$scrollContainer.height(),
              $firstItem = vision.getFirstItem();
  
          this.enabled = false;
  
          this.pagebreaks.unshift([0, url]);
  
          vision.one('rendered', function() {
              // update pagebreaks
              for (var i = 1, l = self.pagebreaks.length; i < l; i++) {
                  self.pagebreaks[i][0] = self.pagebreaks[i][0] + $firstItem.offset().top;
              }
  
              // trigger pageChange and update lastPageNum
              var currentPageNum = self.getCurrentPageNum(prevCurrentScrollOffset) + 1;
  
              vision.fire('pageChange', [currentPageNum, prevCurrentScrollOffset, url]);
  
              self.lastPageNum = currentPageNum;
  
              self.enabled = true;
          });
      };
  
      return this;
  };
  
  /**
   * @public
   */
  VISIONPagingExtension.prototype.initialize = function(vision) {
      this.vision = vision;
  
      // expose the extensions listeners
      jQuery.extend(vision.listeners, this.listeners);
  };
  
  /**
   * @public
   */
  VISIONPagingExtension.prototype.bind = function(vision) {
      try {
          vision.on('prev', jQuery.proxy(this.onPrev, this), this.priority);
      } catch (exception) {}
  
      vision.on('next', jQuery.proxy(this.onNext, this), this.priority);
      vision.on('scroll', jQuery.proxy(this.onScroll, this), this.priority);
  };
  
  VISIONPagingExtension.prototype.unbind = function(a) {
      try {
          a.off("prev", this.onPrev)
      } catch (b) {}
      a.off("next", this.onNext), a.off("scroll", this.onScroll)
  };
  /**
   * Returns current page number based on scroll offset
   *
   * @param {number} scrollOffset
   * @returns {number}
   */
  VISIONPagingExtension.prototype.getCurrentPageNum = function(scrollOffset) {
      for (var i = (this.pagebreaks.length - 1); i > 0; i--) {
          if (scrollOffset > this.pagebreaks[i][0]) {
              return i + 1;
          }
      }
  
      return 1;
  };
  
  /**
   * Returns current pagebreak information based on scroll offset
   *
   * @param {number} scrollOffset
   * @returns {number}|null
   */
  VISIONPagingExtension.prototype.getCurrentPagebreak = function(scrollOffset) {
      for (var i = (this.pagebreaks.length - 1); i >= 0; i--) {
          if (scrollOffset > this.pagebreaks[i][0]) {
              return this.pagebreaks[i];
          }
      }
  
      return null;
  };
  
  /**
   * @public
   * @type {number}
   */
  VISIONPagingExtension.prototype.priority = 500;
  
  }
  if(typeof(VISIONSpinnerExtension) == "undefined"){
  
  
  var VISIONSpinnerExtension = function(options) {
      options = jQuery.extend({}, this.defaults, options);
  
      this.vision = null;
      this.uid = new Date().getTime();
      this.src = options.src;
      this.html = (options.html).replace('{src}', this.src);
  
      /**
       * Shows spinner
       */
      this.showSpinner = function() {
          var $spinner = this.getSpinner() || this.createSpinner(),
              $lastItem = this.vision.getLastItem();
  
          $lastItem.after($spinner);
          $spinner.fadeIn();
      };
  
      /**
       * Shows spinner
       */
      this.showSpinnerBefore = function() {
          var $spinner = this.getSpinner() || this.createSpinner(),
              $firstItem = this.vision.getFirstItem();
  
          $firstItem.before($spinner);
          $spinner.fadeIn();
      };
  
      /**
       * Removes spinner
       */
      this.removeSpinner = function() {
          if (this.hasSpinner()) {
              this.getSpinner().remove();
              jQuery('#load_more_prd').show();
          }
      };
  
      /**
       * @returns {jQuery|boolean}
       */
      this.getSpinner = function() {
          var $spinner = jQuery('#vision_spinner_' + this.uid);
  
          if ($spinner.size() > 0) {
              return $spinner;
          }
  
          return false;
      };
  
      /**
       * @returns {boolean}
       */
      this.hasSpinner = function() {
          var $spinner = jQuery('#vision_spinner_' + this.uid);
  
          return ($spinner.size() > 0);
      };
  
      /**
       * @returns {jQuery}
       */
      this.createSpinner = function() {
          var $spinner = jQuery(this.html).attr('id', 'vision_spinner_' + this.uid);
  
          $spinner.hide();
  
          return $spinner;
      };
  
      return this;
  };
  
  /**
   * @public
   */
  VISIONSpinnerExtension.prototype.bind = function(vision) {
      this.vision = vision;
  
      vision.on('next', jQuery.proxy(this.showSpinner, this));
  
      try {
          vision.on('prev', jQuery.proxy(this.showSpinnerBefore, this));
      } catch (exception) {}
  
      vision.on('render', jQuery.proxy(this.removeSpinner, this));
  };
  
  VISIONSpinnerExtension.prototype.unbind = function(a) {
      a.off("next", this.showSpinner), a.off("render", this.removeSpinner);
      try {
          a.off("prev", this.showSpinnerBefore)
      } catch (b) {}
  };
  /**
   * @public
   */
  VISIONSpinnerExtension.prototype.defaults = {
      src: 'data:image/gif;base64,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',
      html: '<div class="vision-spinner" style="text-align: center;"><img src="{src}"/></div>'
  };
  }
  if(typeof(VISIONTriggerExtension) == "undefined"){
  
  
  var VISIONTriggerExtension = function(options) {
      options = jQuery.extend({}, this.defaults, options);
  
      this.vision = null;
      this.html = (options.html).replace('{text}', options.text);
      this.htmlPrev = (options.htmlPrev).replace('{text}', options.textPrev);
      this.enabled = true;
      this.count = 0;
      this.offset = options.offset;
      this.$triggerNext = null;
      this.$triggerPrev = null;
  
      /**
       * Shows trigger for next page
       */
      this.showTriggerNext = function() {
          if (!this.enabled) {
              return true;
          }
  
          if (false === this.offset || ++this.count < this.offset) {
              return true;
          }
  
          var $trigger = this.$triggerNext || (this.$triggerNext = this.createTrigger(this.next, this.html));
          var $lastItem = this.vision.getLastItem();
  
          $lastItem.after($trigger);
          $trigger.fadeIn();
  
          return false;
      };
  
      /**
       * Shows trigger for previous page
       */
      this.showTriggerPrev = function() {
          if (!this.enabled) {
              return true;
          }
  
          var $trigger = this.$triggerPrev || (this.$triggerPrev = this.createTrigger(this.prev, this.htmlPrev));
          var $firstItem = this.vision.getFirstItem();
  
          $firstItem.before($trigger);
          $trigger.fadeIn();
  
          return false;
      };
  
      this.onRendered = function() {
          this.enabled = false;
      };
      /**
       * @param clickCallback
       * @returns {*|jQuery}
       * @param {string} html
       */
      this.createTrigger = function(clickCallback, html) {
          var uid = (new Date()).getTime(),
              $trigger;
  
          html = html || this.html;
          $trigger = jQuery(html).attr('id', 'vision_trigger_' + uid);
  
          $trigger.hide();
          $trigger.on('click', jQuery.proxy(clickCallback, this));
  
          return $trigger;
      };
  
      return this;
  };
  
  /**
   * @public
   * @param {object} vision
   */
  VISIONTriggerExtension.prototype.bind = function(vision) {
      var self = this;
  
      this.vision = vision;
  
      try {
          vision.on('prev', jQuery.proxy(this.showTriggerPrev, this), this.priority);
      } catch (exception) {}
  
      vision.on('next', jQuery.proxy(this.showTriggerNext, this), this.priority);
      vision.on('rendered', function () { self.enabled = true; }, this.priority);
  };
  
  VISIONTriggerExtension.prototype.unbind = function(a) {
      a.off("next", this.showTriggerNext), a.off("rendered", this.onRendered);
      try {
          a.off("prev", this.showTriggerPrev)
      } catch (b) {}
  };
  
  /**
   * @public
   */
  VISIONTriggerExtension.prototype.next = function() {
      this.enabled = false;
      this.vision.unbind();
  
      if (this.$triggerNext) {
          this.$triggerNext.remove();
          this.$triggerNext = null;
      }
  
      this.vision.next();
  };
  
  /**
   * @public
   */
  VISIONTriggerExtension.prototype.prev = function() {
      this.enabled = false;
      this.vision.unbind();
  
      if (this.$triggerPrev) {
          this.$triggerPrev.remove();
          this.$triggerPrev = null;
      }
  
      this.vision.prev();
  };
  
  /**
   * @public
   */
  VISIONTriggerExtension.prototype.defaults = {
      text: 'Load more items',
      html: '<div class="vision-trigger vision-trigger-next" style="text-align: center; cursor: pointer;"><a>{text}</a></div>',
      textPrev: 'Load previous items',
      htmlPrev: '<div class="vision-trigger vision-trigger-prev" style="text-align: center; cursor: pointer;"><a>{text}</a></div>',
      offset: 0
  };
  
  /**
   * @public
   * @type {number}
   */
  VISIONTriggerExtension.prototype.priority = 1000;
  }
  
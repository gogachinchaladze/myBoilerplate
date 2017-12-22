/* ===========================================================
 * onepagescroll.js v1.2.2
 * ===========================================================
 * Copyright 2014 Pete Rojwongsuriya.
 * http://www.thepetedesign.com
 *
 * Create an Apple-like website that let user scroll
 * one page at a time
 *
 * Credit: Eike Send for the awesome swipe event
 * https://github.com/peachananr/purejs-onepage-scroll
 *
 * License: GPL v3
 *
 * ========================================================== */

function onePageScroll(element, options) {

  var defaults = {
      sectionContainer: "section",
      easing: "ease",
      animationTime: 500,
      pagination: true,
      updateURL: false,
      keyboard: true,
      beforeMove: null,
      afterMove: null,
      loop: false,
      responsiveFallback: false
    },
    _root = this,
    settings = Object.extend({}, defaults, options),
    el = document.querySelector(element),
    sections = document.querySelectorAll(settings.sectionContainer),
    total = sections.length,
    status = "off",
    topPos = 0,
    lastAnimation = 0,
    quietPeriod = 600,
    paginationList = "",
    body = document.querySelector("body"),
    htmlEl = document.documentElement;

  this.init = function() {
    /*-------------------------------------------*/
    /*  Prepare Everything                       */
    /*-------------------------------------------*/

    _addClass(el, "onepage-wrapper")

    for( var i = 0; i < sections.length; i++){
      _addClass(sections[i], "onepage-section");

      sections[i].dataset.index = i + 1;

      if(settings.pagination == true) {
        paginationList += "<li data-index='" + (i+1) + "'><div class='navigation-wrapper'><span>" + formatNumberWithZero(i)+"</span></div></li>";
      }
    }

    _swipeEvents(el);

    document.addEventListener("swipeDown",  _swipeDown);
    document.addEventListener("swipeUp", _swipeUp);

    // Create Pagination and Display Them

    if(settings.pagination == true) {
      var pagination = document.createElement("ul");
      pagination.setAttribute("class", "onepage-pagination");

      body.appendChild(pagination);
      pagination.innerHTML = paginationList;
    }

    _addClass(document.querySelector(settings.sectionContainer + "[data-index='1']"), "active");
    _addClass(htmlEl, "viewing-page-1");
    _addClass(htmlEl,"onepage-scroll");

    if(settings.pagination == true){
      _addClass(document.querySelector(".onepage-pagination li[data-index='1']"), "active");
    }

    _paginationHandler = function() {
      var page_index = this.dataset.index;
      moveTo(page_index);
    }


    if(settings.pagination == true)  {
      var pagination_links = document.querySelectorAll(".onepage-pagination li");

      for( var i = 0; i < pagination_links.length; i++){
        pagination_links[i].addEventListener('click', _paginationHandler);
      }
    }

    _mouseWheelHandler = function(event) {
      event.preventDefault();
      var delta = event.wheelDelta || -event.detail;
      if (!_hasClass(htmlEl, "disabled-onepage-scroll")) _init_scroll(event, delta);
    }

    document.addEventListener('mousewheel', _mouseWheelHandler);
    document.addEventListener('DOMMouseScroll', _mouseWheelHandler);


    if(settings.responsiveFallback != false) {
      window.onresize = function(){
        _responsive();
      }

      _responsive();
    }

    _keydownHandler = function(e) {
      var tag = e.target.tagName.toLowerCase();

      if (!_hasClass(htmlEl, "disabled-onepage-scroll")) {
        switch(e.which) {
          case 38:
            if (tag != 'input' && tag != 'textarea') moveUp()
            break;
          case 40:
            if (tag != 'input' && tag != 'textarea') moveDown()
            break;
          case 32: //spacebar
            if (tag != 'input' && tag != 'textarea') moveDown()
            break;
          case 33: //pageg up
            if (tag != 'input' && tag != 'textarea') moveUp()
            break;
          case 34: //page dwn
            if (tag != 'input' && tag != 'textarea') moveDown()
            break;
          case 36: //home
            moveTo(1);
            break;
          case 35: //end
            moveTo(total);
            break;
          default: return;
        }
      }
      return false;
    }

    if(settings.keyboard == true) {
      document.onkeydown = _keydownHandler;
    }
    return false;
  }

  /*-------------------------------------------------------*/
  /*  Private Functions                                    */
  /*-------------------------------------------------------*/
  /*------------------------------------------------*/
  /*  Credit: Eike Send for the awesome swipe event */
  /*------------------------------------------------*/
  _swipeEvents = function(el){
    var startX,
      startY;

    document.addEventListener("touchstart", touchstart);

    function touchstart(event) {
      var touches = event.touches;
      if (touches && touches.length) {
        startX = touches[0].pageX;
        startY = touches[0].pageY;
        document.addEventListener("touchmove", touchmove);
      }
    }

    function touchmove(event) {
      var touches = event.touches;
      if (touches && touches.length) {
        event.preventDefault();
        var deltaX = startX - touches[0].pageX;
        var deltaY = startY - touches[0].pageY;

        if (deltaX >= 50) {
          var event = new Event('swipeLeft');
          document.dispatchEvent(event);
        }
        if (deltaX <= -50) {
          var event = new Event('swipeRight');
          document.dispatchEvent(event);
        }
        if (deltaY >= 50) {
          var event = new Event('swipeUp');
          document.dispatchEvent(event);
        }
        if (deltaY <= -50) {
          var event = new Event('swipeDown');
          document.dispatchEvent(event);
        }

        if (Math.abs(deltaX) >= 50 || Math.abs(deltaY) >= 50) {
          document.removeEventListener('touchmove', touchmove);
        }
      }
    }

  };
  _swipeDown = function (event) {
    if (!_hasClass(htmlEl, "disabled-onepage-scroll")) event.preventDefault();
    moveUp();
  };
  _swipeUp = function (event) {
    if (!_hasClass(htmlEl, "disabled-onepage-scroll")) event.preventDefault();
    moveDown();
  };
  /*-----------------------------------------------------------*/
  /*  Utility to add/remove class easily with javascript       */
  /*-----------------------------------------------------------*/

  _trim = function(str) {
    return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
  }

  _hasClass = function(ele,cls) {
    if (ele.className) {
      return ele.className.match(new RegExp('(\\s|^)'+cls+'(\\s|$)'));
    } else {
      return ele.className = cls;
    }
  }

  _addClass = function(ele,cls) {
    if (!_hasClass(ele,cls)) ele.className += " "+cls;
    ele.className = _trim(ele.className)
  }

  _removeClass = function(ele,cls) {
    if (_hasClass(ele,cls)) {
      var reg = new RegExp('(\\s|^)'+cls+'(\\s|$)');
      ele.className=ele.className.replace(reg,' ');
    }
    ele.className = _trim(ele.className)
  }

  /*-----------------------------------------------------------*/
  /*  Transtionend Normalizer by Modernizr                     */
  /*-----------------------------------------------------------*/

  _whichTransitionEvent = function(){
    var t;
    var el = document.createElement('fakeelement');
    var transitions = {
      'transition':'transitionend',
      'OTransition':'oTransitionEnd',
      'MozTransition':'transitionend',
      'WebkitTransition':'webkitTransitionEnd'
    }

    for(t in transitions){
      if( el.style[t] !== undefined ){
        return transitions[t];
      }
    }
  }

  /*-----------------------------------------------------------*/
  /*  Function to perform scroll to top animation              */
  /*-----------------------------------------------------------*/

  _scrollTo = function(element, to, duration) {
    if (duration < 0) return;
    var difference = to - element.scrollTop;
    var perTick = difference / duration * 10;

    setTimeout(function() {
      element.scrollTop = element.scrollTop + perTick;
      if (element.scrollTop == to) return;
      _scrollTo(element, to, duration - 10);
    }, 10);
  }



  /*---------------------------------*/
  /*  Function to transform the page */
  /*---------------------------------*/

  _transformPage = function(el2, settings, pos, index, next_el) {
    if (typeof settings.beforeMove == 'function') settings.beforeMove(index, next_el);

    var transformCSS = "-webkit-transform: translate3d(0, " + pos + "%, 0); -webkit-transition: -webkit-transform " + settings.animationTime + "ms " + settings.easing + "; -moz-transform: translate3d(0, " + pos + "%, 0); -moz-transition: -moz-transform " + settings.animationTime + "ms " + settings.easing + "; -ms-transform: translate3d(0, " + pos + "%, 0); -ms-transition: -ms-transform " + settings.animationTime + "ms " + settings.easing + "; transform: translate3d(0, " + pos + "%, 0); transition: transform " + settings.animationTime + "ms " + settings.easing + ";";

    el2.style.cssText = transformCSS;

    var transitionEnd = _whichTransitionEvent();
    el2.addEventListener(transitionEnd, endAnimation, false);

    function endAnimation() {
      if (typeof settings.afterMove == 'function') settings.afterMove(index, next_el);
      el2.removeEventListener(transitionEnd, endAnimation)
    }
  }

  /*-------------------------------------------*/
  /*  Responsive Fallback trigger              */
  /*-------------------------------------------*/

  _responsive = function() {

    if (document.body.clientWidth < settings.responsiveFallback) {

      _addClass(htmlEl, "disabled-onepage-scroll");
      _removeClass(htmlEl, "onepage-scroll");

      document.removeEventListener('mousewheel', _mouseWheelHandler);
      document.removeEventListener('DOMMouseScroll', _mouseWheelHandler);
      _swipeEvents(el);
      document.removeEventListener("swipeDown", _swipeDown);
      document.removeEventListener("swipeUp", _swipeUp);

    }
    else {

      if (_hasClass(htmlEl, "disabled-onepage-scroll")) {
        _removeClass(htmlEl, "disabled-onepage-scroll");
        _addClass(htmlEl, "onepage-scroll");
        _scrollTo(document.documentElement, 0, 2000);
      }

      _swipeEvents(el);
      document.addEventListener("swipeDown",  _swipeDown);
      document.addEventListener("swipeUp", _swipeUp);

      document.addEventListener('mousewheel', _mouseWheelHandler);
      document.addEventListener('DOMMouseScroll', _mouseWheelHandler);
    }
  }

  /*-------------------------------------------*/
  /*  Initialize scroll detection              */
  /*-------------------------------------------*/

  _init_scroll = function(event, delta) {
    var deltaOfInterest = delta,
      timeNow = new Date().getTime();

    // Cancel scroll if currently animating or within quiet period
    if(timeNow - lastAnimation < quietPeriod + settings.animationTime) {
      event.preventDefault();
      return;
    }

    if (deltaOfInterest < 0) {
      moveDown(el)
    } else {
      moveUp(el)
    }

    lastAnimation = timeNow;
  }


  /*-------------------------------------------------------*/
  /*  Public Functions                                     */
  /*-------------------------------------------------------*/

  /*---------------------------------*/
  /*  Function to move down section  */
  /*---------------------------------*/

  this.moveDown = function() {

    var index = document.querySelector(settings.sectionContainer +".active").dataset.index,
        nextIndex = parseInt(index) + 1,
        next = document.querySelector(settings.sectionContainer + "[data-index='" + nextIndex + "']");

    if(next){
      this.moveTo(nextIndex);
    }
    else{
      if (settings.loop == true) {
        this.moveTo(1);
      }
      else {
        return;
      }
    }
  };

  /*---------------------------------*/
  /*  Function to move up section    */
  /*---------------------------------*/

  this.moveUp = function() {
    var index = document.querySelector(settings.sectionContainer +".active").dataset.index,
        prevIndex = parseInt(index) - 1,
        prev = document.querySelector(settings.sectionContainer + "[data-index='" + prevIndex + "']");

    if(prev) {
      this.moveTo(prevIndex);
    }
    else{
      if (settings.loop == true) {
        this.moveTo(total);
      } else {
        return;
      }
    }
  };

  /*-------------------------------------------*/
  /*  Function to move to specified section    */
  /*-------------------------------------------*/

  this.moveTo = function(page_index) {
    var current = document.querySelector(settings.sectionContainer + ".active"),
        next = document.querySelector(settings.sectionContainer + "[data-index='" + (page_index) + "']");

    if(next) {
      var next_index = next.dataset.index;
      _removeClass(current, "active");
      _addClass(next, "active");
      if (typeof settings.beforeMove == 'function') settings.beforeMove(next_index, next);

      htmlEl.className = htmlEl.className.replace(/\bviewing-page-\d.*?\b/g, '');
      _addClass(htmlEl, "viewing-page-"+ next_index);

      if(settings.pagination == true){
        _removeClass(document.querySelector(".onepage-pagination li" + ".active"), "active");
        _addClass(document.querySelector(".onepage-pagination li" + "[data-index='" + (page_index) + "']"), "active");
      }

      setTimeout(function () {
        if (typeof settings.afterMove == 'function') settings.afterMove(next_index, next);
      },settings.animationTime + quietPeriod);
    }

  };

  this.init();
}

/*------------------------------------------------*/
/*  Ulitilities Method                            */
/*------------------------------------------------*/
function formatNumberWithZero(num){
  if(num<10){
    return '0' + num.toString();
  }
  else return num;
}

/*-----------------------------------------------------------*/
/*  Function by John Resig to replicate extend functionality */
/*-----------------------------------------------------------*/

Object.extend = function(orig){
  if ( orig == null )
    return orig;

  for ( var i = 1; i < arguments.length; i++ ) {
    var obj = arguments[i];
    if ( obj != null ) {
      for ( var prop in obj ) {
        var getter = obj.__lookupGetter__( prop ),
          setter = obj.__lookupSetter__( prop );

        if ( getter || setter ) {
          if ( getter )
            orig.__defineGetter__( prop, getter );
          if ( setter )
            orig.__defineSetter__( prop, setter );
        } else {
          orig[ prop ] = obj[ prop ];
        }
      }
    }
  }

  return orig;
};


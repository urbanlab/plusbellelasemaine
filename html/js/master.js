
/* -------------------------------------------------------------------------------
 GLOBAL
---------------------------------------------------------------------------------- */

// Console Log standardization -------------------------------------------------
window.log = function f(){ log.history = log.history || []; log.history.push(arguments); if(this.console) { var args = arguments, newarr; args.callee = args.callee.caller; newarr = [].slice.call(args); if (typeof console.log === 'object') log.apply.call(console.log, console, newarr); else console.log.apply(console, newarr);}};
(function(a){function b(){}for(var c="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn".split(","),d;!!(d=c.pop());){a[d]=a[d]||b;}})
(function(){try{console.log();return window.console;}catch(a){return (window.console={});}}());


// debouncing function from John Hann
// http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
(function($,sr){
  var debounce = function (func, threshold, execAsap) {
      var timeout;

      return function debounced () {
          var obj = this, args = arguments;
          function delayed () {
              if (!execAsap)
                  func.apply(obj, args);
              timeout = null;
          }

          if (timeout)
              clearTimeout(timeout);
          else if (execAsap)
              func.apply(obj, args);

          timeout = setTimeout(delayed, threshold || 100);
      };
  };
  // smartresize 
  jQuery.fn[sr] = function(fn){  return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };
})(jQuery,'smartresize');

/*  ------------------------------------------------------------------------------
  VARS
---------------------------------------------------------------------------------- */

/*  ------------------------------------------------------------------------------
	ON DOM READY > START
---------------------------------------------------------------------------------- */
$(document).ready(function(){
	detectDevice();
	
	$('.alert-auto-close').delay(3000).slideUp('fast');
	
	

});

/*  ------------------------------------------------------------------------------
	ON WINDOW LOAD > START ANIMATIONS
---------------------------------------------------------------------------------- */
$(window).load(function(){
    detectDevice();
	
});

/*  ------------------------------------------------------------------------------
	ON WINDOW SCROLL > START ANIMATIONS
---------------------------------------------------------------------------------- */
$(window).scroll(function(){

});

/*  ------------------------------------------------------------------------------
	ON WINDOW RESIZE > START ANIMATIONS
---------------------------------------------------------------------------------- */
$(window).smartresize(function(){
	detectDevice();
  
});

/*  ------------------------------------------------------------------------------
	GLOBAL FUNCTIONS
---------------------------------------------------------------------------------- */
// Verification d'un email ---------------------------------
function isValidEmailAddress(emailAddress) {
  var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
  return pattern.test(emailAddress);
}

// get the device width and apply a class to body
function detectDevice(){
	var windowWidth = $(window).width();
    if (windowWidth>=1200){$('body').removeClass().addClass('wide');}    
    else if ( windowWidth<1200 && windowWidth>=992 ){$('body').removeClass().addClass('screen');}
    else if (windowWidth<992 && windowWidth>=768 ){$('body').removeClass().addClass('tablet');}
    else {$('body').removeClass().addClass('mobile');}
}

// Link to the top of document : smooth scroll animation ----------------------
function toTheTop(){
	$('#tothetop').bind('click', function(event){
		event.preventDefault();
		$('html,body').animate({scrollTop:0},'normal');
	});
}


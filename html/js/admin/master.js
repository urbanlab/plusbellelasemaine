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
	$('.alert-auto-close').delay(3000).slideUp('fast');
	
	
	if($('.login-page').length) {
		
		$('input').iCheck({
			checkboxClass: 'icheckbox_square-blue',
			radioClass: 'iradio_square-blue',
			increaseArea: '20%' // optional
		});
	}

	// gestion des champs de date
	$('.form-date').datepicker({format: 'dd/mm/yyyy', autoclose: true, language: 'fr'});
});

/*  ------------------------------------------------------------------------------
	ON WINDOW LOAD 
---------------------------------------------------------------------------------- */
$(window).load(function(){
	
	
});

/*  ------------------------------------------------------------------------------
	ON WINDOW SCROLL 
---------------------------------------------------------------------------------- */
$(window).scroll(function(){

});

/*  ------------------------------------------------------------------------------
	ON WINDOW RESIZE 
---------------------------------------------------------------------------------- */
$(window).smartresize(function(){
	
});

/*  ------------------------------------------------------------------------------
	GLOBAL FUNCTIONS
---------------------------------------------------------------------------------- */

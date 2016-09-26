/*
   Forcer le rendering même après un chargement de noisette ajax
   https://github.com/pmq20/mathjax-rails/issues/18
   https://contrib.spip.net/Astuces-courtes-pour-SPIP
*/

$(document).ready(function() {
 var refresh_math_ml = function() {
   if (window.MathJax) {
	  MathJax.Hub.Queue(
	    ["Typeset",MathJax.Hub]
	  );
   }
 }

 if (typeof onAjaxLoad == 'function')
 		 onAjaxLoad(refresh_math_ml);

 if (window.jQuery) jQuery(document).ready(function() {
  		 refresh_math_ml(document);
 });

});

/*
  Un defilement doux code alternatif propose par ZiWaM <ziwam@bzh.net>
  avec une modif par Fil <fil@rezo.net>
  cf. http://www.spip-contrib.net/Ancres-douces#forum409454
*/
;(function($){
$.fn.ancresdouces = function() {
  return this.click(function() {
    if ((location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,''))
    && (location.hostname == this.hostname)) {
      var hash = this.hash.slice(1);
      var $target = $('#'+hash);
      $target = $target.length && $target
        || $('[name=' + this.hash +']');
      if ($target.length) {
        var targetOffset = $target.offset().top;
        $('html,body')
        .animate({scrollTop: targetOffset},
          1000,
          function(){location.hash = hash;}
        );
        return false;
     }
  }
});
}})(jQuery);

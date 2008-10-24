/*
  Un defilement doux code alternatif propose par ZiWaM <ziwam@bzh.net>
  avec une modif par Fil <fil@rezo.net>
  cf. http://www.spip-contrib.net/Ancres-douces#forum409454
*/
;(function($){
$.fn.ancresdouces = function() {
  function filterPath(string) {
  return string
    .replace(/^\//,'')
    .replace(/(index|default).[a-zA-Z]{3,4}$/,'')
    .replace(/\/$/,'');
  }
  var locationPath = filterPath(location.pathname);
  return this.each(function() {
    var thisPath = filterPath(this.pathname) || locationPath;
    if (  locationPath == thisPath
    && (location.hostname == this.hostname || !this.hostname)
    && this.hash.replace(/#/,'') ) {
      var $target = $(this.hash), target = this.hash;
      if (target) {
        var targetOffset = $target.offset().top;
        $(this).click(function(event) {
          event.preventDefault();
          $('html, body').animate({scrollTop: targetOffset}, 1000, function() {
            location.hash = target;
          });
        });
      }
    }
  });
}})(jQuery);

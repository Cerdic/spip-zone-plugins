/*
  Un defilement doux code provenant de
  http://www.learningjquery.com/2007/10/improved-animated-scrolling-script-for-same-page-links
  update 3 (en bas de la page)
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

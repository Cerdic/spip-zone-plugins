(function($){
/*
 * resizehandle.js (c) Fil 2007-2011, plugin pour jQuery
 * @ http://www.jquery.info/spip.php?article44
 * Distribue sous licence GNU/LGPL et MIT
 */
$.fn.resizehandle = function() {
  return this.each(function() {
    var me = $(this);
    me.after(
      $('<div class="resizehandle"></div>')
      .css({height:'16px',width:Math.max(me.width()-4,10)}) // bug MSIE si 100%
      .bind('mousedown', function(e) {
        var h = me.height();
        var y = e.clientY;
        var moveHandler = function(e) {
          me
          .height(Math.max(20, e.clientY + h - y));
        };
        var upHandler = function(e) {
          $('html')
          .unbind('mousemove',moveHandler)
          .unbind('mouseup',upHandler);
        };
        $('html')
        .bind('mousemove', moveHandler)
        .bind('mouseup', upHandler);
      })
    );
  });
};
})(jQuery);

/*
 * resizehandle.js (c) Fil 2007, plugin pour jQuery ecrit
 * a partir du fichier resize.js du projet DotClear
 * (c) 2005 Nicolas Martin & Olivier Meunier and contributors
 */
jQuery.fn.resizehandle = function() {
  return this.each(function() {
    var me = jQuery(this);
    me.after(
      jQuery('<div class="resizehandle"></div>')
      .bind('mousedown', function(e) {
        var h = me.height();
        var y = e.clientY;
        jQuery('body')
        .bind('mousemove', function(e) {
          me
          .height(Math.max(20, e.clientY + h - y));
        })
        .bind('mouseup', function(e) {
          me
          .height(Math.max(20, e.clientY + h - y));
          jQuery('body')
          .unbind('mousemove')
          .unbind('mouseup');
        })
      })
    );
  });
}

$(document).ready(function() {
	// TODO : creer des div supplémentaires pour les desc d'arguments
	$('div.trad .arg-name[@id^=desc]')
		.each(function() {
			$this= $(this);
			var id= $this.attr('id');
			$this.attr('id', 'fake'+id);
			var desc= $this.attr('title');
			if(!desc) desc='';
			$this.after("<div class='editable' id='"+id+"'>"+desc+"</div>");
		});

	// rendre editables tous les div concernés
	$('div.trad [@id^=desc]:not(.arg-name)')
		.addClass('editable')
		.editable("?action=docjquery", {
			type: 'textarea',
			submit: 'OK',
			cancel: 'Annuler',
			onblur: 'ignore',
			oncreate: function(t) {
				$(this).resizehandle();
			}
		});
});

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
        var moveHandler = function(e) {
          me
          .height(Math.max(20, e.clientY + h - y));
        };
        var upHandler = function(e) {
          jQuery('html')
          .unbind('mousemove',moveHandler)
          .unbind('mouseup',upHandler);
        };
        jQuery('html')
        .bind('mousemove', moveHandler)
        .bind('mouseup', upHandler);
      })
    );
  });
}

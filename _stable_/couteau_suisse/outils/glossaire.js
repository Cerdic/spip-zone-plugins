var gloss_posx = 0;
var gloss_posy = 0;
var gloss_offy = 0
var gloss_el = null;

jQuery(document).ready(function() {
  if($('span.gl_mot').length) {
    $('body').append('<div id="overDiv" style="position:absolute; display:none; visibility: hidden;" class="cs_glossaire"><span class="gl_dl"><span class="gl_dt">TITRE</span><span class="gl_dd">Definition</span></span></div>');
	$('span.gl_mot').bind('mouseout',
		function(e) {
			HideOverDiv();
		}
	);
	$('span.gl_mot').bind('mouseover', 
		function(e) {
			GlossMouse(e);
			ShowOverDiv();
		}
	);
	gloss_el = document.getElementById("overDiv");
  }
});

/*
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
*/

function ShowOverDiv() {
	var yy = (gloss_offy < 200) ? (gloss_posy + 10) : (gloss_posy - 70);
	var xx = (gloss_posx + 10);
	
	gloss_el.style.top  = yy.toString()+"px";
	gloss_el.style.left = xx.toString()+"px";
	
	gloss_el.style.display    = 'block';
	gloss_el.style.visibility = 'visible';
}

function HideOverDiv() {
	gloss_el.style.display    = 'none';
	gloss_el.style.visibility = 'hidden';
}

function GlossMouse(e) {
	if (document.all) {
		gloss_offy = (event.clientY + document.body.scrollTop);
		gloss_posx = (event.x + document.body.scrollLeft); 
		gloss_posy = (event.y + document.body.scrollTop);
	}
	else {
		gloss_offy = e.clientY;
		gloss_posx = e.pageX; 
		gloss_posy = e.pageY;
	}
}

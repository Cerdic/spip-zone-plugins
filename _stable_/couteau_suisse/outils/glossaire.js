var gloss_posx = 0;
var gloss_posy = 0;
var gloss_offy = 0;
var gloss_el = null;
var gloss_dt = null;
var gloss_dd = null;

jQuery(document).ready(function() {
  if($('span.gl_js').length) {
    $('body').append('<div id="glossOverDiv" style="position:absolute; display:none; visibility: hidden;"><span class="gl_dl"><span class="gl_dt">TITRE</span><span class="gl_dd">Definition</span></span></div>');
	$('span.gl_mot').bind('mouseout',
		function(e) {
			gloss_el.style.display    = 'none';
			gloss_el.style.visibility = 'hidden';
		}
	);
	$('span.gl_mot').bind('mouseover', 
		function(e) {
			// cas du surligneur (SPIP 1.93)
			if(this.firstChild.className=="spip_surligne") {
				this.className = "gl_mot spip_surligne";
				this.innerHTML = this.firstChild.innerHTML;
				//alert(this.firstChild.className);
			}
			GlossMouse(e);
			gloss_el.style.top  = gloss_posy.toString()+"px";
			gloss_el.style.left = gloss_posx.toString()+"px";
			gloss_dt.innerHTML = this.nextSibling.title; // titre
			gloss_dd.innerHTML = this.nextSibling.nextSibling.title; // definition
			gloss_el.style.fontSize = $(this).css('font-size');
			gloss_el.style.display    = 'block';
			gloss_el.style.visibility = 'visible';
		}
	);
	gloss_el = document.getElementById('glossOverDiv');
	gloss_dt = gloss_el.firstChild.firstChild;
	gloss_dd = gloss_el.firstChild.lastChild;
  }
});

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

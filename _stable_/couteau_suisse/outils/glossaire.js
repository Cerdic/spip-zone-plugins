var gloss_posx = 0;
var gloss_posy = 0;
var gloss_offy = 0
var gloss_el = null;
var gloss_dt = null;
var gloss_dd = null;

jQuery(document).ready(function() {
  if($('span.gl_js').length) {
    $('body').append('<div id="overDiv" style="position:absolute; display:none; visibility: hidden;" class="cs_glossaire"><span class="gl_dl"><span class="gl_dt">TITRE</span><span class="gl_dd">Definition</span></span></div>');
	$('span.gl_mot').bind('mouseout',
		function(e) {
			GlossHideOverDiv();
		}
	);
	$('span.gl_mot').bind('mouseover', 
		function(e) {
			GlossMouse(e);
			GlossShowOverDiv(e);
		}
	);
	gloss_el = document.getElementById("overDiv");
	gloss_dt = gloss_el.firstChild.firstChild;
	gloss_dd = gloss_el.firstChild.lastChild;
  }
});

function GlossShowOverDiv(e) {
	var yy = (gloss_offy < 200) ? (gloss_posy + 10) : (gloss_posy - 70);
	var xx = (gloss_posx + 10);
	gloss_el.style.top  = yy.toString()+"px";
	gloss_el.style.left = xx.toString()+"px";
	gloss_el.style.display    = 'block';
	gloss_el.style.visibility = 'visible';
	//gloss_el.html = gloss_el.style.top;
	gloss_dt.innerHTML = e.target.nextSibling.title;
	gloss_dd.innerHTML = e.target.nextSibling.nextSibling.title;
}

function GlossHideOverDiv() {
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

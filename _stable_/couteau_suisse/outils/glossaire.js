var gloss_el = null;
var gloss_dt = null;
var gloss_dd = null;

jQuery(document).ready(function() {
  if(jQuery('span.gl_js').length) {
    jQuery('body').append('<div id="glossOverDiv" style="position:absolute; display:none; visibility: hidden;"><span class="gl_dl"><span class="gl_dt">TITRE</span><span class="gl_dd">Definition</span></span></div>');
	jQuery('span.gl_mot').hover(
		function(e) {
			// cas du surligneur (SPIP 1.93)
			if(this.firstChild.className=="spip_surligne") {
				this.className = "gl_mot spip_surligne";
				this.innerHTML = this.firstChild.innerHTML;
			}
			gloss_el.style.top  = e.pageY.toString()+"px";
			gloss_el.style.left = e.pageX.toString()+"px";
			gloss_dt.innerHTML = this.nextSibling.title; // titre
			gloss_dd.innerHTML = this.nextSibling.nextSibling.title; // definition
			gloss_el.style.fontSize = jQuery(this).css('font-size');
			gloss_el.style.fontFamily = jQuery(this).css('font-family');
			gloss_el.style.display    = 'block';
			gloss_el.style.visibility = 'visible';
		},
		function(e) {
			gloss_el.style.display    = 'none';
			gloss_el.style.visibility = 'hidden';
		}
	);
	gloss_el = document.getElementById('glossOverDiv');
	gloss_dt = gloss_el.firstChild.firstChild;
	gloss_dd = gloss_el.firstChild.lastChild;
  }
});
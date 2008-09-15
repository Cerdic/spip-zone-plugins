var gloss_el = null;
var gloss_dt = null;
var gloss_dd = null;

// compatibilite Ajax : ajouter "this" a "jQuery" pour mieux localiser les actions
// et tagger avec cs_done pour eviter de binder plrs fois le meme bloc
function glossaire_init() {
  if(jQuery('span.gl_js', this).length) {
	if(!jQuery('#glossOverDiv').length) {
		jQuery('body').append('<div id="glossOverDiv" style="position:absolute; display:none; visibility: hidden;"><span class="gl_dl"><span class="gl_dt">TITRE</span><span class="gl_dd">Definition</span></span></div>');
		gloss_el = document.getElementById('glossOverDiv');
		gloss_dt = gloss_el.firstChild.firstChild;
		gloss_dd = gloss_el.firstChild.lastChild;
	}
	jQuery('span.gl_mot', this).not('.cs_done').addClass('cs_done').hover(
		function(e) {
			// cas du surligneur (SPIP 1.93)
			if(this.firstChild.className=="spip_surligne") {
				this.className = "gl_mot spip_surligne";
				this.innerHTML = this.firstChild.innerHTML;
			}
			gloss_dt.innerHTML = jQuery(this).parent().children('.gl_js')[0].title;  // titre
			gloss_dd.innerHTML = jQuery(this).parent().children('.gl_jst')[0].title; // definition
			reg = jQuery(this).css('font-size').match(/^\d\d?(?:\.\d+)?px/);
			if(reg) gloss_el.style.fontSize = reg[0];
			jQuery(gloss_el)
				.css('top',e.pageY.toString()+"px")
				.css('left', e.pageX.toString()+"px")
				.css('font-family', jQuery(this).css('font-family'));
			gloss_el.style.display    = 'block';
			gloss_el.style.visibility = 'visible';
			},
		function(e) {
			gloss_el.style.display    = 'none';
			gloss_el.style.visibility = 'hidden';
		}
	);
	jQuery('a.cs_glossaire').focus(
		function() {
			legl_mot = this.firstChild;
			gloss_dt.innerHTML = jQuery(this).children('.gl_js')[0].title;  // titre
			gloss_dd.innerHTML = jQuery(this).children('.gl_jst')[0].title; // definition
			reg = jQuery(this.firstChild).css('font-size').match(/^\d\d?(?:\.\d+)?px/);
			if(reg) gloss_el.style.fontSize = reg[0];
			var result = jQuery(this).offset({ scroll: false });
			jQuery(gloss_el)
				.css('top',result.top+"px")
				.css('left', result.left+"px")
				.css('font-family', jQuery(this.firstChild).css('font-family'));
			gloss_el.style.display    = 'block';
			gloss_el.style.visibility = 'visible';
			}
	);
	jQuery('a.cs_glossaire').blur(
		function() {
			gloss_el.style.display    = 'none';
			gloss_el.style.visibility = 'hidden';
			}
	);
  }
}
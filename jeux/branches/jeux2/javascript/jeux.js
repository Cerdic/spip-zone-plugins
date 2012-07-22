// compatibilite Ajax : ajouter "this" a "jQuery" pour mieux localiser les actions 
// et tagger avec cs_done pour eviter de binder plrs fois le meme bloc
function cs_jeux_init() {
	jQuery('.jeux_deplie', this)
		.not('.jeux_done').addClass('jeux_done')
		.click( function(){
			jQuery(this).toggleClass('jeux_replie')
				.next().toggleClass('jeux_invisible')
			// annulation du clic
			return false;
		});
}


// un JS actif replie les blocs invisibles
document.write('<style type="text/css">.jeux_invisible{display:none;}</style>');

// lance jQuery
if(typeof onAjaxLoad=='function')onAjaxLoad(cs_jeux_init);
if(window.jQuery)jQuery(document).ready(function(){
	cs_jeux_init.apply(this);
});



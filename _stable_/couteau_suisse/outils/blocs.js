// compatibilite Ajax : ajouter "this" a "jQuery" pour mieux localiser les actions 
// et tagger avec cs_done pour eviter de binder plrs fois le meme bloc
function blocs_init() {
	// si un resume est present...
	jQuery('div.blocs_resume', this).not('.cs_done').addClass('cs_done')
	  .prev().removeClass('blocs_click')
	  .click(function(){
		jQuery(this).toggleClass('blocs_replie')
		.next().toggleClass('blocs_invisible')
		.next().toggleClass('blocs_invisible');
		// annulation du clic
		return false;
		});

	// sinon...
	jQuery('h4.blocs_click', this).not('.cs_done').addClass('cs_done')
	  .click( function(){
		jQuery(this).toggleClass('blocs_replie')
		.next().toggleClass('blocs_invisible');
		// annulation du clic
		return false;
		});

	// si un bloc ajax est present...
	jQuery('h4.blocs_ajax', this).not('.cs_done').addClass('cs_done')
	  .click(function(){
		var k=jQuery(this).children().attr("href");
		if(k=='javascript:;') return false;
		// une fois le bloc ajax en place, plus besoin de le recharger ensuite
		jQuery(this).children().attr("href", 'javascript:;');
		// ici, on charge !
		jQuery(this).parent().children(".blocs_destination")
//.animeajax()
		.load(k);
		return false;
		});
}

// un JS actif replie les blocs invisibles
document.write('<style type="text/css">div.blocs_invisible{display:none;}</style>');
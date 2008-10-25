// fonction de de/re-pliement
jQuery.fn.blocs_toggle = function() {
	if (!this.length) return this;
	dest = this.toggleClass('blocs_replie')
		.next().toggleClass('blocs_invisible')
	// est-on sur un resume ?
	if (dest.is('div.blocs_resume')) dest.next().toggleClass('blocs_invisible');
	// est-on sur un bloc ajax ?
	url = this.children().attr("href");
	if(url != 'javascript:;') {
		// une fois le bloc ajax en place, plus besoin de le recharger ensuite
		this.children().attr("href", 'javascript:;');
		// ici, on charge !
		this.parent().children(".blocs_destination")
		//.animeajax()
		.load(url);
	}
	return this;
};

// replie tout sauf le bloc appelant et sa lignee parentale
jQuery.fn.blocs_replie_tout = function() {
	if(blocs_replier_tout) {
		var lignee = this.parents('div.cs_blocs').children('h4.blocs_titre');
		jQuery('h4.blocs_titre').not('.blocs_replie').not(lignee).blocs_toggle();
	}
	return this;
}

// compatibilite Ajax : ajouter "this" a "jQuery" pour mieux localiser les actions 
// et tagger avec cs_done pour eviter de binder plrs fois le meme bloc
function blocs_init() {
	jQuery('h4.blocs_titre', this).not('.cs_done').addClass('cs_done')
	  .click( function(){
		jQuery(this).blocs_replie_tout().blocs_toggle();
		// annulation du clic
		return false;
		});
}

// un JS actif replie les blocs invisibles
document.write('<style type="text/css">div.blocs_invisible{display:none;}</style>');

// une fonction et une variable pour reperer une pagination
function blocs_get_pagination(url) {
	tab=url.match(/#pagination([0-9]+)/);
	if (tab==null) return false;
	return tab[1];
}
var blocs_pagination = blocs_get_pagination(window.location.hash);

/*
// Si un bloc contient une pagination inseree dans un bloc,
// code JS a inserer dans le header de votre squelette APRES les appels du Couteau Suisse
jQuery(document).ready(function() {
	if(blocs_pagination!==false) {
		jQuery('div.cs_bloc' + blocs_pagination + ' h4.blocs_titre').eq(0).click();
		window.location.hash = '#pagination' + blocs_pagination;
	}
});
*/
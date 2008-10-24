// fonction de de/re-pliement
(function($) { 
	$.fn.blocs_toggle = function() { 
		return $(this).toggleClass('blocs_replie').next().toggleClass('blocs_invisible'); 
	};
})(jQuery);

// replie tout sauf le bloc appelant
// todo : eviter de replier un parent !!
function blocs_replie_tout(appel) {
	jQuery('h4.blocs_click').not('.blocs_replie').not(appel).blocs_toggle();
}

// compatibilite Ajax : ajouter "this" a "jQuery" pour mieux localiser les actions 
// et tagger avec cs_done pour eviter de binder plrs fois le meme bloc
function blocs_init() {
	// si un resume est present...
	jQuery('div.blocs_resume', this).not('.cs_done').addClass('cs_done')
	  .prev().removeClass('blocs_click')
	  .click(function(){
		jQuery(this).blocs_toggle()
		.next().toggleClass('blocs_invisible');
		// annulation du clic
		return false;
		});

	// sinon...
	jQuery('h4.blocs_click', this).not('.cs_done').addClass('cs_done')
	  .click( function(){
//		blocs_replie_tout(this);
		jQuery(this).blocs_toggle();
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

// une fonction et une variable pour reperer une pagination
function blocs_get_pagination(url) {
	tab=url.match(/#pagination([0-9]+)/);
	if (tab==null) return false;
	return tab[1];
}
var blocs_pagination = blocs_get_pagination(window.location.hash);

/*
// Si un bloc contient une pagination inseree dans un bloc,
// code JS a inserer dans votre squelette APRES les appels du Couteau Suisse
jQuery(document).ready(function() {
	if(blocs_pagination!==false) {
		jQuery('div.cs_bloc' + blocs_pagination + ' h4.blocs_titre').eq(0).click();
		window.location.hash = '#pagination' + blocs_pagination;
	}
});
*/
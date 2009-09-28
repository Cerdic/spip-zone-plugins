// compatibilité SPIP 1.9.2 : fonction $.hasClass indisponible sous jQuery 1.1.1
if(typeof jQuery.fn.hasClass=="undefined")
	jQuery.fn.hasClass = function( selector ) {
		return this.is( "." + selector );
	};

// Gestion du slide jQuery
jQuery.fn.blocs_toggle_slide = function( selector ) {
	return this.toggleClass('blocs_slide')
		.is(".blocs_slide")?this.slideUp(blocs_slide):this.slideDown(blocs_slide);
};

// fonction de de/re-pliement
jQuery.fn.blocs_toggle = function() {
	if (!this.length) return this;
	// applique-t-on la fonction sur cs_blocs ou sur blocs_titre ?
	var cible = this.hasClass('cs_blocs')? this.children('.blocs_titre').eq(0) : this;
	// on replie/deplie la cible...
	var dest = cible.toggleClass('blocs_replie').next();
	if(blocs_slide==='aucun') {
		dest.toggleClass('blocs_invisible');
		// est-on sur un resume ?
		if (dest.is('div.blocs_resume')) dest.next().toggleClass('blocs_invisible');
	} else {
		dest.blocs_toggle_slide();
		// est-on sur un resume ?
		if (dest.is('div.blocs_resume')) dest.next().blocs_toggle_slide();
	}
	// est-on sur un bloc ajax ?
	var url = cible.children().attr("href");
	if(url != 'javascript:;') {
		// une fois le bloc ajax en place, plus besoin de le recharger ensuite
		cible.children().attr("href", 'javascript:;');
		// ici, on charge !
		cible.parent().children(".blocs_destination")
		//.animeajax()
		.load(url);
	}
	return this;
};

// replie tout sauf le bloc appelant et sa lignee parentale
jQuery.fn.blocs_replie_tout = function() {
	if(blocs_replier_tout) {
		// applique-t-on la fonction sur cs_blocs ou sur blocs_titre ?
		var cible = this.hasClass('cs_blocs')? this : this.parents('div.cs_blocs');
		// lignee du bloc
		var lignee = cible.children('.blocs_titre');
		jQuery('.blocs_titre').not('.blocs_replie').not(lignee).blocs_toggle();
	}
	return this;
}

// une variable globale stockant le(s) bloc(s) a deplier si un clic ajax a eu lieu
var blocs_clic_ajax = null;

// compatibilite Ajax : ajouter "this" a "jQuery" pour mieux localiser les actions 
// et tagger avec cs_done pour eviter de binder plrs fois le meme bloc
function blocs_init() {
	// clic sur un titre de bloc
	jQuery('.blocs_titre', this).not('.cs_done').addClass('cs_done')
	  .click( function(){
		jQuery(this).blocs_replie_tout().blocs_toggle();
		// annulation du clic
		return false;
	   });
	// pour un lien 'replier_bloc' present dans le bloc
	jQuery('.blocs_destination a.replier_bloc', this).not('.cs_done').addClass('cs_done')
	 .click( function(){
		jQuery(this).parents('.cs_blocs:first').blocs_replie_tout().blocs_toggle();
		// annulation du clic
		return false;
	   });

/*
// LA SUITE DE CE CODE NE FONCTIONNE POUR L'INSTANT QUE SUR LE PREMIER CLIC, JE NE SAIS PAS ENCORE PKOI...
	// stockage du bloc (numerote !) a reouvrir dans le cas d'un clic ajax sur une 
	// pagination SPIP contenue a l'interieur
	jQuery(".ajaxbloc .pagination a.noajax", this).not('.cs_done').addClass('cs_done')
	  .click( function(){
		var parent = jQuery(this).parents('.cs_blocs');
		if(!parent.length) return true;
		var numero = /cs_bloc\d+/.exec(parent[0].className);
		if(numero!==null) blocs_clic_ajax = numero[0];
		return true;
	   });
	// rouvre le nouveau bloc ajax si un clic a eu lieu a l'interieur de l'ancien
	if(blocs_clic_ajax!==null) {
		jQuery('.'+blocs_clic_ajax, this).blocs_toggle();
		blocs_clic_ajax = null
	}
*/
}

// un JS actif replie les blocs invisibles
document.write('<style type="text/css">div.blocs_invisible{display:none;}</style>');

// Sauve l'etat des blocs numerotes dans un cookie si on quitte la page
function cs_blocs_cookie() {
	if(typeof jQuery.cookie!='function') return;
	var blocs_cookie_name = 'blocs' + window.location.pathname + window.location.search
	blocs_cookie_name = blocs_cookie_name.replace(/[ ;,=]/,'_');
	var deplies = jQuery.cookie(blocs_cookie_name);
	jQuery.cookie(blocs_cookie_name, null);
	if(deplies)
		jQuery(deplies).blocs_replie_tout().blocs_toggle();
	jQuery(window).bind('unload', function() {
		jQuery.cookie(blocs_cookie_name, blocs_deplies());
	});
}

// renvoie la liste des selecteurs de blocs ouverts
function blocs_deplies() {
	var deplies = '';
	jQuery('.cs_blocs').each(function() {
		var numero = /cs_bloc\d+/.exec(this.className);
		if(numero==null) return;
		replie = jQuery(this).children('.blocs_titre').eq(0).hasClass('blocs_replie');
		if(!replie) deplies += (deplies.length?', ':'') + 'div.' + numero[0];
	});
	return deplies.length?deplies:null;
}

// une fonction et une variable pour reperer une pagination
function blocs_get_pagination(url) {
	tab=url.match(/#pagination([0-9]+)/);
	if (tab==null) return false;
	return tab[1];
}

var blocs_pagination = blocs_get_pagination(window.location.hash);

/*
// Si un bloc contient une pagination inseree dans un bloc,
// code JS a inserer dans le header de votre squelette APRES les appels du Couteau Suisse :
jQuery(document).ready(function() {
	if(blocs_pagination!==false) {
		jQuery('div.cs_bloc' + blocs_pagination + ' .blocs_titre').eq(0).click();
		window.location.hash = '#pagination' + blocs_pagination;
	}
});
*/

/*
//	Pour un bloc dépliable du genre :
//	<BOUCLE_art(ARTICLES)>
//		#BLOC_TITRE
//		#TITRE
//		#BLOC_RESUME
//		#INTRODUCTION
//		#BLOC_DEBUT
//		#TEXTE
//		#BLOC_FIN
//	</BOUCLE_art>
//	le clic sur un point de suite cliquable de la balise #INTRODUCTION produit l'ouverture du bloc.
//	code JS a inserer dans le header de votre squelette APRES les appels du Couteau Suisse :
jQuery(document).ready(function(){
	jQuery('.blocs_resume>a.pts_suite')
	  .click( function(){
		jQuery(this).parents('.cs_blocs:first').children('.blocs_titre')
			.blocs_replie_tout().blocs_toggle();
		// annulation du clic
		return false;
		});
});
*/
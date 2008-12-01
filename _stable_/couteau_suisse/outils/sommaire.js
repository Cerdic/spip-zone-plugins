// compatibilite Ajax : ajouter "this" a "jQuery" pour mieux localiser les actions 
// et tagger avec cs_done pour eviter de binder plrs fois le meme bloc
function cs_sommaire_init() {
	jQuery('div.cs_sommaire_titre_avec_fond, div.cs_sommaire_titre_sans_fond', this)
		.not('.cs_done').addClass('cs_done')
		.click( function(){
			jQuery(this).toggleClass('cs_sommaire_replie')
				.next().toggleClass('cs_sommaire_invisible')
			// annulation du clic
			return false;
		});
}

// Sauver l'etat du sommaire dans un cookie si on quitte la page et le remettre quand on revient
function cs_sommaire_cookie() {
	var replie = jQuery.cookie('cs_commaire');
	var sel = 'div.cs_sommaire_titre_avec_fond, div.cs_sommaire_titre_sans_fond';
	if (replie)
		jQuery(sel).eq(0).addClass('cs_sommaire_replie')
			.next().toggleClass('cs_sommaire_invisible');
	jQuery(window).bind('unload', function() {
		jQuery.cookie('cs_commaire',
			Number(jQuery(sel).eq(0).hasClass('cs_sommaire_replie'))
		);
	});
}

jQuery(function(){
	saisies_fieldset_pliable();
	onAjaxLoad(saisies_fieldset_pliable);
});

function saisies_fieldset_pliable(){
	// On cherche les groupes de champs pliables
	jQuery('.fieldset.pliable')
		.each(function(){
			var fieldset = jQuery(this);
			var groupe = jQuery(this).find('> fieldset > .editer-groupe');
			var legend = jQuery(this).find('> fieldset > .legend');

			// S'il est déjà plié on cache le contenu
			if (fieldset.is('.plie'))
				groupe.hide();

			// Ensuite on ajoute une action sur le titre
			legend
				.unbind('click')
				.click(
					function(){
						fieldset.toggleClass('plie');
						if (groupe.is(':hidden'))
							groupe.show();
						else
							groupe.hide();
					}
				);
		});
};

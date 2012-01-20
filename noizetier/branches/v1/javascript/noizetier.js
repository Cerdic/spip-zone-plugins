$(function(){
	noizetier_parametres_pliable();
	onAjaxLoad(noizetier_parametres_pliable);
});

function noizetier_parametres_pliable(){
	// On cherche les blocs pliables
	$('li.noisette')
		.each(function(){
			var li = $(this);
			var infos = $(this).find('div.infos');
			var titre = $(this).find('div.titre');
			
			// S'il est déjà plié on cache le contenu
			if (li.is('.plie'))
				infos.hide();
			
			// Ensuite on ajoute une action sur le titre
			titre
				.unbind('click')
				.click(
					function(){
						li.toggleClass('plie');
						if (infos.is(':hidden'))
							infos.show();
						else
							infos.hide();
					}
				);
		});
};

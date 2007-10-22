<?php


if (!defined("_ECRIRE_INC_VERSION")) return;

// Ajoute le bouton d'amin aux webmestres
function cfg_ajouter_onglets($flux) {
	if ($flux['args'] == 'configuration'
	AND autoriser('configurer')) {
		// on voit le bouton dans la barre "configurer"
		$flux['data']['cfg'] =
			new Bouton(
			_DIR_PLUGIN_CFG."cfg-22.png",  // icone
			_L('CFG'),	// titre
			generer_url_ecrire('cfg'),
			NULL,
			'cfg'
			);
	}
	return $flux;
}


// ajoute le css de CFG a l'espace prive
function cfg_header_prive($texte) {
	global $auteur_session, $spip_display, $spip_lang;
	if (_request('exec') == 'cfg'){
		$texte.= "<link rel='stylesheet' type='text/css' href='" . _DIR_PLUGIN_CFG . "css/cfg.css' />"
			. "<script type='text/javascript'>
			$(document).ready(function(){
				jQuery('.cfg_arbo ul').hide();
				jQuery('.cfg_arbo h5')
				.prepend('<b>[+] </b>')
				.toggle(
				  function () {
					$(this).children('b').text('[-] ');
					$(this).next('ul').show();
				  },
				  function () {
					$(this).children('b').text('[+] ');
					$(this).next('ul').hide();
				  })
			});
			</script>
			" . "\n";
	}
	return $texte;
}
?>

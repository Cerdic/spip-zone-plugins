<?php


// espace prive: css & js
function partageur_header_prive($flux){
	// attention ce fichier est compresse et passe en cache
  $css = generer_url_public('partageur.css');
	$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' media='all' />\n";  
	return $flux;
}

	
// espace prive: bouton sur rubrique
function partageur_affiche_enfants($flux) {
		include_spip('inc/autoriser');
		global $spip_lang_right;
		if (autoriser('voir', 'partageur')) {
			$id_rubrique = $flux['args']['id_rubrique'];
			// bouton partage ds cette rubrique
			if ($id_rubrique) {
			  $flux['data'].= icone_inline(_T('partageur:creer_partage'), generer_url_ecrire("partageur_add", "id_rubrique=$id_rubrique"), _DIR_PLUGIN_PARTAGEUR.'/img/partageur-24.png',"creer.gif", $spip_lang_right);
				$flux['data'].= '<br class="nettoyeur" />';		
			}
		}
		return $flux;
}

?>
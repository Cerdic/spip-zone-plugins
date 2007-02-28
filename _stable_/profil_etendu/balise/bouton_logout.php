<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');

function balise_BOUTON_LOGOUT($p) {
  return calculer_balise_dynamique($p,'BOUTON_LOGOUT',array());
}

function balise_BOUTON_LOGOUT_stat($args, $filtres) {

	return (array('test'));
}

function balise_BOUTON_LOGOUT_dyn($test) {
	if (!$GLOBALS["auteur_session"]) 
		return '';
	else {
		$url="spip.php?action=logout&url=".urlencode(self()."&var_mode=calcul");
		return array('formulaires/formulaire_bouton', 0,
				array(
						'javascript' => "",
						'image' => 'auteur-24.gif',
						'action' => 'supprimer',
						'action_alt' => "Logout",
						'url' => $url
				));
	}
}

?>
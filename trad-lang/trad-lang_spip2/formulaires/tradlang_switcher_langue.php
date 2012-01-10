<?php 

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * chargement des valeurs par defaut des champs du #FORMULAIRE_RECHERCHE
 * on peut lui passer l'url de destination en premier argument
 *
 * @param string $lien
 * @return array
 */
function formulaires_tradlang_switcher_langue_charger_dist($lien = '',$langue_modules='',$titre=''){
	if(!$langue_modules)
		$langue_modules = _request('langue_modules');

	return 
		array(
			'action' => ($lien ? $lien : parametre_url(self(),'langue_modules','')), # action specifique, ne passe pas par Verifier, ni Traiter
			'langue_modules' => $langue_modules,
			'titre' => $titre
		);
}

?>
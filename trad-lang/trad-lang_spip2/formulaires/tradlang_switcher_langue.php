<?php 

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * chargement des valeurs par defaut du select de #FORMULAIRE_TRADLANG_SWITCHER_LANGUE
 *
 * @param string $lien
 * 		Le lien de la page de retour, par défaut ce sera sur la page en cours
 * @param string/array $langue_modules
 * 		Un string (si multiple = false) ou un array des langues par défaut sélectionnées du formulaires
 * @param string $titre
 * 		Le titre du formulaire (si présent, créera un fieldset + legend)
 * @param string $name
 * 		Le name du select (qui sera ensuite envoyé dans l'URL)
 * @param bool $multiple
 * 		Défini si le select est multiple ou pas
 * @return array
 * 		Les valeurs chargées dans le formulaire
 */
function formulaires_tradlang_switcher_langue_charger_dist($lien = '',$langue_modules='',$titre='',$name='langue_modules',$multiple=false){
	if(!$langue_modules)
		$langue_modules = _request('langue_modules');
	
	if($multiple && !is_array($langue_modules))
		$langue_modules = array();
	else if(!$multiple && is_array($langue_modules))
		$langue_modules = '';
	
	return 
		array(
			'action' => ($lien ? $lien : parametre_url(self(),'langue_modules','')), # action specifique, ne passe pas par Verifier, ni Traiter
			'titre' => $titre,
			'name' => $name,
			$name => $langue_modules,
			'multiple' => $multiple
		);
}

?>
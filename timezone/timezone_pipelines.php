<?php
/**
 * Utilisations de pipelines pour Timezone
 *
 * @plugin	 Timezone
 * @copyright  2014
 * @author	 kent1
 * @licence	GNU/GPL v3
 * @package	SPIP\Timezone\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion dans le pipeline formulaire_fond (SPIP)
 * 
 * Ajouter le champ timezone dans le formulaire de configuration d'identité du site
 * 
 * @param array $flux
 * @return array $flux
 */
function timezone_formulaire_fond($flux){
	if($flux['args']['form'] == 'configurer_identite'){
		if(!function_exists('lire_config'))
			include_spip('inc/config');
		$flux['args']['contexte']['timezone'] = lire_config('timezone','');
		$contenu = recuperer_fond('formulaires/timezone_inclure',$flux['args']['contexte']);
		$flux['data'] = preg_replace(",(<li [^>]*class=[\"']editer editer_descriptif_site.*<\/li>),Uims","\\1".$contenu,$flux['data'],1);
	}
	return $flux;
}


/**
 * Insertion dans le pipeline formulaires_traiter (SPIP)
 * 
 * Ajouter la meta timezone suite au formulaire de configuration d'identité du site
 * 
 * @param array $flux
 * @return array $flux
 */
function timezone_formulaire_traiter($flux){
	if($flux['args']['form'] == 'configurer_identite'){
		if(_request('timezone')){
			include_spip('inc/meta');
			ecrire_meta('timezone',_request('timezone'));
		}
	}
	return $flux;
}

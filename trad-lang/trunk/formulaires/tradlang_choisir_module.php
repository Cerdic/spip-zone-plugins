<?php
/**
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 * 
 * Formulaire de choix de module à traduire
 * 
 * @package SPIP\Tradlang\Formulaires
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Chargement du formulaire
 * 
 * @param int $id_tradlang_module
 * 		L'identifiant numérique du module présélectionné
 * @param string $lang_orig
 * 		La langue source présélectionnée (sinon ce sera la langue mère du module)
 * @param string $lang_cible
 * 		La langue cible présélectionnée, sinon ce sera :
 * 		- la langue de l'interface
 * 		- la première langue préférée
 * 		- La première langue du select
 * @param string $lang_crea
 * @return array $valeurs
 * 		Le tableau des valeurs chargées au formulaire
 */
function formulaires_tradlang_choisir_module_charger($id_tradlang_module="",$lang_orig="fr",$lang_cible="",$lang_crea=""){
	$id_tradlang_module = _request('id_tradlang_module') ? _request('id_tradlang_module') : $id_tradlang_module;
	
	include_spip('inc/autoriser');
	if(autoriser('modifier','tradlang')){
		if(!intval($id_tradlang_module))
			$id_tradlang_module = sql_getfetsel('id_tradlang_module','spip_tradlang_modules','','','priorite,nom_mod');
		if(!$infos_module = sql_fetsel('*','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module))){
			$valeurs['id_tradlang_module'] = $id_tradlang_module = $module_defaut = sql_getfetsel('id_tradlang_module','spip_tradlang_modules','module NOT LIKE "attic%" AND module != "contrib"',array('priorite','nom_mod'),'','0,1');
			/**
			 * Si aucun module dans la base
			 */
			if(!$module_defaut){
				if(autoriser('configurer','tradlang')){
					$valeurs['message_erreur'] = _T('tradlang:erreur_aucun_module');
					$valeurs['editable'] = false;
					return $valeurs;
				}
				return false;
			}
		}
		else
			$valeurs['id_tradlang_module'] = $id_tradlang_module;
		
		include_spip('inc/lang_liste');
		include_spip('inc/config');
		
		if(!$lang_cible)
			$lang_cible = $GLOBALS['spip_lang'];

		$valeurs = array('id_tradlang_module' => $id_tradlang_module,'lang_orig' => $lang_orig,'lang_cible'=>$lang_cible,'lang_crea'=> $lang_crea);
		foreach($valeurs as $key => $val){
			if(_request($key))
				$valeurs[$key] = _request($key);
		}
		
		/**
		 * Si la langue d'origine passée au formulaire n'est pas la langue mère 
		 *
		 * On vérifie que la langue d'origine choisie dans l'url est correctement traduite 
		 * sinon on passe à la langue mère
		 */
		if($lang_orig != $infos_module['lang_mere']){
			$compte_total_mere = sql_getfetsel('COUNT(*)','spip_tradlangs','id_tradlang_module='.intval($valeurs['id_tradlang_module']).' AND statut="OK" AND lang='.sql_quote($infos_module['lang_mere']));
			$compte_total_orig = sql_getfetsel('COUNT(*)','spip_tradlangs','id_tradlang_module='.intval($valeurs['id_tradlang_module']).' AND statut="OK" AND lang='.sql_quote($lang_orig));
			if($compte_total_mere != $compte_total_orig)
				$valeurs['lang_orig'] = $infos_module['lang_mere'];
		}

		$valeurs['lang_mere'] = $infos_module['lang_mere'];
		
		$langues_possibles = $GLOBALS['codes_langues'];
		
		ksort($langues_possibles);
		$langues_modules = sql_select('DISTINCT lang','spip_tradlangs','module='.sql_quote($infos_module['module']));
		while($langue = sql_fetch($langues_modules)){
			$langues_presentes[$langue['lang']] = traduire_nom_langue($langue['lang']);
		}
		if(is_array($langues_presentes)){
			ksort($langues_presentes);
			$langues_possibles = array_diff($langues_possibles,$langues_presentes);
		}
		
		ksort($langues_possibles);
		
		$config = lire_config('tradlang');
		if (is_array($config) && is_array($config['langues_autorisees'])){
			foreach($config['langues_autorisees'] as $key=>$val){
				$langues_conf[$val] = traduire_nom_langue($val);
			}
			$langues_possibles = array_intersect_key($langues_possibles,$langues_conf);	
		}
			
		$valeurs['_langues_possibles'] = $langues_possibles;
		if(!$lang_orig)
			$valeurs['lang_orig'] = $valeurs['lang_mere'];
	}else{
		$valeurs['editable'] = false;
		$valeurs['message_erreur'] = _T('tradlang:erreur_autorisation_modifier_modules');
	}
	return $valeurs;
}

/**
 * Vérification du formulaire
 * Si pas de langue cible sélectionnées ou si la langue cible est 
 * identique à la langue d'origine sélectionnée, on retourne une erreur 
 * 
 * @param int $id_tradlang_module
 * 		L'identifiant numérique du module présélectionné
 * @param string $lang_orig
 * 		La langue source présélectionnée (sinon ce sera la langue mère du module)
 * @param string $lang_cible
 * 		La langue cible présélectionnée, sinon ce sera :
 * 		- la langue de l'interface
 * 		- la première langue préférée
 * 		- La première langue du select
 * @param string $lang_crea
 * @return array $erreurs 
 * 		Le tableau des erreurs
 */
function formulaires_tradlang_choisir_module_verifier($id_tradlang_module="",$lang_orig="",$lang_cible="",$lang_crea=""){
	$erreur = array();
	if(!_request('lang_cible') && !_request('creer_lang_cible'))
		$erreur['lang_cible'] = _T('tradlang:erreur_pas_langue_cible');
	else if(_request('lang_cible') == _request('lang_orig'))
		$erreur['lang_cible'] = _T('tradlang:erreur_langues_differentes');
	return $erreur;
}

/**
 * Traitement du formulaire
 * Redirige vers la page de traduction après avoir créé la nouvelle version dans 
 * le cas où on crée une nouvelle langue du module sélectionné 
 * 
 * @param int $id_tradlang_module
 * 		L'identifiant numérique du module présélectionné
 * @param string $lang_orig
 * 		La langue source présélectionnée (sinon ce sera la langue mère du module)
 * @param string $lang_cible
 * 		La langue cible présélectionnée, sinon ce sera :
 * 		- la langue de l'interface
 * 		- la première langue préférée
 * 		- La première langue du select
 * @param string $lang_crea
 * @return array $res 
 * 		Le tableau habituel des cvt avec redirect pour la redirection
 */
function formulaires_tradlang_choisir_module_traiter($id_tradlang_module="",$lang_orig="",$lang_cible="",$lang_crea=""){
	$id_tradlang_module = _request('id_tradlang_module');
	$lang_orig = _request('lang_orig');
	$lang_cible = _request('lang_cible');
	$lang_crea = _request('creer_lang_cible');
	if($traduire = _request('traduire')){
		include_spip('inc/autoriser');
		$res['message_ok'] = _T('tradlang:message_passage_trad');
		include_spip('inc/autoriser');
		if($lang_crea && autoriser('modifier','tradlang')){
			// Import de la langue mere
			$infos_module = sql_fetsel('*','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module));
			$ajouter_code_langue = charger_fonction('tradlang_ajouter_code_langue','inc');
			$ajouter_code_langue($infos_module,$lang_crea);
			$lang_cible = $lang_crea;
			$res['message_ok'] = _T('tradlang:message_passage_trad_creation_lang',array('lang'=>$lang_crea));
		}
		$res['redirect'] = parametre_url(parametre_url(parametre_url(generer_url_entite($id_tradlang_module,'tradlang_module'),"lang_orig",$lang_orig),"lang_cible",$lang_cible),'lang_crea',$lang_crea);;
	}else
		$res['editable'] = true;
	return $res;
}
?>
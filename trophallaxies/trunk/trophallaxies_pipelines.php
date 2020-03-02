<?php
/**
 * Utilisations de pipelines par Trophallaxies
 *
 * @plugin     Trophallaxies
 * @copyright  2020
 * @author     Vincent CALLIES
 * @licence    GNU/GPL
 * @package    SPIP\Trophallaxies\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insertion dans le pipeline affiche_gauche (SPIP)
 *
 * On affiche une aide liée à l'objet ou l'exec
 * si les variables de langue pertinentes sont existantes
 * et selon les préférences de l'auteur et de la configuration du plugin.
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */

function trophallaxies_affiche_gauche($flux){
	include_spip('inc/presentation');
	// les panneaux d'aide peuvent être affichés 
	// si la variable de langue existe dans le fichier trophallaxie
	if ($flux['args']['exec'] AND (_T("trophallaxie:".$flux['args']['exec']."_aide_affiche_gauche",array(),array('force'=>'')))) {
	 	$panneau = debut_cadre_relief('bulle-24.png',true,'', 
	 			_T("trophallaxie:".$flux['args']['exec']."_titre_affiche_gauche")) .
				_T("trophallaxie:".$flux['args']['exec']."_aide_affiche_gauche") .
				fin_cadre_relief(true);
		// on affiche systématiquement le panneau si la configuration est forcée
		if (lire_config('trophallaxies/forcee')=='on') {
			$flux['data'] .=$panneau;
		} else { 
		// sinon on s'enquière aimablement de la préférence de l'auteur
			if (($GLOBALS['visiteur_session']['prefs']['aides_trophallaxies']=='') OR ($GLOBALS['visiteur_session']['prefs']['aides_trophallaxies']=='navigation_avec_aides')) {
				$flux['data'] .=$panneau;
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline recuperer_fond (SPIP)
 *
 * On propose dans le formulaire de configuration de l'auteur
 * une préférence permettant le choix de l'affichage ou non
 * d'une aide liée à l'objet ou l'exec.
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */

function trophallaxies_formulaire_fond($flux) {
	if ($flux['args']['form'] == 'configurer_preferences') {
		$ajout = recuperer_fond("prive/squelettes/inclure/ajout_configurer_preferences", $flux['args']['contexte']);
		// On ajoute l'expression du souhait après le Menu de navigation
		// donc avant : <div class="editer editer_display">
		$masque = '<div class="editer editer_display">';
		$flux['data'] = str_replace ($masque, $ajout.$masque, $flux['data']);
	}

	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_charger (SPIP)
 *
 * On charge dans le formulaire de configuration de l'auteur
 * la préférence permettant le choix de l'affichage ou non
 * d'une aide liée à l'objet ou l'exec.
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */

function trophallaxies_formulaire_charger ($flux) {
	if ($flux['args']['form'] == 'configurer_preferences'){
		$flux['data']['aides_trophallaxies'] = isset($GLOBALS['visiteur_session']['prefs']['aides_trophallaxies'])?$GLOBALS['visiteur_session']['prefs']['aides_trophallaxies']:'navigation_avec_aides';
	}
    return $flux;
}
	
/**
 * Insertion dans le pipeline formulaire_verifier (SPIP)
 *
 * On ajoute dans le formulaire de configuration de l'auteur
 * la préférence relative au choix de l'affichage ou non
 * d'une aide liée à l'objet ou l'exec
 * avant que le formulaire ne soit traité.
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */

function trophallaxies_formulaire_verifier ($flux) {
	if ($flux['args']['form'] == 'configurer_preferences'){
		if ($aides_trophallaxies = _request('aides_trophallaxies')) {
			$GLOBALS['visiteur_session']['prefs']['aides_trophallaxies'] = ($aides_trophallaxies=='navigation_avec_aides') ? $aides_trophallaxies : 'navigation_sans_aide';
	    }
	}
    return $flux;
}



<?php
/**
 * Utilisations de pipelines par Aide par Objet
 *
 * @plugin     aide_par_objet
 * @copyright  2020
 * @author     Vincent CALLIES
 * @licence    GNU/GPL
 * @package    SPIP\aide_par_objet\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insertion dans le pipeline affiche_gauche (SPIP)
 *
 * On affiche une aide liée à l'objet ou l'exec
 * si les chaînes de langue pertinentes sont existantes
 * et selon les préférences de l'auteur et de la configuration du plugin.
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */

function aide_par_objet_affiche_gauche($flux){
	include_spip('inc/presentation');
	// les panneaux d'aide peuvent être affichés 
	// si la chaîne de langue existe dans le fichier aide_par_objet
	if ($flux['args']['exec'] AND ($titre = _T("aide_par_objet:".$flux['args']['exec']."_titre",array(),array('force'=>'')))) {
		// le titre révèle que l'on veut sa propre mise en forme (par une div définie par le titre lui-même)
		if ($titre[0]=='#') {
			$titre[1]=='#' ? $div = "<div id='" . substr($titre, 1) . "'>" : $div = "<div class='" . substr($titre, 1) . "'>";
			$panneau = $div 
				. _T("aide_par_objet:" . $flux['args']['exec'] . "_texte")
				. "</div>";
		} else
		 	$panneau = debut_cadre_relief('bulle-24.png',true,'', $titre)
	 			. _T("aide_par_objet:" . $flux['args']['exec'] . "_texte")
	 			. fin_cadre_relief(true);
		// on affiche systématiquement le panneau si la configuration est forcée
		if (lire_config('aide_par_objet/forcee')=='on') {
			$flux['data'] .= $panneau;
		} else { 
		// sinon on s'enquière aimablement de la préférence de l'auteur
			if (($GLOBALS['visiteur_session']['prefs']['aide_par_objet'] == '') OR ($GLOBALS['visiteur_session']['prefs']['aide_par_objet'] == 'navigation_avec_aides')) {
				$flux['data'] .= $panneau;
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

function aide_par_objet_formulaire_fond($flux) {
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

function aide_par_objet_formulaire_charger ($flux) {
	if ($flux['args']['form'] == 'configurer_preferences'){
		$flux['data']['aide_par_objet'] = isset($GLOBALS['visiteur_session']['prefs']['aide_par_objet'])?$GLOBALS['visiteur_session']['prefs']['aide_par_objet']:'navigation_avec_aides';
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

function aide_par_objet_formulaire_verifier ($flux) {
	if ($flux['args']['form'] == 'configurer_preferences'){
		if ($aide_par_objet = _request('aide_par_objet')) {
			$GLOBALS['visiteur_session']['prefs']['aide_par_objet'] = ($aide_par_objet=='navigation_avec_aides') ? $aide_par_objet : 'navigation_sans_aide';
	    }
	}
    return $flux;
}



<?php
/**
 * Fonctions utiles au plugin Rang
 *
 * @plugin     Rang
 * @copyright  2016
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Rang\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/rang_api');

// puisque les mots-clés sont pris en compte, on va avoir besoin de ceci
include_spip('inc/mots');

/**
 * Surcharge de la balise `#RANG`
 * 
 * Appelle balise_RANG_dist(), mais renvoie une chaine vide si le rang est null ou zéro
 * 
 */
function balise_RANG($p) {
	$p = balise_RANG_dist($p);
	$p->code = "(intval($p->code) == 0 ? '' : $p->code)";
	return $p;
}

/**
 * Balise `#RANG_LISTE_OBJETS`
 * Retourne la listes des objets (nom au pluriel) cochés dans la configuration.
 * Permet d'utiliser #VAL{produits}|in_array{#RANG_LISTE_OBJETS}} dans les squelettes,
 * sans lever d'erreur si le plugin Rang n'est pas installé.
 * 
 * @balise
 *        
 * @param $p
 *
 * @return mixed
 */
function balise_RANG_LISTE_OBJETS($p) {
	// utiliser function_exists pour éviter une erreur quand on désactive Rang
	$p->code = "function_exists('rang_liste_objets')?rang_liste_objets():array()";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * Détecte si l'objet a été selectionné dans la configuration du plugin
 *
 * @deprecated dépréciée depuis la version 0.8, utiliser à la place la balise #RANG_LISTE_OBJETS
 *             exemple : #VAL{produits}|in_array{#RANG_LISTE_OBJETS}}
 *             
 * @param string $objet
 *     article, rubrique, etc.
 *
 * @return bool
 *
 **/
function rang_objet_dans_config($objet) {
	$table = table_objet_sql($objet);
	$liste = lire_config('rang/objets');
	return in_array($table, $liste);
}

//function balise_OBJET_RANG($p) {
//	$type_objet = interprete_argument_balise(1, $p);
//	$id_objet_parent = interprete_argument_balise(2, $p);
//	if (!$type_objet) {
//		$msg = _T('zbug_balise_sans_argument', array('balise' => ' OBJET_RANG'));
//		erreur_squelette($msg, $p);
//		$p->interdire_scripts = true;
//		return $p;
//	} else {
//		if($id_objet_parent){
//			$p->code = $id_objet_parent. ' && (in_array(' . $type_objet . ',rang_liste_objets()))';
//		} else {
//			$p->code = 'in_array(' . $type_objet . ',rang_liste_objets())';
//		}
//		$p->interdire_scripts = true;
//		return $p;
//	}
//}
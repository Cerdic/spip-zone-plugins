<?php
/**
 * Définit les autorisations du plugin Lim
 *
 * @plugin     Lim
 * @copyright  2015
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\Lim\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function lim_autoriser() {}

/**************************************************************/
/************* DESACTIVER DES LOGOS PAR CONTENUS *************/

/* Exceptions historiques */
function autoriser_auteur_iconifier($faire,$type,$id,$qui,$opt) {
	if (in_array(table_objet_sql($type),explode(',',lire_config('lim_logos'))))
		return false;
	else return (($id == $qui['id_auteur']) OR
			(($qui['statut'] == '0minirezo') AND !$qui['restreint']));
}

function autoriser_mot_iconifier($faire,$type,$id,$qui,$opt) {
	if (in_array(table_objet_sql($type),explode(',',lire_config('lim_logos'))))
		return false;
	return (($qui['statut'] == '0minirezo') AND !$qui['restreint']);
}

function autoriser_groupemots_iconifier($faire,$type,$id,$qui,$opt) {
	if (in_array(table_objet_sql($type),explode(',',lire_config('lim_logos'))))
		return false;
	else return (($qui['statut'] == '0minirezo') AND !$qui['restreint']);
}

function autoriser_rubrique_iconifier($faire,$type,$id,$qui,$opt) {
	if (in_array(table_objet_sql($type),explode(',',lire_config('lim_logos'))))
		return false;
	return autoriser('publierdans', 'rubrique', $id, $qui, $opt);
}


// iconifier ou non les objets
function autoriser_iconifier($faire, $type, $id, $qui, $opt) {
	// on gère d'abord une exception sur l'objet Sites référencés
    // on a toujours le droit d'ajouter un logo au site lui même.
    if (_request('exec') == 'configurer_identite' AND  $type =='site') {
        return true;
    }
	if (in_array(table_objet_sql($type),explode(',',lire_config('lim_logos'))))
		return false;
	// par defaut, on a le droit d'iconifier si on a le droit de modifier
	else return autoriser('modifier', $type, $id, $qui, $opt);
}

/**********************************************************/
/************* RESTRICTION DANS LES RUBRIQUES *************/
/**
 * gérer création et modification (en fait creerobjetrdans)
 * @pipeline autoriser 
 */

if (!function_exists('autoriser_rubrique_creerrubriquedans')) {
	function autoriser_rubrique_creerrubriquedans($faire, $type, $id, $qui, $opt) {
		$quelles_rubriques = lire_config('lim_rubriques/rubrique');
		is_null($quelles_rubriques) ? $lim_rub = true : $lim_rub = !in_array($id,$quelles_rubriques);
		return
			$lim_rub
			AND autoriser_rubrique_creerrubriquedans_dist($faire, $type, $id, $qui, $opt);
	}
}

if (!function_exists('autoriser_rubrique_creerarticledans')) {
	function autoriser_rubrique_creerarticledans($faire, $type, $id, $qui, $opt) {
		$quelles_rubriques = lire_config('lim_rubriques/article');
		is_null($quelles_rubriques) ? $lim_rub = true : $lim_rub = !in_array($id,$quelles_rubriques);
		
		return
			$lim_rub
			AND autoriser_rubrique_creerarticledans_dist($faire, $type, $id, $qui, $opt);
	}
}

if (!function_exists('autoriser_rubrique_creerbrevedans')) {
	function autoriser_rubrique_creerbrevedans($faire, $type, $id, $qui, $opt) {
		$r = sql_fetsel("id_parent", "spip_rubriques", "id_rubrique=".intval($id));
		$quelles_rubriques = lire_config('lim_rubriques/breve');
		is_null($quelles_rubriques) ? $lim_rub = true : $lim_rub = !in_array($id,$quelles_rubriques);

		return
			$lim_rub
			AND autoriser_rubrique_creerbrevedans_dist($faire, $type, $id, $qui, $opt);
	}
}

if (!function_exists('autoriser_rubrique_creersitedans')) {
	function autoriser_rubrique_creersitedans($faire, $type, $id, $qui, $opt) {
		$quelles_rubriques = lire_config('lim_rubriques/site');
		is_null($quelles_rubriques) ? $lim_rub = true : $lim_rub = !in_array($id,$quelles_rubriques);

		// exception : la page exec=sites accessible depuis le menu "Edition -> Sites référencés"
		if (_request('exec') == 'sites') $lim_rub = true;
		
		return
			$lim_rub
			AND autoriser_rubrique_creersitedans_dist($faire, $type, $id, $qui, $opt);
	}
}


if (!function_exists('autoriser_joindredocument')) {
	function autoriser_joindredocument($faire, $type, $id, $qui, $opt) {
		// Attention : ici il faut vérifier que le contexte est bien une rubrique
		if ($type == 'rubrique') {
			$quelles_rubriques = lire_config('lim_rubriques/document');
			is_null($quelles_rubriques) ? $lim_rub = true : $lim_rub = !in_array($id,$quelles_rubriques);
		}
		else {
			$lim_rub = true;
		}
		return
			$lim_rub
			AND autoriser_joindredocument_dist($faire, $type, $id, $qui, $opt);
	}
}

// if (!function_exists('autoriser_rubrique_publierdans')) {
// 	function autoriser_rubrique_publierdans($faire, $type, $id, $qui, $opt) {

// 		// Dans LIM l'appel à cette autorisation signifie que forcément $opt est renseigné
// 		if (is_array($opt) AND array_key_exists('lim_except_rub',$opt) AND array_key_exists('type',$opt)) {
// 			$type = $opt['type'];
// 			$quelles_rubriques = lire_config("lim_rubriques/$type");
// 			if (!is_null($quelles_rubriques)) {
// 				$rubrique_except = array(0 => $opt['lim_except_rub']);
// 				$quelles_rubriques = array_diff($quelles_rubriques, $opt);
// 				$lim_rub = !in_array($id,$quelles_rubriques);
// 			}
// 			// cas possible : un objet peut avoir été sélectionné dans ?exec=configurer_lim_rubriques, mais aucune restriction activée
// 			else $lim_rub = true;
// 		}
// 		// ici gestion hors CVT
// 		else $lim_rub = true;

// 		return
// 			$lim_rub
// 			AND autoriser_rubrique_publierdans_dist($faire, $type, $id, $qui, $opt);
// 	}
// }

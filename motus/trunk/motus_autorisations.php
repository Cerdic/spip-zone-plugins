<?php

function motus_autoriser(){}


/**
 * Autorisation pour verifier le droit d'associer des mots
 * a un objet
 *
 * Si l'affichage est autorise par la fonction mere,
 * On teste que les restrictions eventuelles sur le groupe
 * ne viennent pas faire qu'il n'y aurait aucun groupe d'affiche ensuite
 *
 * @return bool
 */
function autoriser_associermots($faire,$quoi,$id,$qui,$opts) {
	if (!autoriser_associermots_dist($faire,$quoi,$id,$qui,$opts)) {
		return false;
	}

	// il existe des groupes pour l'objet en question.
	// on ne s'occupe que du cas ou nous ne connaissons pas de groupe precis d'association
	if (isset($opts['groupe_champs']) OR isset($opts['id_groupe'])){
		return true;
	}
	
	// chercher si un groupe est autorise pour mon statut
	// et pour la table demandee
	$table = addslashes(table_objet($quoi));
	$droit = substr($qui['statut'],1);
	$restrictions = sql_allfetsel('rubriques_on', 'spip_groupes_mots',"tables_liees REGEXP '(^|,)$table($|,)' AND ".addslashes($droit)."='oui'");
	$restrictions = array_map('array_shift', $restrictions);
	
	// pour chaque resultat, on teste si on peut l'associer ou non...
	// deja, un des groupes est sans restriction : c'est OK !
	foreach ($restrictions as $r) {
		if (!$r) return true;
	}
	
	// puis via l'autorisation...
	foreach ($restrictions as $r) {
		if (motus_autoriser_groupe_si_selection_rubrique($r, $quoi, $id, $qui))
			return true;
	}

	// tout est interdit d'affichage !
	return false;
}


/**
 * Autorisation pour verifier le droit d'afficher le selecteur de mots
 * pour un groupe de mot donne, dans un objet / id_objet donne
 *
 * @return bool
 */
function autoriser_groupemots_afficherselecteurmots($faire,$quoi,$id,$qui,$opts){

	static $groupes = array();
	
	$objet = $opts['objet'];
	$id_objet = $opts['id_objet'];

	if (!$objet) return true;

	// premier tri
	if (!autoriser_associermots_dist($faire,$objet,$id_objet,$qui,$opts))
		return false;

	if (!isset($groupes[$id])) {
		$groupes[$id] = sql_getfetsel('rubriques_on', 'spip_groupes_mots', 'id_groupe='.$id);
	}

	// pas de restriction, on s'en va
	if (!$groupes[$id]) {
		return true;
	}

	// si restriction a une rubrique...
	return motus_autoriser_groupe_si_selection_rubrique($groupes[$id], $objet, $id_objet, $qui);
	
}


/**
 * Retourne vrai si une selection de rubrique s'applique a cet objet
 * autrement dit, si l'objet appartient a une des rubriques donnees
 *  
 * @param string $restriction Liste des restrictions issues d'une selection avec le selecteur generique (rubrique|3)
 * @param string $objet Objet sur lequel on teste l'appartenance a une des rubriques (article)
 * @param int $id_objet Identifiant de l'objet.
 * @param int $qui De qui teste t'on l'autorisation.
 * @return bool
**/
function motus_autoriser_groupe_si_selection_rubrique($restrictions, $objet, $id_objet, $qui) {
	// si restriction a une rubrique...
	include_spip('formulaires/selecteur/generique_fonctions');
	if ($rubs = picker_selected($restrictions, 'rubrique')) {
		
		// trouver la rubrique de l'objet en question
		if ($objet != 'rubrique') {
			$table = table_objet_sql($objet);
			$desc = sql_showtable($table);	
			if ($desc and isset($desc['field']['id_rubrique'])) {
				$id_rub = sql_getfetsel('id_rubrique', $table, id_table_objet($table) . '=' . intval($id_objet));
			}
		} else {
			$id_rub = $id_objet;
		}
		$opts = array();
		$opts['rubriques_on'] = $rubs;
		return autoriser('dansrubrique', 'groupemots', $id_rub, $qui, $opts);
	}

	return false;
}




function autoriser_groupemots_dansrubrique_dist($faire,$quoi,$id,$qui,$opts){
	static $rubriques = -1;

	// init
	if ($rubriques === -1) $rubriques = array();

	if (!$rubs = $opts['rubriques_on']  // pas de liste de rubriques ?
	or !$id  // pas d'info de rubrique... on autorise par defaut...
	or in_array($id, $rubs)) // la rubrique est dedans
		return true;

	// la ca se complique...
	// si deja calcule... on le retourne.
	$hash = md5(implode('',$rubs) . '_' . $opts['id_groupe']);
	if (isset($rubriques[$id][$hash]))
		return $rubriques[$id][$hash];
	
	// remonter recursivement les rubriques...
	$id_parent = sql_getfetsel('id_parent','spip_rubriques', 'id_rubrique = '. sql_quote($id));

	// si racine... pas de chance
	if (!$id_parent) {
		$rubriques[$id][$hash] = false;
	} else {
		$rubriques[$id][$hash] = autoriser('dansrubrique','groupemots',$id_parent,$qui,$opts);
	}

	return $rubriques[$id][$hash];
}



?>

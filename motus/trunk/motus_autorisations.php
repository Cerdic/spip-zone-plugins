<?php

/**
 * Définition d'autorisations 
 *
 * Essentiellement des surcharges d'autorisations du plugin mots
 * 
 * @package Motus\Autorisations
**/

/** Fonction d'appel du pipeline **/
function motus_autoriser(){}

/**
 * Autorisation de voir le champs extra rubriques_on sur les groupes
 *
 * Il est hérité du parent. Toujours vrai
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_spip_groupes_mots_voirextra_rubriques_on_dist($faire,$type,$id,$qui,$opt) {
	return true;
}

/**
 * Autorisation de voir le champs extra rubriques_on sur les groupes
 *
 * On le limite aux groupes racine (si plugin gma - groupes mots arborescents)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_spip_groupes_mots_modifierextra_rubriques_on_dist($faire,$type,$id,$qui,$opt) {
	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table('spip_groupes_mots');
	if (!isset($desc['field']['id_groupe_racine'])) {
		return true;
	}

	// si c'est une creation de groupe
	// on retourne false si on crée un goupe dans un parent connu
	if ($id == 'oui'){
		return (bool)!_request('id_parent');
	}

	// sinon on cherche la racine du groupe
	$id_racine = sql_getfetsel('id_groupe_racine', 'spip_groupes_mots', 'id_groupe=' . sql_quote($id));
	// vrai si la racine est notre groupe 
	return ($id_racine == $id);
}

/**
 * Autorisation d'associer des mots à un objet
 *
 * Si l'affichage est autorisé par la fonction mère,
 * On teste que les restrictions eventuelles sur le groupe
 * ne viennent pas faire qu'il n'y aurait aucun groupe d'affiché ensuite
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_associermots($faire,$type,$id,$qui,$opt) {
	if (!autoriser_associermots_dist($faire,$type,$id,$qui,$opt)) {
		return false;
	}

	// il existe des groupes pour l'objet en question.
	// on ne s'occupe que du cas ou nous ne connaissons pas de groupe precis d'association
	if (isset($opt['groupe_champs']) OR isset($opt['id_groupe'])){
		return true;
	}

	// chercher si un groupe est autorise pour mon statut
	// et pour la table demandee
	$table = addslashes(table_objet($type));
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
		if (motus_autoriser_groupe_si_selection_rubrique($r, $type, $id, $qui))
			return true;
	}

	// tout est interdit d'affichage !
	return false;
}


/**
 * Autorisation d'afficher le selecteur de mots
 *
 * Autorisation pour un groupe de mot donné, dans un objet / id_objet donne
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
 */
function autoriser_groupemots_afficherselecteurmots($faire,$type,$id,$qui,$opt){

	static $groupes = array();
	
	$objet    = $opt['objet'];
	$id_objet = $opt['id_objet'];

	if (!$objet) return true;

	// premier tri
	if (!autoriser_associermots_dist($faire,$objet,$id_objet,$qui,$opt))
		return false;

	// liste des rubriques autorisées pour le groupe donné
	if (!isset($groupes[$id])) {
		$groupes[$id] = sql_getfetsel('rubriques_on', 'spip_groupes_mots', 'id_groupe='.$id);
	}

	// pas de restriction, on s'en va
	if (!$groupes[$id]) {
		return true;
	}

	// si restriction a une rubrique...
	// on passe la liste des rubriques concerné et on regarde si l'objet à lier est dedans ou non
	return motus_autoriser_groupe_si_selection_rubrique($groupes[$id], $objet, $id_objet, $qui);
}


/**
 * Retourne vrai si une selection de rubrique s'applique à cet objet
 * 
 * Autrement dit, si l'objet appartient à une des rubriques données
 *  
 * @param string $restriction
 *     Liste des restrictions issues d'une selection avec le selecteur generique (rubrique|3)
 * @param string $objet
 *     Objet sur lequel on teste l'appartenance a une des rubriques (article)
 * @param int $id_objet
 *     Identifiant de l'objet.
 * @param int $qui
 *     De qui teste t'on l'autorisation.
 * @return bool
**/
function motus_autoriser_groupe_si_selection_rubrique($restrictions, $objet, $id_objet, $qui) {
	// si restriction a une rubrique...
	include_spip('formulaires/selecteur/generique_fonctions');
	if ($rubs = picker_selected($restrictions, 'rubrique')) {
		
		// trouver la rubrique de l'objet en question
		if ($objet != 'rubrique') {

			$trouver_table = charger_fonction('trouver_table', 'base');
			$desc = $trouver_table( table_objet($objet) );

			if ($desc and isset($desc['field']['id_rubrique'])) {
				$table = table_objet_sql($objet);
				$id_rub = sql_getfetsel('id_rubrique', $table, id_table_objet($table) . '=' . intval($id_objet));
			}
		} else {
			$id_rub = $id_objet;
		}
		$opt = array();
		$opt['rubriques_on'] = $rubs;
		// ici on sait dans quelle rubriuqe est notre objet ($id_rub)
		// et on connait la liste des rubriques acceptées ($opt['rubriques_on'])
		return autoriser('dansrubrique', 'groupemots', $id_rub, $qui, $opt);
	}

	return false;
}



/**
 * Retourne vrai si la rubrique $id fait partie d'une des branches de $opt['rubriques_on']
 * 
 * Autrement dit, si la rubrique appartient à une des rubriques données
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_groupemots_dansrubrique_dist($faire,$type,$id,$qui,$opt){
	static $rubriques = array();

	if (!isset($opt['rubriques_on'])
	or !$rubs = $opt['rubriques_on']  // pas de liste de rubriques ?
	or !$id  // pas d'info de rubrique... on autorise par defaut...
	or in_array($id, $rubs)) // la rubrique est dedans
		return true;

	// la ca se complique...
	// si deja calcule... on le retourne.
	$hash = md5(implode('',$rubs));
	if (isset($rubriques[$id][$hash]))
		return $rubriques[$id][$hash];
	
	// remonter recursivement les rubriques...
	$id_parent = sql_getfetsel('id_parent','spip_rubriques', 'id_rubrique = '. sql_quote($id));

	// si racine... pas de chance
	if (!$id_parent) {
		$rubriques[$id][$hash] = false;
	} else {
		$rubriques[$id][$hash] = autoriser('dansrubrique','groupemots',$id_parent,$qui,$opt);
	}

	return $rubriques[$id][$hash];
}



?>

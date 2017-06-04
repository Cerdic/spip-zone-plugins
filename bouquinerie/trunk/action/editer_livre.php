<?php
/**
 * Modifier une fiche livre
 *
 * @plugin     Bouquinerie
 * @copyright  2017
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Bouquinerie\inc
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Modifier une fiche livre
 * fonction spéciale pour cet objet car la gestion des dates (parution et nouvelle edition) n'est pas la même que pour un objet standard de SPIP
 * En effet, le changement de statut d'un livre ne modifie en aucun cas la date de parution
 * $err est un message d'erreur eventuelle
 *
 * @param string $objet
 * @param int $id
 * @param array|null $set
 * @return mixed|string
 */
function livre_modifier($id, $set = null) {
	$objet = 'livre';
	$table_sql = table_objet_sql($objet);
	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table($table_sql);
	if (!$desc or !isset($desc['field'])) {
		spip_log("Objet $objet inconnu dans objet_modifier", _LOG_ERREUR);

		return _L("Erreur objet $objet inconnu");
	}
	include_spip('inc/modifier');

	$white = array_keys($desc['field']);
	// on ne traite pas la cle primaire par defaut, notamment car
	// sur une creation, id_x vaut 'oui', et serait enregistre en id_x=0 dans la base
	$white = array_diff($white, array($desc['key']['PRIMARY KEY']));

	if (isset($desc['champs_editables']) and is_array($desc['champs_editables'])) {
		$white = $desc['champs_editables'];
	}
	$c = collecter_requests(
	// white list
		$white,
		// black list 
		// ici spécial fiche livre : on retire $champ_date de la black list
		array('statut', 'id_parent', 'id_secteur'),
		// donnees eventuellement fournies
		$set
	);

	// Si l'objet est publie, invalider les caches et demander sa reindexation
	if (objet_test_si_publie($objet, $id)) {
		$invalideur = "id='$objet/$id'";
		$indexation = true;
	} else {
		$invalideur = "";
		$indexation = false;
	}
	if ($err = objet_modifier_champs($objet, $id,
		array(
			'data' => $set,
			'nonvide' => '',
			'invalideur' => $invalideur,
			'indexation' => $indexation,
			// champ a mettre a date('Y-m-d H:i:s') s'il y a modif
			'date_modif' => (isset($desc['field']['date_modif']) ? 'date_modif' : '')
		),
		$c)
	) {
		return $err;
	}

	// Modification de statut, changement de rubrique ?
	// FIXME: Ici lorsqu'un $set est passé, la fonction collecter_requests() retourne tout
	//         le tableau $set hors black liste, mais du coup on a possiblement des champs en trop. 
	// fiche livre : pareil, on retire $champ_date du tableau dans l'appel par 'collecter_requests'
	$c = collecter_requests(array('statut', 'id_parent'), array(), $set);
	$err = objet_instituer($objet, $id, $c);

	return $err;
}

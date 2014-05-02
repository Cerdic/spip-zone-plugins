<?php
/**
 * Gestion du formulaire de datation d'une commande
 *
 * Si si, datation
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Formulaires
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('formulaires/dater');

/**
 * Chargement du formulaire de datation d'une commande
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @param integer $id_commande
 *     Identifiant de la commande
 * @param string $type_date
 *     creation|paiement|envoi
 * @param string $retour
 *     Adresse de redirection
 * @return Array
 *     Environnement du formulaire
 */
function formulaires_dater_commande_charger_dist($id_commande, $type_date='creation', $retour=''){

	if (!intval($id_commande))
		return false;

	$table = table_objet('commande');
	$trouver_table = charger_fonction('trouver_table','base');
	$desc = $trouver_table($table);

	if (!$desc)
		return false;

	// date, date_paiement, date_envoi
	$champ_date = ($type_date=='creation') ? 'date' : 'date_'.$type_date;
	$label = ($type_date=='creation') ? 'date_commande_label' : 'date_'.$type_date.'_label';

	if (!isset($desc['field'][$champ_date]))
		return false;

	$valeurs = array(
		'champ_date'=>$champ_date,
		'id'=>$id_commande,
		'type_date'=>$type_date
	);

	$select = "$champ_date as date";
	$row = sql_fetsel($select, $desc['table'], "id_commande=".intval($id_commande));

	$valeurs['editable'] = autoriser('dater','commande',$id_commande,null);

	if ($regs = recup_date($row['date'], false)) {
		$annee = $regs[0];
		$mois = $regs[1];
		$jour = $regs[2];
		$heure = $regs[3];
		$minute = $regs[4];
	}

	$valeurs['afficher_'.$champ_date] = $row['date'];
	$valeurs[$champ_date.'_jour'] = dater_formater_saisie_jour($jour,$mois,$annee);
	$valeurs[$champ_date.'_heure'] = "$heure:$minute";

	$valeurs['_label_date'] = _T('commandes:'.$label);
	$valeurs['_saisie_en_cours'] = (_request($champ_date.'_jour')!==null) && (_request('annuler')==null);

	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param integer $id_commande
 *     Identifiant de la commande
 * @param string $type_date
 *     creation|paiement|envoi
 * @param string $retour
 *     Adresse de redirection
 * @return string
 *     Hash du formulaire
 */
function formulaires_dater_commande_identifier_dist($id_commande, $type_date='creation', $retour=''){
	return serialize(array($id_commande));
}

/**
 * Vérifications du formulaire de datation d'une commande
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @param integer $id_commande
 *     Identifiant de la commande
 * @param string $type_date
 *     creation|paiement|envoi
 * @param string $retour
 *     Adresse de redirection
 * @return Array Tableau des erreurs
 */
function formulaires_dater_commande_verifier($id_commande, $type_date='creation', $retour=''){
	$erreurs = array();

	$champ_date = ($type_date=='creation') ? 'date' : 'date_'.$type_date;

	if ($v=_request($champ_date."_jour") AND !dater_recuperer_date_saisie($v))
		$erreurs[$champ_date] = _T('format_date_incorrecte');
	elseif ($v=_request($champ_date."_heure") AND !dater_recuperer_heure_saisie($v))
		$erreurs[$champ_date] = _T('format_heure_incorrecte');

	return $erreurs;
}

/**
 * Traitement du formulaire de datation d'une commande
 *
 * Traiter les champs postés 
 *
 * @param integer $id_commande
 *     Identifiant de la commande
 * @param string $type_date
 *     creation|paiement|envoi
 * @param string $retour
 *     Adresse de redirection
 * @return Array
 */
function formulaires_dater_commande_traiter_dist($id_commande, $type_date='creation', $retour=''){
	$res = array('editable'=>' ');

	if (_request('changer')){
		$table = table_objet('commande');
		$trouver_table = charger_fonction('trouver_table','base');
		$desc = $trouver_table($table);

		if (!$desc)
			return array('message_erreur'=>_L('erreur')); #impossible en principe

		// date, date_paiement, date_envoi
		$champ_date = ($type_date=='creation') ? 'date' : 'date_'.$type_date;

		$set = array();

		if (!$d = dater_recuperer_date_saisie(_request($champ_date.'_jour')))
			$d = array(date('Y'),date('m'),date('d'));
		if (!$h = dater_recuperer_heure_saisie(_request($champ_date.'_heure')) or intval(_request($champ_date.'_jour'))==0)
			$h = array(0,0);

		// déjouer le formatage auto
		if (intval($d[0]) == 9000) $d[0] = 0;

		$set[$champ_date] = sql_format_date($d[0], $d[1], $d[2], $h[0], $h[1]);

		include_spip('action/editer_objet');
		objet_modifier('commande', $id_commande, $set);
	}

	if ($retour)
		$res['redirect'] = $retour;

	set_request($champ_date.'_jour');
	set_request($champ_date.'_heure');

	return $res;
}

?>

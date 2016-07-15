<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Critere {filtre_statut_subscription?} qui s'applique sur le statut de mailsubscriber ou de mailsubscription
 * @param $idb
 * @param $boucles
 * @param $crit
 */
function critere_MAILSUBSCRIBERS_filtre_statut_subscription_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	//$crit->cond
	$_statut = '@$Pile[0]["statut"]';
	$_id_mailsubscribinglist = '@$Pile[0]["id_mailsubscribinglist"]';
	$_mailsubscriber_statut = $boucle->id_table.'.statut';
	$_mailsubscription_statut = $_mailsubscriber_statut;
	foreach ($boucle->from as $cle=>$table){
		if ($table=='spip_mailsubscriptions'){
			$_mailsubscription_statut = $cle.".statut";
		}
	}
	$where = "($_id_mailsubscribinglist?'$_mailsubscription_statut':'$_mailsubscriber_statut').'='.sql_quote($_statut)";
	if ($crit->cond){
		$where = "($_statut?$where:'1=1')";
	}
	$boucle->where[] = $where;
	$boucles[$idb]->modificateur['criteres']['statut'] = true;
	$boucle->select[] = '".'."($_id_mailsubscribinglist?'$_mailsubscription_statut':'$_mailsubscriber_statut')".'." as statut_subscription';
}

/**
 * #STATUT_SUBSCRIPTION dans la boucle qui utilise {filtre_statut_subscription?}
 * @param $p
 * @return mixed
 */
function balise_STATUT_SUBSCRIPTION_dist($p) {
	return rindex_pile($p, 'statut_subscription', 'filtre_statut_subscription');
}



/**
 * @param string $liste
 * @param string $statut
 * @return array|int
 */
function filtre_mailsubscribers_compte_inscrits_dist($liste, $statut = 'valide') {
	include_spip('inc/mailsubscribers');

	return mailsubscribers_compte_inscrits($liste, $statut);
}

/**
 * Trouver les statuts auteur qui n'ont pas encore de liste automatique
 *
 * @return array
 */
function mailsubscribers_liste_statut_auteur_possibles() {
	$possibles = array(
		'0minirezo' => 'info_administrateurs',
		'1comite' => 'info_redacteurs',
		'6forum' => 'info_visiteurs'
	);
	$existing = sql_allfetsel('identifiant', 'spip_mailsubscribinglists',
		sql_in('identifiant', array_keys($possibles)) . 'AND statut!=' . sql_quote('poubelle'));
	$existing = array_map('reset', $existing);
	foreach ($existing as $id) {
		if (isset($possibles[$id])) {
			unset($possibles[$id]);
		}
	}

	$possibles = array_map('_T', $possibles);

	return $possibles;
}

/**
 * Tester si une liste est synchronisee
 * @param $identifiant
 * @return string
 */
function mailsubscribers_liste_synchronisee($identifiant) {
	include_spip('inc/mailsubscribers');
	if (mailsubscribers_trouver_fonction_synchro($identifiant)) {
		return ' ';
	}

	return '';
}

/**
 * Cle action pour les URLs subscribe/unsubscribe/confirm
 * pour avoir une cle utilisable sur une liste precise,
 * l'id de liste est fourni en suffixe du jeton
 * sous la forme "+".$id_mailsubscribinglist
 * @param $action
 * @param $email
 * @param $jeton
 * @return string
 */
function mailsubscriber_cle_action($action, $email, $jeton) {
	$arg = "$action-$email-$jeton";
	include_spip("inc/securiser_action");
	$hash = calculer_cle_action($arg);

	return $hash;
}

/**
 * URL unsubscribe
 * pour unsubscribe sur une liste precise, l'id de liste est fourni en suffixe du jeton
 * sous la forme "+".$id_mailsubscribinglist
 * @param string $email
 * @param string $jeton
 * @param string $sep
 * @return string
 */
function mailsubscriber_url_subscribe($email, $jeton, $sep = "&amp;") {
	$url = generer_url_action("subscribe_mailsubscriber", "email=" . urlencode($email), false, true);
	$url = parametre_url($url, "arg", mailsubscriber_cle_action("subscribe", $email, $jeton), $sep);

	return $url;
}

/**
 * URL subscribe
 * pour subscribe sur une liste precise, l'id de liste est fourni en suffixe du jeton
 * sous la forme "+".$id_mailsubscribinglist
 * @param string $email
 * @param string $jeton
 * @param string $sep
 * @return string
 */
function mailsubscriber_url_unsubscribe($email, $jeton, $sep = "&amp;") {
	$url = generer_url_action("unsubscribe_mailsubscriber", "email=" . urlencode($email), false, true);
	$url = parametre_url($url, "arg", mailsubscriber_cle_action("unsubscribe", $email, $jeton), $sep);

	return $url;
}

/**
 * URL confirm
 * pour confirm sur une liste precise, l'id de liste est fourni en suffixe du jeton
 * sous la forme "+".$id_mailsubscribinglist
 * @param string $email
 * @param string $jeton
 * @param string $sep
 * @return string
 */
function mailsubscriber_url_confirm($email, $jeton, $sep = "&amp;") {
	$url = generer_url_action("confirm_mailsubscriber", "email=" . urlencode($email), false, true);
	$url = parametre_url($url, "arg", mailsubscriber_cle_action("confirm", $email, $jeton), $sep);

	return $url;
}

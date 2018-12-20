<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

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
	$where_not_poubelle = "(($_id_mailsubscribinglist or $_statut=='prop')?'$_mailsubscription_statut':'$_mailsubscriber_statut').'!='.sql_quote('poubelle')";
	$boucle->where[] = "((!$_statut or !in_array($_statut,array('all','poubelle')))?$where_not_poubelle:'1=1')";
	$where = "(($_id_mailsubscribinglist or $_statut=='prop')?'$_mailsubscription_statut':'$_mailsubscriber_statut').'='.sql_quote($_statut)";
	if ($crit->cond){
		$where = "(($_statut and $_statut!=='all')?$where:'1=1')";
	}
	$boucle->where[] = $where;
	$boucles[$idb]->modificateur['criteres']['statut'] = true;
	$boucle->select[] = '".'."(($_id_mailsubscribinglist or $_statut=='prop')?'$_mailsubscription_statut':'$_mailsubscriber_statut')".'." as statut_subscription';
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
 * @param int $id_segment
 * @return array|int
 */
function filtre_mailsubscribers_compte_inscrits_dist($liste, $statut = 'valide', $id_segment=0) {
	include_spip('inc/mailsubscribers');

	return mailsubscribers_compte_inscrits($liste, $statut, $id_segment);
}


function filtre_email_variante_obfusquee_dist($email) {
	if (!function_exists('mailsubscribers_test_email_obfusque')) {
		include_spip('inc/mailsubscribers');
	}
	if (mailsubscribers_test_email_obfusque($email)) {
		return $email;
	}

	// ne pas passer par
	return mailsubscribers_obfusquer_email($email, true);
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
 * un ou plusieurs ids de liste dans $id_mailsubscribinglists
 * sous la forme "+".$id_mailsubscribinglist
 * @param string $action
 * @param string $email
 * @param string $jeton
 * @param int|array $id_mailsubscribinglists
 * @return string
 */
function mailsubscriber_cle_action($action, $email, $jeton, $id_mailsubscribinglists = null) {
	if ($id_mailsubscribinglists) {
		if (!is_array($id_mailsubscribinglists)) {
			$id_mailsubscribinglists = explode(',', $id_mailsubscribinglists);
		}
		$id_mailsubscribinglists = array_map('intval', $id_mailsubscribinglists);
		$id_mailsubscribinglists = array_filter($id_mailsubscribinglists);
		asort($id_mailsubscribinglists);
	}

	if ($id_mailsubscribinglists) {
		$jeton .= '+' . implode('+', $id_mailsubscribinglists);
	}
	$arg = "$action-$email-$jeton";
	include_spip("inc/securiser_action");
	$hash = calculer_cle_action($arg);

	if ($id_mailsubscribinglists) {
		$hash = implode('-', $id_mailsubscribinglists) . '-' . $hash;
	}

	return $hash;
}

/**
 * URL unsubscribe
 * pour subscribe sur une ou plusieurs listes precises, fournir le ou les ID dans $id_mailsubscribinglist
 * @param string $email
 * @param string $jeton
 * @param int|array $id_mailsubscribinglists
 *   une ou plusieurs listes a confirmer
 * @param string $sep
 * @return string
 */
function mailsubscriber_url_subscribe($email, $jeton, $id_mailsubscribinglists=null, $sep = "&amp;") {
	// legacy ancien format d'appel : $sep etait le 3eme argument
	if (is_string($id_mailsubscribinglists) and $id_mailsubscribinglists and !intval($id_mailsubscribinglists)) {
		$sep = $id_mailsubscribinglists;
		$id_mailsubscribinglists = null;
	}

	$url = generer_url_action("subscribe_mailsubscriber", "email=" . urlencode($email), false, true);
	$url = parametre_url($url, "arg", mailsubscriber_cle_action("subscribe", $email, $jeton, $id_mailsubscribinglists), $sep);

	return $url;
}

/**
 * URL subscribe
 * pour unsubscribe sur une ou plusieurs listes precises, fournir le ou les ID dans $id_mailsubscribinglist
 * @param string $email
 * @param string $jeton
 * @param int|array $id_mailsubscribinglists
 *   une ou plusieurs listes a confirmer
 * @param string $sep
 * @return string
 */
function mailsubscriber_url_unsubscribe($email, $jeton, $id_mailsubscribinglists=null, $sep = "&amp;") {
	// legacy ancien format d'appel : $sep etait le 3eme argument
	if (is_string($id_mailsubscribinglists) and $id_mailsubscribinglists and !intval($id_mailsubscribinglists)) {
		$sep = $id_mailsubscribinglists;
		$id_mailsubscribinglists = null;
	}

	$url = generer_url_action("unsubscribe_mailsubscriber", "email=" . urlencode($email), false, true);
	$url = parametre_url($url, "arg", mailsubscriber_cle_action("unsubscribe", $email, $jeton, $id_mailsubscribinglists), $sep);

	return $url;
}

/**
 * URL confirm
 * pour confirm sur une ou plusieurs listes precises, fournir le ou les ID dans $id_mailsubscribinglist
 * @param string $email
 * @param string $jeton
 * @param int|array $id_mailsubscribinglists
 *   une ou plusieurs listes a confirmer
 * @param string $sep
 * @return string
 */
function mailsubscriber_url_confirm($email, $jeton, $id_mailsubscribinglists=null, $sep = "&amp;") {
	// legacy ancien format d'appel : $sep etait le 3eme argument
	if (is_string($id_mailsubscribinglists) and $id_mailsubscribinglists and !intval($id_mailsubscribinglists)) {
		$sep = $id_mailsubscribinglists;
		$id_mailsubscribinglists = null;
	}

	$url = generer_url_action("confirm_mailsubscriber", "email=" . urlencode($email), false, true);
	$url = parametre_url($url, "arg", mailsubscriber_cle_action("confirm", $email, $jeton, $id_mailsubscribinglists), $sep);

	return $url;
}


function mailsubscriber_afficher_filtre_segment($segment) {
	$out = array();
	if (!function_exists('mailsubscriber_declarer_informations_liees')) {
		include_spip('inc/mailsubscribers');
	}
	if ($declaration = mailsubscriber_declarer_informations_liees()) {
		foreach($segment as $filtre_k=>$v){
			if (strncmp($filtre_k,'filtre_',7)==0){
				$k = substr($filtre_k,7);
				if (isset($declaration[$k]) and $v) {
					$out[] = mailsubscribers_afficher_valeur_informations_liees($k,$v,$declaration,false);
				}
			}
		}
	}
	return implode('<br />',$out);
}

/**
 * @pipeline mailsubscriber_informations_liees
 * @param $id_mailsubscriber
 * @param $email
 * @return string
 */
function mailsubscriber_afficher_informations_liees($id_mailsubscriber, $email) {

	$out = "";
	if (!function_exists('mailsubscriber_declarer_informations_liees')) {
		include_spip('inc/mailsubscribers');
	}
	if ($declaration = mailsubscriber_declarer_informations_liees()) {
		$infos = mailsubscriber_recuperer_informations_liees($id_mailsubscriber, $email);

		foreach($infos as $k=>$v) {
			$out .= mailsubscribers_afficher_valeur_informations_liees($k, $v, $declaration);
		}
		if ($out) {
			$out = "<table class='spip infos-liees'>"
				. "<caption>"._T('mailsubscriber:label_informations_liees')."</caption>"
				. "<tbody>$out</tbody>"
				. "</table>";
		}

	}

	return $out;

}

/**
 * Afficher une valeur des informations liees, eventuellement traduite d'apres la declaration
 * @param string $k
 * @param string $v
 * @param $declaration
 * @return string
 */
function mailsubscribers_afficher_valeur_informations_liees($k, $v, $declaration, $html=true){
	$titre = $k;
	if (isset($declaration[$k]['titre'])){
		$titre = typo(supprimer_numero($declaration[$k]['titre']));
	}
	if (is_string($v) and strpos($v,',') !== false){
		$v = explode(',', $v);
	}

	if (test_plugin_actif('saisies')) {
		if (isset($declaration[$k]['saisie'])){
			$saisie = array(
				'saisie' => $declaration[$k]['saisie'],
				'options' => $declaration[$k]['options'],
			);
			$saisie['options']['label'] = ' ';
			$vue = saisies_generer_vue($saisie,array($k=>$v));
		}
		else {
			$valeur = $v;
			if (!is_array($valeur)) $valeur = array($valeur);
			foreach ($valeur as $i=>$va){
				if (isset($declaration[$k]['valeurs'][$va])) {
					$valeur[$i] = typo(supprimer_numero($declaration[$k]['valeurs'][$va]));
				}
			}
			$vue = implode(', ',$valeur);
		}
		if ($html) {
			$out = "<tr><th scope='row'>$titre</th><td>".$vue."</td></tr>";
		}
		else {
			$out = "$titre&nbsp;: ".strip_tags($vue);
		}
	}
	return $out;
}

function mailsubscribers_is_updating_segment($id_mailsubscribinglist, $id_segment) {
	static $update_segments;
	static $progress = array();
	if (is_null($update_segments)) {
		if (isset($GLOBALS['meta']['mailsubscriptions_update_segments'])) {
			$update_segments = unserialize($GLOBALS['meta']['mailsubscriptions_update_segments']);
			if (!$update_segments){
				$update_segments = array();
			}
		}
	}
	if (isset($update_segments[$id_mailsubscribinglist])
	  and in_array($id_segment,$update_segments[$id_mailsubscribinglist])){
		if (!isset($progress[$id_mailsubscribinglist])){
			$tot = sql_countsel('spip_mailsubscriptions','id_segment=0 AND id_mailsubscribinglist='.intval($id_mailsubscribinglist));
			if ($tot > 0) {
				$n = sql_countsel('spip_mailsubscriptions','actualise_segments=1 AND id_segment=0 AND id_mailsubscribinglist='.intval($id_mailsubscribinglist));
				$progress[$id_mailsubscribinglist] = intval(round(($tot-$n)*100/$tot)).'%';
			}
			else {
				$progress[$id_mailsubscribinglist] = '100%';
			}
		}
		return $progress[$id_mailsubscribinglist];
	}
	return '';
}

/**
 * Un filtre pour lister les listes de diffusion dans un squelette
 *
 * @Note
 * Les listes sont stockées différement entre la branche 1 et 2
 * À partir de la branche 2, il est possible de boucler directement sur la table subscribinglists
 *
 * @uses mailsubscribers_listes
 * @param array $options
 *     voir mailsubscribers_listes()
 * @return array|int
 */
function filtre_mailsubscribers_listes_dist($options = array()) {
	include_spip('inc/mailsubscribers');

	return mailsubscribers_listes($options);
}

/**
 * Titrer un segment
 * @param string $identifiant
 * @param int $id_segment
 * @return string
 */
function mailsubscribers_titre_segment($identifiant, $id_segment) {
	static $listes;
	if (is_null($listes)) {
		include_spip('inc/mailsubscribers');
		$listes = mailsubscribers_listes(array('segments' => true));
	}

	$id = $identifiant;
	if ($id_segment) {
		$id = "$id+$id_segment";
	}
	if (isset($listes[$id])) {
		return $listes[$id]['titre'];
	}
	return $id;
}

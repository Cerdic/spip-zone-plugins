<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/mailsubscribers');
include_spip('inc/mailsubscribinglists');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_mailsubscribinglist_segment_identifier_dist($id_mailsubscribinglist, $id_segment = 'new', $retour = '') {
	return serialize(array(intval($id_mailsubscribinglist),intval($id_segment)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_mailsubscribinglist_segment_charger_dist($id_mailsubscribinglist, $id_segment = 'new', $retour = '') {
	$subscribinglist = sql_fetsel('*','spip_mailsubscribinglists','id_mailsubscribinglist='.intval($id_mailsubscribinglist));
	$segments = array();
	if ($subscribinglist['segments']) {
		$segments = unserialize($subscribinglist['segments']);
	}

	$valeurs = array(
		'_id_mailsubscribinglist' => $id_mailsubscribinglist,
		'_id_segment' => $id_segment,
		'titre' => '',
		'auto_update' => '',
		'_saisies' => array(),
	);

	if (intval($id_segment) and isset($segments[$id_segment]['titre'])) {
		$valeurs['titre'] = $segments[$id_segment]['titre'];
		$valeurs['auto_update'] = $segments[$id_segment]['auto_update'];
	}

	$declaration = mailsubscriber_declarer_informations_liees();
	foreach ($declaration as $k=>$d) {
		if (isset($d['saisie']) and !isset($valeurs['filtre_'.$k])){
			$valeurs['filtre_'.$k] = '';
			if (intval($id_segment) and isset($segments[$id_segment]['filtre_'.$k])) {
				$valeurs['filtre_'.$k] = $segments[$id_segment]['filtre_'.$k];
				if (isset($d['options']['multiple']) and $d['options']['multiple']) {
					$valeurs['filtre_'.$k] = explode(',', $valeurs['filtre_'.$k]);
				}
			}

			$saisie = array(
				'saisie' => $d['saisie'],
				'options' => $d['options'],
			);
			$saisie['options']['nom'] = 'filtre_'.$k;
			if (!isset($saisie['options']['label'])) {
				$saisie['options']['label'] = $d['titre'];
			}
			if (isset($saisie['options']['label'])) {
				$saisie['options']['label'] = supprimer_numero(typo($saisie['options']['label']));
			}
			$valeurs['_saisies'][] = $saisie;
		}
	}

	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_mailsubscribinglist_segment_verifier_dist($id_mailsubscribinglist, $id_segment = 'new', $retour = '') {

	$erreurs = formulaires_editer_objet_verifier('mailsubscribinglist', $id_mailsubscribinglist, array('titre'));

	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_mailsubscribinglist_segment_traiter_dist($id_mailsubscribinglist, $id_segment = 'new', $retour = '') {
	$subscribinglist = sql_fetsel('*','spip_mailsubscribinglists','id_mailsubscribinglist='.intval($id_mailsubscribinglist));
	$segments = array();
	if ($subscribinglist['segments']) {
		$segments = unserialize($subscribinglist['segments']);
	}
	$update = _request('update');

	if (!intval($id_segment)){
		$id_segment = max(array_keys($segments)) + 1;
		$update = true;
	}

	$segment = array(
		'id' => $id_segment,
		'titre' => _request('titre'),
		'auto_update' => _request('auto_update'),
	);
	$declaration = mailsubscriber_declarer_informations_liees();
	foreach ($declaration as $k=>$d) {
		if (isset($d['saisie']) and !isset($segment['filtre_'.$k])) {
			$v = _request('filtre_'.$k);
			if (is_array($v)) {
				$v = array_map('trim', $v);
				$v = array_filter($v);
				$v = implode(',', $v);
			}
			$segment['filtre_'.$k] = trim($v);
		}
	}
	$segments[$id_segment] = $segment;
	include_spip('action/editer_objet');
	objet_modifier('mailsubscribinglist', $id_mailsubscribinglist, array('segments'=>serialize($segments)));

	if ($update){
		mailsubscribers_start_update_mailsubscribinglist_segment($id_mailsubscribinglist, $id_segment);
	}

	$res = array(
		'message_ok' => 'ok'
	);
	if ($retour){
		$res['redirect'] = $retour;
	}

	return $res;
}


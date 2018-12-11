<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function mailsubscribers_start_update_mailsubscribinglist_segment($id_mailsubscribinglist, $id_segment){
	$update = array();
	if (isset($GLOBALS['meta']['mailsubscriptions_update_segments'])) {
	  $update = unserialize($GLOBALS['meta']['mailsubscriptions_update_segments']);
		if (!$update) $update = array();
	}
	if (!isset($update[$id_mailsubscribinglist])) $update[$id_mailsubscribinglist] = array();
	$update[$id_mailsubscribinglist][] = $id_segment;
	$update[$id_mailsubscribinglist] = array_unique($update[$id_mailsubscribinglist]);
	$update[$id_mailsubscribinglist] = array_filter($update[$id_mailsubscribinglist]);
	ecrire_meta('mailsubscriptions_update_segments', serialize($update));

	// placer le pointeur sur les subscriptions pour le genie
	// uniquement sur les subscribers qui ont au moins un des segments valide (sinon si tout refuse il n'y aura rien a faire)
	// on est oblige de faire des series de allfetsel/update car on ne peut pas faire un update avec ne sous requete comme celle la
	$n = 0;
	while ($in_subscribers_valides = sql_allfetsel(
		"DISTINCT id_mailsubscriber",
		"spip_mailsubscriptions",
		"statut!=" . sql_quote('refuse') . ' AND actualise_segments=0 AND id_segment=0 AND id_mailsubscribinglist=' . intval($id_mailsubscribinglist), '', '', '0,10000')){
		$in_subscribers_valides = array_column($in_subscribers_valides, 'id_mailsubscriber');
		sql_updateq('spip_mailsubscriptions', array('actualise_segments' => 1), 'id_segment=0 AND id_mailsubscribinglist=' . intval($id_mailsubscribinglist) . " AND " . sql_in('id_mailsubscriber', $in_subscribers_valides));
		spip_log("actualise_segments=1 sur liste $id_mailsubscribinglist et " . count($in_subscribers_valides) . ' inscrits', 'mailsubscribers');
		$n += count($in_subscribers_valides);
	}
	spip_log("actualise_segments=1 sur liste $id_mailsubscribinglist et TOTAL $n inscrits", 'mailsubscribers');
}

/**
 * Mettre a jour tous les segments de toutes les listes d'un subscriber
 * @param $id_mailsubscriber
 * @param bool|array $force
 *   true ou liste de $id_mailsubscribinglist sur lesquels on veut forcer la mise a jour
 */
function mailsubscribers_actualise_segments($id_mailsubscriber, $force = false){

	$ids = sql_allfetsel('id_mailsubscribinglist','spip_mailsubscriptions','id_segment=0 AND id_mailsubscriber='.intval($id_mailsubscriber));
	$ids = array_map('reset', $ids);
	// supprimer les segments morts sur d'autres id_mailsubscribinglist
	sql_delete('spip_mailsubscriptions', 'id_mailsubscriber='.intval($id_mailsubscriber).' AND id_segment>0 AND '.sql_in('id_mailsubscribinglist',$ids,'NOT'));
	foreach ($ids as $id_mailsubscribinglist){
		$force_this = (is_array($force) ? in_array($id_mailsubscribinglist, $force) : $force);
		mailsubscribers_actualise_mailsubscribinglist_segments($id_mailsubscriber, $id_mailsubscribinglist, $force_this);
	}
	
}

/**
 * Mettre a jour tous les segment d'une liste d'un subscriber
 * @param $id_mailsubscriber
 * @param $id_mailsubscribinglist
 * @param bool $force
 */
function mailsubscribers_actualise_mailsubscribinglist_segments($id_mailsubscriber, $id_mailsubscribinglist, $force = false){
	static $segments = array();
	static $update_segments;
	if (!isset($segments[$id_mailsubscribinglist])) {
		if ($segments[$id_mailsubscribinglist] = sql_getfetsel('segments','spip_mailsubscribinglists','id_mailsubscribinglist='.intval($id_mailsubscribinglist))){
			$segments[$id_mailsubscribinglist] = unserialize($segments[$id_mailsubscribinglist]);
		}
	}
	if (is_null($update_segments)) {
		if (isset($GLOBALS['meta']['mailsubscriptions_update_segments'])) {
			$update_segments = unserialize($GLOBALS['meta']['mailsubscriptions_update_segments']);
			if (!$update_segments){
				$update_segments = array();
			}
		}
	}

	if ($segments[$id_mailsubscribinglist]){
		$update_needed = array();
		if (isset($update_segments[$id_mailsubscribinglist])) {
			$update_needed = &$update_segments[$id_mailsubscribinglist];
		}
		foreach ($segments[$id_mailsubscribinglist] as $id_segment=>$segment){
			if ($force
				or in_array($id_segment, $update_needed)
			  or (isset($segment['auto_update']) and $segment['auto_update'])){
				mailsubscribers_actualise_segment($id_mailsubscriber, $id_mailsubscribinglist, $id_segment, $segments[$id_mailsubscribinglist]);
			}
		}
	}

}

/**
 * Mettre a jour un segment d'une liste d'un subscriber
 * @param $id_mailsubscriber
 * @param $id_mailsubscribinglist
 * @param $id_segment
 * @param array $segments
 */
function mailsubscribers_actualise_segment($id_mailsubscriber, $id_mailsubscribinglist, $id_segment, $segments = null){
	if (is_null($segments)) {
		if ($segments = sql_getfetsel('segments','spip_mailsubscribinglists','id_mailsubscribinglist='.intval($id_mailsubscribinglist))){
			$segments = unserialize($segments);
		}
	}

	if ($segments and isset($segments[$id_segment])){
		$need = mailsubscribers_teste_segment($id_mailsubscriber,$segments[$id_segment]);
		$where = 'id_mailsubscriber='.intval($id_mailsubscriber).' AND id_mailsubscribinglist='.intval($id_mailsubscribinglist).' AND id_segment=';
		$is = sql_countsel('spip_mailsubscriptions', $where . intval($id_segment));
		if ($is and !$need) {
			sql_delete('spip_mailsubscriptions', $where . intval($id_segment));
		}
		if ($need and !$is) {
			if ($sub = sql_fetsel('*','spip_mailsubscriptions', $where . intval(0))){
				$sub['id_segment'] = $id_segment;
				sql_insertq('spip_mailsubscriptions', $sub);
			}
		}
	}
}

/**
 * Tester l'appartenance d'un subscriber a un segment
 * @param $id_mailsubscriber
 * @param $segment
 * @return bool
 */
function mailsubscribers_teste_segment($id_mailsubscriber, $segment){
	static $informations_liees = array();
	static $declaration;
	if (is_null($declaration)){
		if (!function_exists('mailsubscriber_declarer_informations_liees')) {
			include_spip('inc/mailsubscribers');
		}
		$declaration = mailsubscriber_declarer_informations_liees();
	}
	
	if (!$declaration) return false;

	if (!isset($informations_liees[$id_mailsubscriber])) {
		$email = sql_getfetsel('email','spip_mailsubscribers','id_mailsubscriber='.intval($id_mailsubscriber));
		$informations_liees[$id_mailsubscriber] = mailsubscriber_recuperer_informations_liees($id_mailsubscriber, $email);
	}
	$infos = &$informations_liees[$id_mailsubscriber];

	foreach($segment as $k=>$v){
		if (strncmp($k,'filtre_',7)==0 and strlen($v)){
			$filtre_k = substr($k,7);
			if (!isset($infos[$filtre_k])){
				return false;
			}
			// si le filtre contient plusieurs valeurs, il suffit qu'on en ait une pour etre dans le segment (c'est un OU)
			if (strpos($v,',') !== false){
				$v = explode(',', $v);
				if (is_array($infos[$filtre_k])){
					if (!array_intersect($v,$infos[$filtre_k])){
						return false;
					}
				}
				else {
					if (!in_array($infos[$filtre_k],$v)){
						return false;
					}
				}
			}
			// si le filtre contient 1 valeur, il faut qu'elle soit dans les infos du subscriber
			else {
				if (is_array($infos[$filtre_k])){
					if (!in_array($v,$infos[$filtre_k])){
						return false;
					}
				}
				else {
					if ($infos[$filtre_k]!=$v){
						return false;
					}
				}
			}
		}
	}

	return true;

}
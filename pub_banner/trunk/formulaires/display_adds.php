<?php
/**
 * @name 		Pubban displayer
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 * @subpackage	Formulaires
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_display_adds_charger_dist(){
	include_spip('inc/pubban_process');
	include_spip('base/abstract_sql');
	$emplacement = pubban_recuperer_emplacement_par_nom(_request('empl'));
	if($emplacement['statut'] != '2actif') return;

	$list_pub = pubban_pubs_de_emplacement($emplacement['id'], false);
	$nbpub = count($list_pub);
	if($nbpub == 0) return;
	$nbpub = $nbpub-1;
	$banaffi = rand(0, $nbpub);

	$recup = sql_select("*", $GLOBALS['_PUBBAN_CONF']['table_pub'], "id_pub IN (".join(',', $list_pub).") AND statut IN ('2actif')", '', '', $banaffi.",1", '', _BDD_PUBBAN);
	while($tableau = spip_fetch_array($recup)){
		$id_pub = $tableau['id_pub'];
		$nompub = $tableau['titre'];
		$url = $tableau['url'];
		$code = $tableau['objet'];
		$type = $tableau['type'];
		$affires = $tableau['affichages_restant'];
		$affi = $tableau['affichages'];
	}
	if(!strlen($url) AND _PUBBAN_ADDS) $url = $GLOBALS['meta']['adresse_site'].'/?page='.PUBBAN_SKEL_ADDS;
	$datas['affichages'] = $affi+1;
	if($affires != 0) {
		$datas['affichages_restant'] = $affires-1;
		if($datas['affichages_restant'] == 0) $datas['statut'] = '3obsolete';
	}
	$editer_pub = charger_fonction('editer_pub', 'inc');
	$editer_pub($id_pub, $datas);

	// Statistiques
	$date_stats = date("Y-m-d");
	$jour_stats = date("z");
	$recup = sql_select("*", $GLOBALS['_PUBBAN_CONF']['table_stats'], "date IN ('".$date_stats."') AND id_empl=".$emplacement['id'], '', '', '', '', _BDD_PUBBAN);
	if (sql_count($recup) > 0) {
		while($tableau = spip_fetch_array($recup)){
			$verif_affi = $tableau['affichages'];
		}
		sql_updateq($GLOBALS['_PUBBAN_CONF']['table_stats'], array("affichages" => $verif_affi + 1), "date IN ('".$date_stats."') AND id_empl=".$emplacement['id'], '', _BDD_PUBBAN);
	}
	else{
		sql_insertq($GLOBALS['_PUBBAN_CONF']['table_stats'],  array('id_empl'=>$emplacement['id'],'jour'=>$jour_stats,'date'=>$date_stats,'clics'=>'0','affichages'=>'1'), '', _BDD_PUBBAN);
	}

	$valeurs = array(
		'test' => $a,
		'url' => substr_count($url, 'http://') ? $url : generer_url_public($url),
		'nompub' => $nompub,
		'id_empl' => $emplacement['id'],
		'id_pub' => $id_pub,
		'type' => $type,
		'code' => $code,
		'java_goto' => generer_url_public(_PUBBAN_ADDS_CLICKER),
		'width' => $emplacement['width'],
		'height' => $emplacement['height']
	);
	
	return $valeurs;
}
function formulaires_display_adds_verifier_dist(){}
function formulaires_display_adds_traiter_dist(){}
?>
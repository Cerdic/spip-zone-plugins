<?php
/**
 * @name 		Fonctions
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Renvoie la puce d'un element en fonction de son statut
 */
function pubban_recup_puce_pub($statut, $type=false) {
	$div = '';
	foreach($GLOBALS['_PUBBAN_PUCES_STATUTS'] as $key => $value){
		if(isset($value['value']) && $statut == $value['value']) {
			if($type) $div .= $value['name'];
			else $div .= $value['icon'];
		}
	}
	return($div);
}

/**
 * Renvoie le statut affichable d'un element
 */
function pubban_recup_statut_pub($statut) {
	return( _T('pubban:'.substr($statut,1)) );
}

/**
 * @todo ecrire la fonction !!
 */
function retirer_lien_pub($url='', $what='pub', $id_del=false){
}

function pubban_exporter($list_id){
	include_spip('inc/filtres');
	$ids = explode(',', $list_id);
	$entetes = array(
			_T('pubban:site_web'),
			_T('pubban:titre'),
			_T('pubban:type'),
			_T('pubban:date_add'),
			_T('pubban:statut'),
			_T('pubban:url'),
			_T('pubban:emplacement'),
			_T('pubban:dimensions_emplacement'),
			_T('pubban:illimite'),
			_T('pubban:date_debut'),
			_T('pubban:date_fin'),
			_T('pubban:affichages'),
			_T('pubban:affichages_restant'),
			_T('pubban:clics'),
			_T('pubban:clics_restant'),
			_T('pubban:ratio'),
	);
	$donnees = array();
	if(count($ids)) foreach($ids as $id){
		include_spip('inc/pubban_process');
		$datas = pubban_recuperer_pub($id);
		$id_emp = pubban_emplacements_de_la_pub($id);
		$datas_emp = pubban_recuperer_emplacement($id_emp);
		$donnees[$id] = array(
			textebrut($GLOBALS['meta']['nom_site']),
			textebrut($datas['titre']),
			$datas['type'],
			date_iso($datas['date_add']),
			pubban_recup_statut_pub($datas['statut']),
			$datas['url'],
			textebrut($datas_emp['titre']),
			$datas_emp['width']." x ".$datas_emp['height']." px",
			$datas['illimite'],
			date_iso($datas['date_debut']),
			date_iso($datas['date_fin']),
			$datas['affichages'],
			$datas['affichages_restant'],
			$datas['clics'],
			$datas['clics_restant'],
			($datas['clics']/$datas['affichages'])." %",
		);
	}
	// Nom du fichier
	$export = "Export Campagne Stats ".date('d-m-Y');
	// On exporte (fonction plugin bonux)
	include_spip('inc/exporter_csv');
/*
	echo "<pre>";	
	echo "le tableau d'en-tetes : ".var_export($entetes,1);
	echo "le tableau d'en-tetes formate : ".var_export(array_map('texte_backend', array_map('textebrut', $entetes)),1);

	echo "le tableau final : ".var_export($donnees,1);
	echo "le tableau final formate : ".var_export(array_walk($donnees, 'textebrut'),1);
	exit;
*/
	inc_exporter_csv_dist(
//		$export, $donnees,';', array_map('texte_backend', array_map('textebrut', $entetes))
		$export, $donnees,',', array_map('texte_backend', array_map('textebrut', $entetes))
	);
}

/**
 * Formate en HTML
 * Inverse de {@link affiche_code_pub()}
 */
function env_to_html($str){
	$serialize_chars = array('&lt;', '&gt;', '&#39;');
	$unserialize_chars = array('<', '>', '"');
	return( str_replace($serialize_chars, $unserialize_chars, $str) );
}

/**
 * Formate code
 * Inverse de {@link env_to_html()}
 */
function affiche_code_pub($str){
	$serialize_chars = array('&lt;', '&gt;', '&#39;');
	$unserialize_chars = array('<', '>', '"');
	return( str_replace($unserialize_chars, $serialize_chars, $str) );
}

/**
 * Affichage d'une pub en fonction du code
 */
function affiche_pub($code){
	if(pubban_UrlOK($code)) return $code;
	if(_PUBBAN_REP) return str_replace('../', $GLOBALS['meta']['adresse_site'].'/', _PUBBAN_REP).'/'.$code;
	return $code;
}

?>
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
 * Renvoie l'image d'un element en fonction de son type
 */
function pubban_recup_img_pub($type, $objet) {
	$ext_pub = pubban_extension( $objet );
	return ($type == 'img') ? ( isset($GLOBALS['pubban_pub_icons'][ $ext_pub ]) ? $GLOBALS['pubban_pub_icons'][ $ext_pub ] : $GLOBALS['pubban_pub_icons']['default'] ) : $GLOBALS['pubban_pub_icons']['flash'];
}

/**
 * @todo ecrire la fonction !!
 */
function retirer_lien_pub($url='', $what='pub', $id_del=false){
	$tot_arg = _request($what);
	$_tot_arg = str_replace($id_del, '', $tot_arg);
	$n_url = parametre_url($url, $what, $_tot_arg);
	return $n_url;
}

function pubban_exporter($ids=null, $banner_ids=null){
	include_spip('inc/filtres');
	include_spip('inc/publicite');
	include_spip('inc/banniere');
	if (!is_array($ids)) $ids = explode(',', $ids);
	$ids = array_filter($ids);

	if (!count($ids)) {
		if (empty($banner_ids)) return;
		else {
			if (!is_array($banner_ids)) $banner_ids = explode(',', $banner_ids);
			include_spip('inc/pubban_process');
			$tmpids=array();
			foreach($banner_ids as $ban_id) {
				$_ids = pubban_pubs_de_la_banniere($ban_id, true);
				$tmpids = array_merge($tmpids, $_ids);
			}
		}
		$ids = $tmpids;
	}

	$entetes = array(
			_T('pubban:site_web'),
			_T('pubban:titre'),
			_T('pubban:type'),
			_T('pubban:date_add'),
			_T('pubban:statut'),
			_T('pubban:url_pub'),
			_T('pubban:bannieres_pub'),
			_T('pubban:dimensions'),
			_T('pubban:illimite'),
			_T('pubban:date_debut'),
			_T('pubban:date_fin'),
			_T('pubban:nb_affichages'),
			_T('pubban:nb_affires_pub'),
			_T('pubban:nb_clics'),
			_T('pubban:nb_clicres_pub'),
			_T('pubban:ratio'),
	);
	$donnees = array();
	if(count($ids)) foreach($ids as $id){
		include_spip('inc/pubban_process');
		$datas = pubban_recuperer_publicite($id);
		$id_emp = pubban_bannieres_de_la_pub($id);
		$datas_emp = pubban_recuperer_banniere($id_emp);
		$donnees[$id] = array(
			utf8_encode( html_entity_decode(textebrut($GLOBALS['meta']['nom_site']))),
			utf8_encode( html_entity_decode(textebrut($datas['titre']))),
			$datas['type'],
			date_iso($datas['date_add']),
			utf8_encode( html_entity_decode(pubban_recup_statut_pub($datas['statut']))),
			$datas['url'],
			utf8_encode( html_entity_decode(textebrut($datas_emp['titre']))),
			$datas_emp['width']." x ".$datas_emp['height']." px",
			$datas['illimite'],
			!empty($datas['date_debut']) ? date_iso($datas['date_debut']) : '-',
			!empty($datas['date_fin']) ? date_iso($datas['date_fin']) : '-',
			$datas['affichages'],
			$datas['affichages_restant'],
			$datas['clics'],
			$datas['clics_restant'],
			round( ($datas['clics']/$datas['affichages']*100), 4)." %",
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
		$export, $donnees,',', array_map('utf8_encode', array_map('html_entity_decode', array_map('textebrut', $entetes)))
	);
}

/**
 * Formate en HTML
 * Inverse de {@link affiche_code_pub()}
 */
function env_to_html($str){
	$serialize_chars = array('&lt;', '&gt;', '&#39;', '&quot;');
	$unserialize_chars = array('<', '>', '"', "'");
	return( str_replace($serialize_chars, $unserialize_chars, $str) );
}

/**
 * Formate code
 * Inverse de {@link env_to_html()}
 */
function affiche_code_pub($str){
	$serialize_chars = array('&lt;', '&gt;', '&#39;', '&quot;');
	$unserialize_chars = array('<', '>', '"', "'");
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

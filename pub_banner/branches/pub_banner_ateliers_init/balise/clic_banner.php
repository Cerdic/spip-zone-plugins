<?php
/**
 * @name 		Clic banniere
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 * @subpackage	Balises
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_CLIC_BANNER($p) {
   return calculer_balise_dynamique($p,CLIC_BANNER,array());
}

function balise_CLIC_BANNER_dyn($p, $id_pub, $id_empl) {
	$id_pub = _request('id_pub');
	$id_empl = _request('id_empl') ? _request('id_empl') : $pub['emplacement'];
	$redirect = _request('redirect') ? _request('redirect') : false;
	$pub = pubban_recuperer_pub($id_pub);
	$emplacement = pubban_recuperer_emplacement($id_empl);

	$datas['clics'] = $pub['clics'] + 1;
	if($pub['clics_restant'] == 1) $datas['actif'] = '0';
	elseif($pub['clics_restant'] != 0) $datas['clics_restant'] = $pub['clicres'] - 1;

	sql_updateq($GLOBALS['_PUBBAN_CONF']['table_pub'],$datas,"id_pub='".$id_pub."'", '', _BDD_PUBBAN);

	// Statistiques
	$date_stats = date("Y-m-d");
	$jour_stats = date("z");
	$recup = sql_select("*", $GLOBALS['_PUBBAN_CONF']['table_stats'], "date IN ('".$date_stats."') AND id_empl=".$emplacement['id'], '', '', '', '', _BDD_PUBBAN);
	if (sql_count($recup) > 0) {
		while($tableau = spip_fetch_array($recup)){
			$verif_clic = $tableau['clics'];
		}
		sql_updateq($GLOBALS['_PUBBAN_CONF']['table_stats'], array("clics" => $verif_clic + 1), "date IN ('".$date_stats."') AND id_empl=".$emplacement['id'], '', _BDD_PUBBAN);
	}
	if($redirect){
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}

}

?>
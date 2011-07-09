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

function balise_CLIC_BANNIERE($p) {
   return calculer_balise_dynamique($p,CLIC_BANNIERE,array());
}

function balise_CLIC_BANNIERE_dyn($p, $id_publicite, $id_banniere) {
	include_spip('base/abstract_sql');
	$id_publicite = _request('id_publicite');
	$pub = pubban_recuperer_publicite($id_publicite);
	$id_banniere = _request('id_banniere') ? _request('id_banniere') : $pub['id_banniere'];
	$redirect = _request('redirect') ? _request('redirect') : false;
	$banniere = pubban_recuperer_banniere($id_banniere);

	$datas['clics'] = $pub['clics'] + 1;
	if($pub['clics_restant'] == 1) $datas['actif'] = '0';
	elseif($pub['clics_restant'] != 0) $datas['clics_restant'] = $pub['clicres'] - 1;

	sql_updateq('spip_publicites',$datas,"id_publicite='".$id_publicite."'", '');

	// Statistiques
	$date_stats = date("Y-m-d");
	$jour_stats = date("z");
	$recup = sql_select("*", 'spip_pubban_stats', "date IN ('".$date_stats."') AND id_banniere=".$banniere['id'], '', '', '', '');
	if (sql_count($recup) > 0) {
		while($tableau = spip_fetch_array($recup)){
			$verif_clic = $tableau['clics'];
		}
		sql_updateq('spip_pubban_stats', array("clics" => $verif_clic + 1), "date IN ('".$date_stats."') AND id_banniere=".$banniere['id'], '');
	}
/*
	if($redirect){
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
*/
}

?>
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

function balise_CLIC_BANNIERE_dyn($p) {
	include_spip('base/abstract_sql');
	include_spip('inc/publicite');
	include_spip('inc/banniere');

	$tout = _request('tout');
	$id_publicite = _request('id_publicite');
	$pub = pubban_recuperer_publicite($id_publicite);
	$id_banniere = _request('id_banniere') ? _request('id_banniere') : $pub['id_banniere'];
	$redirect = _request('redirect') ? urldecode(_request('redirect')) : false;
	$page = _request('from') ? _request('from') : '';
	$banniere = pubban_recuperer_banniere($id_banniere);

	// On ne comptabilise les clics que si 'tout' est absent
	if ($tout==false || $tout==0) {
		$datas['clics'] = $pub['clics'] + 1;
		if($pub['clics_restant'] == 1) $datas['actif'] = '0';
		elseif($pub['clics_restant'] != 0) $datas['clics_restant'] = $pub['clicres'] - 1;

		sql_updateq('spip_publicites',$datas,"id_publicite='".$id_publicite."'", '');

		// Statistiques
		$date_stats = date("Y-m-d");
		$jour_stats = date("z");

		// Stats banniere
		$_id_ban = intval($banniere['id']);
		$recup_banniere = sql_select("*", 'spip_pubban_stats', "date IN ('".$date_stats."') AND id_banniere=".$_id_ban." AND page='$page'", '', '', '', '');
		if (sql_count($recup_banniere) > 0) {
			while($tableau = spip_fetch_array($recup_banniere)){
				$verif_clic = $tableau['clics'];
			}
			sql_updateq('spip_pubban_stats', array("clics" => $verif_clic + 1), "date IN ('".$date_stats."') AND id_banniere=".$_id_ban." AND page='$page'", '');
		}

		// Stats pub
		$_id_pub = intval($id_publicite);
		$recup_pub = sql_select("*", 'spip_pubban_stats', "date IN ('".$date_stats."') AND id_publicite=".$_id_pub." AND page='$page'", '', '', '', '');
		if (sql_count($recup_pub) > 0) {
			while($tableau = spip_fetch_array($recup_pub)){
				$verif_clic = $tableau['clics'];
			}
			sql_updateq('spip_pubban_stats', array("clics" => $verif_clic + 1), "date IN ('".$date_stats."') AND id_publicite=".$_id_pub." AND page='$page'", '');
		}
	}

	// Redirection si demande
	if($redirect){
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
	// Sinon retour
	return '';
}

?>
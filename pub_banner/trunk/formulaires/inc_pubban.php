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

function formulaires_inc_pubban_charger_dist($env=null,$id_banniere=null){
	include_spip('inc/pubban_process');
	include_spip('base/abstract_sql');
	include_spip('inc/banniere');
	if (empty($id_banniere))
		$id_banniere = _request('empl');
	$tout = _request('tout') ? _request('tout') : false;
	$banniere = pubban_recuperer_banniere_par_nom($id_banniere);
	$page = _request('from') ? _request('from') : '';
	if(empty($banniere) || (!$tout && $banniere['statut'] != '2actif')) return;

	$list_pub = pubban_pubs_de_la_banniere($banniere['id'], false);
	$nbpub = count($list_pub);
	if($nbpub == 0) return;
	$nbpub = $nbpub-1;
	$banaffi = rand(0, $nbpub);

	$recup = sql_select("*", 'spip_publicites', "id_publicite IN (".join(',', $list_pub).") AND statut IN ('2actif')", '', '', $banaffi.",1", '');
	while($tableau = spip_fetch_array($recup)){
		$id_publicite = $tableau['id_publicite'];
		$nompub = $tableau['titre'];
		$url = $tableau['url'];
		$blank = $tableau['blank'];
		$code = $tableau['objet'];
		$type = $tableau['type'];
		$affires = $tableau['affichages_restant'];
		$affi = $tableau['affichages'];
	}

	// On ne comptabilise l'affichage que si 'tout' est absent
	if ($tout===false) {
		$datas['affichages'] = $affi+1;
		if($affires != 0) {
			$datas['affichages_restant'] = $affires-1;
			if($datas['affichages_restant'] == 0) $datas['statut'] = '3obsolete';
		}
		$editer_pub = charger_fonction('editer_publicite', 'inc');
		$editer_pub($id_publicite, $datas);

		// Statistiques
		$date_stats = date("Y-m-d");
		$jour_stats = date("z");

		// Stats banniere
		$id_banniere = intval($banniere['id']);
		$recup_banniere = sql_select("*", 'spip_pubban_stats', "date IN ('".$date_stats."') AND id_banniere=".$id_banniere." AND page='$page'", '', '', '', '');
		if (sql_count($recup_banniere) > 0) {
			while($tableau = spip_fetch_array($recup_banniere)){
				$verif_affi = $tableau['affichages'];
			}
			sql_updateq('spip_pubban_stats', array("affichages" => $verif_affi + 1), "date IN ('".$date_stats."') AND id_banniere=".$id_banniere." AND page='$page'", '');
		}
		else{
			sql_insertq('spip_pubban_stats',  array('id_banniere'=>$banniere['id'],'jour'=>$jour_stats,'date'=>$date_stats,'clics'=>'0','affichages'=>'1','page'=>$page), '');
		}
	}

	// Affichage ...
	$valeurs = array(
//		'test' => $a,
		'url' => substr_count($url, 'http://') ? $url : generer_url_public($url),
		'blank' => $blank,
		'nomframe' => $banniere['titre_id'],
		'nompub' => $nompub,
		'id_banniere' => $banniere['id'],
		'id_publicite' => $id_publicite,
		'type' => $type,
		'code' => $code,
		'java_goto' => generer_url_public(_PUBBAN_ADDS_CLICKER),
		'width' => $banniere['width'],
		'height' => $banniere['height'],
		'javascript_onclick' => defined('PUBBAN_FORCE_JAVASCRIPT_ONCLICK') && PUBBAN_FORCE_JAVASCRIPT_ONCLICK==1 ? 'oui' : 'non',
		'javascript_refresh'=>$banniere['refresh'],
		'from' => $page,
		'tout'=>$tout
	);

	// un cas tellement idiot : il n'y a qu'une banniere => pas de refresh
	if (!$nbpub){
		$valeurs['javascript_refresh'] = 0;
	}
	
	return $valeurs;
}
function formulaires_inc_pubban_verifier_dist(){}
function formulaires_inc_pubban_traiter_dist(){}
?>
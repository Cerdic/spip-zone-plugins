<?php
/**
 * @name 		Statistiques
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 * @subpackage	Formulaires
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_stats_adds_charger_dist(){
	include_spip('base/abstract_sql');
	$valeurs = array(
		'action' => generer_url_ecrire('pubban_stats'),
		'date_today' => date("d/m/Y"),
		'date_from_7' => date('d/m/y', time() - 604800),
		'date_from_30' => date('d/m/y', time() - 2592000),
		'date_from_90' => date('d/m/y', time() - 7776000),
	);

	$valeurs['type_perf'] = $type_perf = _request('type_perf') ? _request('type_perf') : 'clic';
	$valeurs['period_perf'] = $period_perf = _request('period_perf') ? _request('period_perf') : 1;
	$valeurs['type_evo'] = $type_evo = _request('type_evo') ? _request('type_evo') : 'clic';
	$date_stats = date("Y-m-d");
	$jour_stats = date("z");
	$toshow_perf=0;

	//  Clic
	if($type_perf == 'clic') {
		$recup = sql_select("id_empl", $GLOBALS['_PUBBAN_CONF']['table_empl'], '', '', '', '', '', _BDD_PUBBAN);
		$n=1;
		$n_emp_zero = 0;
		$n_emp_verif = 0;
		while($tableau = spip_fetch_array($recup)){
			$emp = $tableau['id_empl'];
			$emplacement = pubban_recuperer_emplacement($emp);
			$n_emp_verif++;

			if($period_perf == 1) $requete = sql_select("SUM(clics) AS Cpt, SUM(affichages) AS Aff", $GLOBALS['_PUBBAN_CONF']['table_stats'], "id_empl=".$emp." AND date IN ('".$date_stats."')", '', '', '', '', _BDD_PUBBAN);

			elseif($period_perf == 7){
				$inter_stats = date('z', time() - 604800); 
				$sep = ($inter_stats > $jour_stats) ? 'OR' : 'AND';
				$requete = sql_select("SUM(clics) AS Cpt, SUM(affichages) AS Aff", $GLOBALS['_PUBBAN_CONF']['table_stats'], "id_empl=".$emp." AND jour >= ('".$inter_stats."') ".$sep." jour < ('".$jour_stats."')", '', '', '', '', _BDD_PUBBAN);
			}

			elseif($period_perf == 30){
				$inter_stats = date('z', time() - 2592000); 
				$sep = ($inter_stats > $jour_stats) ? 'OR' : 'AND';
				$requete = sql_select("SUM(clics) AS Cpt, SUM(affichages) AS Aff", $GLOBALS['_PUBBAN_CONF']['table_stats'], "id_empl=".$emp." AND jour >= ('".$inter_stats."') ".$sep." jour < ('".$jour_stats."')", '', '', '', '', _BDD_PUBBAN);
			}

			elseif($period_perf == 90){
				$inter_stats = date('z', time() - 7776000); 
				$sep = ($inter_stats > $jour_stats) ? 'OR' : 'AND';
				$requete = sql_select("SUM(clics) AS Cpt, SUM(affichages) AS Aff", $GLOBALS['_PUBBAN_CONF']['table_stats'], "id_empl=".$emp." AND jour >= ('".$inter_stats."') ".$sep." jour < ('".$jour_stats."')", '', '', '', '', _BDD_PUBBAN);
			}

			$array = spip_fetch_array($requete);
			$cpt_clic = $array['Cpt'];
			$cpt_affi = $array['Aff'];
			if($cpt_clic == '' OR $cpt_clic == 0) {
				$n_emp_zero++;
				$n_emp_zero_empl[$emp] = true;
			}
			else {
				$n_emp_zero_empl[$emp] = false;
				$valeurs['clics_perf_'.$n] = $cpt_clic;
				$valeurs['affis_perf_'.$n] = $cpt_affi;
				$valeurs['titre_empl_'.$n] = $emplacement['titre'];
				$valeurs['titre_cut_empl_'.$n] = substr($emplacement['titre'], 0, 3).".";
				$valeurs['n_perf_'.$n] = $n;
				$valeurs['div1js'][$n] = $n;
				$toshow_perf = 1;
				$n++;
			}
		}
		if($n_emp_zero != 0) {
			if($n_emp_zero == $n_emp_verif) {
				$valeurs['js_div1'] = '';
				$valeurs['message_div1'] = _T('pubban:no_clic_in_period');
			}
			else {
				for($i=0; $i<count($n_emp_zero_empl); $i++)
					if($n_emp_zero_empl[$i]) $valeurs['message_div1'] = _T('pubban:no_clic_for_emp');
			}
		}
		if($toshow_perf){
			$valeurs['js_div1'] = 'clic';
			$valeurs['nom_div1'] = 'pieCanvas';
			$valeurs['height_div1'] = '350px';
			$valeurs['width_div1'] = '380px';
		}
	}

	//  Ratio %
	if($type_perf == 'ratio'){
		$recup = sql_select("DISTINCT id_empl", $GLOBALS['_PUBBAN_CONF']['table_join'], '', '', '', '', '', _BDD_PUBBAN);
		$n=1;
		$n_emp_zero = 0;
		$n_emp_verif = 0;
		while($tableau = mysql_fetch_array($recup)){
			$emp = $tableau['id_empl'];
			$emplacement = pubban_recuperer_emplacement($emp);
			$n_emp_verif++;

			if($period_perf==1){
				$requete = sql_select("SUM(clics) AS Cpt", $GLOBALS['_PUBBAN_CONF']['table_stats'], "id_empl=".$emp." AND date IN ('".$date_stats."')", '', '', '', '', _BDD_PUBBAN);
				$requete2 = sql_select("SUM(affichages) AS Affi", $GLOBALS['_PUBBAN_CONF']['table_stats'], "id_empl=".$emp." AND date IN ('".$date_stats."')", '', '', '', '', _BDD_PUBBAN);
			}

			elseif($period_perf==7){
				$inter_stats = date('z', time() - 604800); 
				$sep = ($inter_stats>$jour_stats) ? 'AND' : 'OR';
				$requete = sql_select("SUM(clics) AS Cpt", $GLOBALS['_PUBBAN_CONF']['table_stats'], "id_empl=".$emp." AND jour >= ('".$inter_stats."') ".$sep." jour < ('".$jour_stats."')", '', '', '', '', _BDD_PUBBAN);
				$requete2 = sql_select("SUM(affichages) AS Affi", $GLOBALS['_PUBBAN_CONF']['table_stats'], "id_empl=".$emp." AND jour >= ('".$inter_stats."') OR jour < ('".$jour_stats."')", '', '', '', '', _BDD_PUBBAN);
			}

			elseif($period_perf==30){
				$inter_stats = date('z', time() - 2592000); 
				$sep = ($inter_stats>$jour_stats) ? 'AND' : 'OR';
				$requete = sql_select("SUM(clics) AS Cpt", $GLOBALS['_PUBBAN_CONF']['table_stats'], "id_empl=".$emp." AND jour >= ('".$inter_stats."') ".$sep." jour < ('".$jour_stats."')", '', '', '', '', _BDD_PUBBAN);
				$requete2 = sql_select("SUM(affichages) AS Affi", $GLOBALS['_PUBBAN_CONF']['table_stats'], "id_empl=".$emp." AND jour >= ('".$inter_stats."') OR jour < ('".$jour_stats."')", '', '', '', '', _BDD_PUBBAN);
			}

			elseif($period_perf=="90"){
				$inter_stats = date('z', time() - 7776000); 
				$sep = ($inter_stats>$jour_stats) ? 'AND' : 'OR';
				$requete = sql_select("SUM(clics) AS Cpt", $GLOBALS['_PUBBAN_CONF']['table_stats'], "id_empl=".$emp." AND jour >= ('".$inter_stats."') ".$sep." jour < ('".$jour_stats."')", '', '', '', '', _BDD_PUBBAN);
				$requete2 = sql_select("SUM(affichages) AS Affi", $GLOBALS['_PUBBAN_CONF']['table_stats'], "id_empl=".$emp." AND jour >= ('".$inter_stats."') OR jour < ('".$jour_stats."')", '', '', '', '', _BDD_PUBBAN);
			}
			$array = spip_fetch_array($requete);
			$array2 = spip_fetch_array($requete2);		
			$perf_emp_ratio = ($array2['Affi'] == 0) ? 0 : round($array['Cpt'] / $array2['Affi'] * 100, 1);
			if($perf_emp_ratio == 0) $n_emp_zero++;
			else {
				$valeurs['ratios_perf_'.$n] = $perf_emp_ratio;
				$valeurs['titre_empl_'.$n] = $emplacement['titre'];
				$valeurs['titre_cut_empl_'.$n] = substr($emplacement['titre'], 0, 3).".";
				$valeurs['n_perf_'.$n] = $n;
				$valeurs['div1js_2'][$n] = $n;
				$n++;
				$toshow_perf = 1;
			}
		}
		if($n_emp_zero != 0) {
			if($n_emp_zero == $n_emp_verif) {
				$valeurs['js_div1'] = '';
				$valeurs['message_div1'] = _T('pubban:no_clic_in_period');
			}
			else {
				$valeurs['message_div1'] = _T('pubban:no_clic_for_emp');
			}
		}
		if($toshow_perf){
			$valeurs['js_div1'] = 'ratio';
			$valeurs['nom_div1'] = 'myCanvas';
			$valeurs['height_div1'] = '300px';
			$valeurs['width_div1'] = '400px';
		}
	}

	//  Clic Evolution
	if($type_evo == "clic"){
		$valeurs['div2js'] = array();
		$n = 1;
		$n2verif=0;
		$period_sec_de=8640000;
		$period_sec_a=7869600;
		while($n<=10){
			$period_de=date('z', time() - $period_sec_de); 
			$period_a=date('z', time() - $period_sec_a);
			$sep = ($period_de>$period_a) ? 'OR' : 'AND';
			$requete = sql_select("SUM(clics) AS Cpt, SUM(affichages) AS Aff", $GLOBALS['_PUBBAN_CONF']['table_stats'], "jour >= ('".$period_de."') ".$sep." jour <= ('".$period_a."')", '', '', '', '', _BDD_PUBBAN);
			$array = spip_fetch_array($requete);
			$cpt_clic = (isset($array['Cpt'])) ? $array['Cpt'] : 0;
			$cpt_affi = (isset($array['Aff'])) ? $array['Aff'] : 0;
			if($cpt_clic != 0 AND $n2verif == 0) $n2verif = 1;
			$valeurs['date_debut_'.$n] = date('d/m/y', time() - $period_sec_de);
			$valeurs['date_fin_'.$n] = date('d/m/y', time() - $period_sec_a);
			$valeurs['clics_'.$n] = $cpt_clic;
			$valeurs['affis_'.$n] = $cpt_affi;
			$valeurs['div2js'][$n] = $n;
			$valeurs['n_evo_'.$n] = $n;
			$n++;
			$period_sec_de = $period_sec_de-864000;
			$period_sec_a = $period_sec_a-864000;
		}
		if($n2verif == 0) {
			$valeurs['js_div2'] = '';
			$valeurs['message_div2'] = _T('pubban:no_datas_yet');
		}
		else {
			$valeurs['js_div2'] = 'ok';
			$valeurs['nom_div2'] = 'lineCanvas';
			$valeurs['height_div2'] = '300px';
			$valeurs['width_div2'] = '400px';
		}
	}

	//  Ratio Evolution
	if($type_evo=="ratio"){
		$valeurs['div2js'] = array();
		$n=1;
		$n2verif=0;
		$period_sec_de=8640000;
		$period_sec_a=7869600;
		while($n <= 10){
			$period_de = date('z', time() - $period_sec_de); 
			$period_a = date('z', time() - $period_sec_a);
			$sep = ($period_de > $period_a) ? 'OR' : 'AND';
			$requete = sql_select("SUM(clics) AS Cpt", $GLOBALS['_PUBBAN_CONF']['table_stats'], "jour >= ('".$period_de."') ".$sep." jour <= ('".$period_a."')", '', '', '', '', _BDD_PUBBAN);
			$requete2 = sql_select("SUM(affichages) AS affievo", $GLOBALS['_PUBBAN_CONF']['table_stats'], "jour >= ('".$period_de."') ".$sep." jour <= ('".$period_a."')", '', '', '', '', _BDD_PUBBAN);
			$array = spip_fetch_array($requete);
			$array2 = spip_fetch_array($requete2);
			$cpt_clic = (isset($array['Cpt'])) ? $array['Cpt'] : 0;
			$cpt_affi = (isset($array2['affievo'])) ? $array2['affievo'] : 0;
			if($cpt_clic != 0 AND $n2verif == 0) $n2verif = 1;
			$ratio = ($cpt_affi > 0) ? round($cpt_clic / $cpt_affi * 100, 1) : 0;
			$valeurs['date_debut_'.$n] = date('d/m/y', time() - $period_sec_de);
			$valeurs['date_fin_'.$n] = date('d/m/y', time() - $period_sec_a);
			$valeurs['ratios_'.$n] = $ratio;
			$valeurs['n_evo_'.$n] = $n;
			$valeurs['div2js_2'][$n] = $n;
			$n++;
			$period_sec_de=$period_sec_de-864000;
			$period_sec_a=$period_sec_a-864000;
		}
		if($n2verif == 0) {
			$valeurs['js_div2'] = '';
			$valeurs['message_div2'] = _T('pubban:no_datas_yet');
		}
		else {
			$valeurs['js_div2'] = 'ok';
			$valeurs['nom_div2'] = 'lineCanvas';
			$valeurs['height_div2'] = '300px';
			$valeurs['width_div2'] = '400px';
		}
	}

	return $valeurs;
}
?>
<?php

/**
 * Moteur de rcherche Pubban
 */
function pubban_search($str){
	include_spip('base/abstract_sql');
	$results = array('pub', 'emp');
	$i=0;
	$j=0;

	// Recherche dans les publicites
	$ban_str = 'id_banniere=';
	$ban_max = strlen($ban_str);
	if(substr_count($str, $ban_str) != 0 AND is_numeric(substr($str, $ban_max))) {
		$id_banniere = substr($str, $ban_max);
		$pub = sql_select("id_publicite", 'spip_bannieres_publicites', "id_banniere=".intval($id_banniere), '', '', '', '');
		while ($row = spip_fetch_array($pub)) {
			$results['pub'][$i] = $row['id_publicite'];
			$i++;
		}
	}
	if(is_integer(intval($str)) AND intval($str) != 0){
		$pub = sql_getfetsel("id_publicite", 'spip_publicites', "id_publicite=".intval($str), '', '', '', '');
		if($pub) {
			$results['pub'][$i] = $pub;
			$i++;
		}
		$empl = sql_getfetsel("id_banniere", 'spip_bannieres', "id_banniere=".intval($str), '', '', '', '');
		if($empl) {
			$results['emp'][$j] = $empl;
			$j++;
		}
	}
	else{
		$pub = sql_select("id_publicite", 'spip_publicites', "titre LIKE '%".$str."%'", '', '', '', '');
		if(sql_count($pub) > 0) {
			while($row = spip_fetch_array($pub)){
				$results['pub'][$i] = $row['id_publicite'];
				$i++;
			}
		}
		$empl = sql_select("id_banniere", 'spip_bannieres', "titre LIKE '%".$str."%'", '', '', '', '');
		if(sql_count($empl) > 0) {
			while($row = spip_fetch_array($empl)){
				$results['emp'][$j] = $row['id_banniere'];
				$j++;
			}
		}
	}
	return $results;
}


?>
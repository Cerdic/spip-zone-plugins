<?php

// ----------------------------
// FONCTIONS EMPLACEMENT
// ----------------------------

function pubban_pubs_de_emplacement($id_emp, $toutes=true){
	$list_pub = array();
	$requete = sql_select("id_pub", $GLOBALS['_PUBBAN_CONF']['table_join'], "id_empl=".intval($id_emp), '', '', '', '', _BDD_PUBBAN);
	if (sql_count($requete) > 0) {
		while ($row = spip_fetch_array($requete)) {
			if(!$toutes){
				$statut = pubban_recuperer_pub($row['id_pub'], 'statut');
				if($statut == '2actif')
					$list_pub[] = $row['id_pub'];
			}
			else $list_pub[] = $row['id_pub'];
		}
		sql_free($requete, _BDD_PUBBAN);
		return $list_pub;
	}
	return false;
}

// ----------------------------
// FONCTIONS PUB
// ----------------------------

/**
 * Liste les emplacements dans lesquels la pub est présente
 */
function pubban_emplacements_de_la_pub($id_pub, $id_empl_verif=false){
	$list_emp = array();
	$requete = sql_select("id_empl", $GLOBALS['_PUBBAN_CONF']['table_join'], "id_pub=".intval($id_pub), '', '', '', '', _BDD_PUBBAN);
	if (sql_count($requete) > 0) {
		while ($row = spip_fetch_array($requete)) {
			// On ne passe plus en reference, PHP le fait tout seul
//			array_push(&$list_emp, $row['id_empl']);
			array_push($list_emp, $row['id_empl']);
		}
		sql_free($requete, _BDD_PUBBAN);
		if($id_empl_verif)
			return( in_array($id_empl_verif, $list_emp) );
		return $list_emp;
	}
	return false;
}

function pubban_verifier_pub($datas){

	if($datas['type'] == 'flash') {
		$to_add = '<object onClick=\'clic();\' ';
		if( !substr_count($datas['objet'], $to_add) )
			$datas['objet'] = str_replace('<object ', $to_add, $datas['objet']);
	}

	$dates = false;
	if(_request('droits_ill') == 'oui') {
		$datas['illimite'] = 'oui';
		$dates = true;
	}
	else {
		if(_request('droits_aff'))	{
			if(is_numeric(_request('droits_aff'))) { $datas['affichages_restant'] = intval(_request('droits_aff')); }
		}
		elseif(_request('droits_clic'))	{
			if(is_numeric(_request('droits_clic'))) { $datas['clics_restant'] = intval(_request('droits_clic')); }
		}
		else $dates = true;
	}
	if($dates)	{
		if(_request('droits_dates_fin')) $datas['date_fin'] = _request('droits_dates_fin'); 
		if(_request('droits_dates_debut')) $datas['date_debut'] = _request('droits_dates_debut'); 
		else $datas['date_debut'] = date('Y-m-d');
	}
}


?>
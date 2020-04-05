<?php

/**
 * Recuperation des donnes d'une publicite
 * @param	integer	$id_publicite	L'ID de la pub a recuperer
 * @param	string	$str	Le nom d'un paramètre à récupérer (optionnel)
 * @return array	Les données de la pub (ou la valeur du paramètre si demandé)
 */
function pubban_recuperer_publicite($id_publicite, $str=false) {
	include_spip('base/abstract_sql');
	$vals = array();
	if($id_publicite != '0') {
		$resultat = sql_select("*", 'spip_publicites',"id_publicite=".intval($id_publicite)	, '', '', '', '');
		if (sql_count($resultat) > 0) {
			while ($row=spip_fetch_array($resultat)) {
				$vals['id'] = $id_publicite;
				$vals['id_publicite'] = $id_publicite;
				$vals['type'] = $row['type'];
				$vals['titre'] = $row['titre'];
				$vals['url'] = $row['url'];
				$vals['objet'] = $row['objet'];
				$vals['illimite'] = $row['illimite'];
				$vals['affichages'] = $row['affichages'];
				$vals['clics'] = $row['clics'];
				$vals['affichages_restant'] = $row['affichages_restant'];
				$vals['clics_restant'] = $row['clics_restant'];
				$vals['date_debut'] = $row['date_debut'];
				$vals['date_fin'] = $row['date_fin'];
				$vals['date_add'] = $row['date_add'];
				$vals['statut'] = $row['statut'];
			}
			sql_free($resultat);
		}
		$resultat_empl = sql_select("*", 'spip_bannieres_publicites',"id_publicite=".intval($id_publicite), '', '', '', '');
		if (sql_count($resultat_empl) > 0) {
			while ($row_empl=spip_fetch_array($resultat_empl)) {
				$vals['banniere'][] = $row_empl['id_banniere'];
			}
			sql_free($resultat_empl);
		}
	}
	if($str){
		if( isset($vals[$str]) ) return $vals[$str];
		return false;
	}
	return $vals;
}

/**
 * Liste les bannieres dans lesquelles la pub est présente
 */
function pubban_bannieres_de_la_pub($id_publicite, $id_banniere_verif=false){
	include_spip('base/abstract_sql');
	$list_emp = array();
	$requete = sql_select("id_banniere", 'spip_bannieres_publicites', "id_publicite=".intval($id_publicite), '', '', '', '');
	if (sql_count($requete) > 0) {
		while ($row = spip_fetch_array($requete)) {
			array_push($list_emp, $row['id_banniere']);
		}
		sql_free($requete);
		if($id_banniere_verif)
			return( in_array($id_banniere_verif, $list_emp) );
		return $list_emp;
	}
	return false;
}

function pubban_verifier_pub($data){

	if($data['type'] == 'flash') {
		$to_add = '<object onClick=\'clic();\' ';
		if( !substr_count($data['objet'], $to_add) )
			$data['objet'] = str_replace('<object ', $to_add, $data['objet']);
	}

	$dates = false;
	if(_request('droits_ill') == 'oui') {
		$data['illimite'] = 'oui';
		$dates = true;
	}
	else {
		if(_request('droits_aff'))	{
			if(is_numeric(_request('droits_aff'))) { $data['affichages_restant'] = intval(_request('droits_aff')); }
		}
		elseif(_request('droits_clic'))	{
			if(is_numeric(_request('droits_clic'))) { $data['clics_restant'] = intval(_request('droits_clic')); }
		}
		else $dates = true;
	}
	if($dates)	{
		if(_request('droits_dates_fin')) $data['date_fin'] = _request('droits_dates_fin'); 
		if(_request('droits_dates_debut')) $data['date_debut'] = _request('droits_dates_debut'); 
		else $data['date_debut'] = date('Y-m-d');
	}
}

?>
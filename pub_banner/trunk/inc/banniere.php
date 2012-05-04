<?php

/**
 * Recuperation des donnes d'une banniere
 * @param	integer	$id_banniere	L'ID de la bannière à récuperer
 * @param	string	$str	Le nom d'un paramètre à récupérer (optionnel)
 * @return array	Les données de la banniere (ou la valeur du paramètre si demandé)
 */
function pubban_recuperer_banniere($id_banniere, $str=false) {
	include_spip('base/abstract_sql');
	$vals = array();
	if($id_banniere != '0') {
		$resultat = sql_select("*", 'spip_bannieres',"id_banniere=".intval($id_banniere), '', '', '', '');
		if (sql_count($resultat) > 0) {
			while ($row=spip_fetch_array($resultat)) {
				$vals['id'] = $id_banniere;
				$vals['id_banniere'] = $id_banniere;
				$vals['titre'] = $row['titre'];
				$vals['titre_id'] = $row['titre_id'];
				$vals['width'] = $row['width'];
				$vals['height'] = $row['height'];
				$vals['ratio_pages'] = $row['ratio_pages'];
				$vals['statut'] = $row['statut'];
				$vals['refresh'] = $row['refresh'];
			}
			sql_free($resultat);
		}
	}
	if($str){
		if( isset($vals[$str]) ) return $vals[$str];
		return false;
	}
	return $vals;
}

/**
 * Recuperation de l'ID d'une banniere depuis son nom
 * @param	string	$name	Le nom de la banniere a recuperer
 * @return integer	L'ID recherche
 */
function pubban_recuperer_banniere_par_nom($name) {
	include_spip('base/abstract_sql');

	// Si c'est un "id" on renvoie
	if (is_numeric($name))
		return pubban_recuperer_banniere($name);

	// Par "titre_id"
	$id_banniere = sql_getfetsel("id_banniere", 'spip_bannieres', "titre_id=".sql_quote($name), '', '', '', '');
	if($id_banniere)
		return pubban_recuperer_banniere($id_banniere);

	// Par "titre" (compatibilite)
	$id_banniere = sql_getfetsel("id_banniere", 'spip_bannieres', "titre LIKE ('$name')", '', '', '', '');
	if($id_banniere)
		return pubban_recuperer_banniere($id_banniere);

	// Sinon nada
	return false;
}

function pubban_comparer_bannieres($emp){
	if(!is_array($emp)) return;
	if(count($emp) > 1) {
		$width = $height = array();
		foreach($emp as $k=>$empl){
			$width[] = pubban_recuperer_banniere($empl, 'width');
			$height[] = pubban_recuperer_banniere($empl, 'height');
		}
		if( count(array_unique($width)) != 1 OR 
			count(array_unique($height)) != 1
		) return false;
	}
	return true;
}

function pubban_liste_bannieres($statut=false){
	include_spip('base/abstract_sql');
	$bannieres = array();
	if($statut AND !is_array($statut))
		$statut = array( $statut );
	$where = $statut ? "statut IN ('".join("','", $statut)."')" : '';
	$resultat = sql_select("id_banniere", 'spip_bannieres', $where, '', '', '', '');
	if (sql_count($resultat) > 0) {
		while ($row=spip_fetch_array($resultat)) {
			$bannieres[] = $row['id_banniere'];
		}
	}
	return $bannieres;
}

function pubban_trouver_bannieres($id_publicite){
	if($id_publicite == '0') return;
	include_spip('base/abstract_sql');
	$bannieres = array();
	$resultat = sql_select("*", 'spip_bannieres_publicites', 'id_publicite='.intval($id_publicite), '', '', '', '');
	if (sql_count($resultat) > 0) {
		while ($row=spip_fetch_array($resultat)) {
			$bannieres[] = $row['id_banniere'];
		}
	}
	return $bannieres;
}

function pubban_pubs_de_la_banniere($id_banniere, $toutes=true){
	include_spip('base/abstract_sql');
	include_spip('inc/publicite');
	$list_pub = array();
	$requete = sql_select("id_publicite", 'spip_bannieres_publicites', "id_banniere=".intval($id_banniere), '', '', '', '');
	if (sql_count($requete) > 0) {
		while ($row = spip_fetch_array($requete)) {
			if(!$toutes){
				$statut = pubban_recuperer_publicite($row['id_publicite'], 'statut');
				if($statut == '2actif')
					$list_pub[] = $row['id_publicite'];
			}
			else $list_pub[] = $row['id_publicite'];
		}
		sql_free($requete);
		return $list_pub;
	}
	return false;
}

?>
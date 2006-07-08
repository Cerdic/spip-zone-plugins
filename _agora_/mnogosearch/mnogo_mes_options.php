<?php
define('_MNOGO_LOCAL_CACHE_DELAI',86400);

//
// <BOUCLE(DOCUMENTS)>
//
function boucle_MNOGOSEARCH_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_mnogosearch";
	$boucle->select[] = 'numero';

	$out = calculer_boucle($id_boucle, $boucles);
	$out = str_replace("\$Numrows['$id_boucle']['compteur_boucle']++;","\$Numrows['$id_boucle']['compteur_boucle']=\$Pile[\$SP]['numero'];",$out);

	$partition = calculer_parties($boucles,$id_boucle);

	$partition1 = preg_replace('/(\$nombre_boucle =).*?;/','\\1 10000;',$partition);
	$partition_cor = preg_replace('/(\$nombre_boucle =).*?;/',"\\1 \$GLOBALS['mnogo_resultats_synthese']['MNOGO_TOTAL'];",$partition);
	$partition_cor .= "
	\$Numrows['$id_boucle']['grand_total']=\$GLOBALS['mnogo_resultats_synthese']['MNOGO_TOTAL'];";
	
	$out = $partition1."
	mnogo_checkresults(\$GLOBALS['recherche'],\$debut_boucle,\$fin_boucle-\$debut_boucle+1);
	".str_replace($partition,$partition_cor,$out);
	return $out;
}

// {recherche}
// http://www.spip.net/@recherche
// gestion du cas ou le critere recherche est applique a la boucle MNOGOSEARCH
// sinon renvoi vers la boucle _dist
function critere_recherche($idb, &$boucles, $crit) {
	global $table_des_tables;
	$boucle = &$boucles[$idb];
	if ($boucle->id_table=='mnogosearch'){
		// Ne pas executer la requete en cas de hash vide
		$boucle->hash = "
	// RECHERCHE
	\$rech_where = hash_where(\$GLOBALS['recherche']);
		";
		// et la recherche trouve
		$boucle->where[] = '$rech_where';
		$boucle->order[] = 'numero';
	}
	else
		critere_recherche_dist($idb, $boucles, $crit);
}

function mnogo_formate_recherche($recherche){
	$recherche = trim($recherche);
	$recherche = preg_replace(',\s(ET|AND)\s,',' & ',$recherche);
	$recherche = preg_replace(',\s(OU|OR)\s,',' | ',$recherche);
	$recherche = preg_replace(',\s(?=\s),','',$recherche);
	return $recherche;
}

function mnogo_hash($recherche=NULL){
	if ($recherche==NULL) $recherche = mnogo_formate_recherche(_request('recherche'));
	$h = substr(md5($recherche), 0, 16);
	return $h;
}
function hash_where($recherche=NULL){
	$h = mnogo_hash($recherche);
	
	// Attention en MySQL 3.x il faut passer par HEX(hash)
	// alors qu'en MySQL 4.1 c'est interdit !
	/*$vers = spip_query("SELECT VERSION() AS v");
	$vers = spip_fetch_array($vers);
	if (substr($vers['v'], 0, 1) >= 4
	AND substr($vers['v'], 2, 1) >= 1 )
		return "hash='$h'";
	else*/
	return  "HEX(hash)='$h'";
}

function mnogo_checkresults($recherche, $debut, $nombre){
	if ($recherche!==NULL){
		$res = spip_query("SELECT * FROM spip_mnogosearch_summary WHERE ".hash_where($recherche));
		// verifier que cette recherche a deja ete faite
		$refresh = true;
		if ($row = spip_fetch_array($res)){
			if (time()-strtotime($row['maj'])<_MNOGO_LOCAL_CACHE_DELAI)
				$refresh = false;
		}
		// si oui, verifier que les resultats en memoire correspondent
		if (!$refresh){
			$res = spip_query("SELECT numero FROM spip_mnogosearch WHERE numero>=".spip_abstract_quote($debut)
			." AND numero<=".spip_abstract_quote(min($debut+$nombre,$row['total']))." AND valide='oui' AND ".hash_where($recherche));
			if (spip_num_rows($res)<$nombre)
				$refresh = true;
		}
		if ($refresh){
			include_spip('inc/mnogo_distant');
			mnogo_getresults($recherche,$debut,$nombre);
			$res = spip_query("SELECT * FROM spip_mnogosearch_summary WHERE ".hash_where($recherche));
			$row = spip_fetch_array($res);
		}
		$GLOBALS['mnogo_resultats_synthese']['MNOGO_RESUME_RESULTATS'] = $row['resume_resultats'];
		$GLOBALS['mnogo_resultats_synthese']['MNOGO_TOTAL'] = $row['total'];
	}
	else{
		$GLOBALS['mnogo_resultats_synthese']['MNOGO_RESUME_RESULTATS'] = '';
		$GLOBALS['mnogo_resultats_synthese']['MNOGO_TOTAL'] = '';
	}
	return true;
}

?>
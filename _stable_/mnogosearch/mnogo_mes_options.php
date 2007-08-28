<?php
define('_MNOGO_LOCAL_CACHE_DELAI',86400);

//
// <BOUCLE(DOCUMENTS)>
//
function boucle_MNOGOSEARCH_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_mnogosearch";
	$boucle->select[] = "$id_table.numero";
	$boucle->select[] = "$id_table.points";
	// Ne pas executer la requete en cas de hash vide
	$boucle->hash = "
	// RECHERCHE
	\$liste_args = \$Pile[0];
	\$liste_args['recherche'] = ".$boucle->modificateur['recherche'].";"
	. ((isset($boucle->modificateur['groupbysite']) && $boucle->modificateur['groupbysite'])?"
	\$liste_args['GroupBySite']='yes';
	":"")
	. ((isset($boucle->modificateur['ul']))?"
	\$liste_args['site']= ".$boucle->modificateur['site'].";
	":"")
	. ((isset($boucle->modificateur['tri']))?"
	\$liste_args['s']= ".$boucle->modificateur['tri'].";
	":"")
	. ((isset($boucle->modificateur['section']))?"
	\$liste_args['su']= ".$boucle->modificateur['section'].";
	":"")
	. ((isset($boucle->modificateur['recherche_sections']))?"
	\$liste_args['wf']= ".$boucle->modificateur['recherche_sections'].";
	":"")
	. "mnogo_checkresults(\$liste_args['recherche'],\$debut_boucle,\$fin_boucle-\$debut_boucle+1, \$liste_args);
	\$rech_where = hash_where(\$liste_args['recherche'],\$liste_args);
		";
	$out = calculer_boucle($id_boucle, $boucles);
	$out = str_replace("\$Numrows['$id_boucle']['compteur_boucle']++;","\$Numrows['$id_boucle']['compteur_boucle']=\$Pile[\$SP]['numero'];",$out);

	$partition = calculer_parties($boucles,$id_boucle);

	$partition1 = preg_replace('/(\$nombre_boucle =).*?;/','\\1 10000;',$partition);
	$partition_cor = preg_replace('/(\$nombre_boucle =).*?;/',"\\1 \$GLOBALS['mnogo_resultats_synthese']['MNOGO_TOTAL'];",$partition);
	$partition_cor .= "
	\$Numrows['$id_boucle']['grand_total']=\$GLOBALS['mnogo_resultats_synthese']['MNOGO_TOTAL'];";
	
	$out = $partition1 . str_replace($partition,$partition_cor,$out);
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
		if (isset($crit->param[0]))
			$quoi = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
		else
			$quoi = '@$Pile[0]["recherche"]';
		$boucle->modificateur['recherche'] = $quoi;
		// le hash sera mis dans la fonction boucle en tenant compte d'eventuels autres modificateurs
		// et la recherche trouve
		$boucle->where[] = '$rech_where';
		$boucle->order[] = 'numero';
	}
	else
		critere_recherche_dist($idb, $boucles, $crit);
}

function critere_groupbysite_dist($idb, &$boucles, $crit) {
	global $table_des_tables;
	$boucle = &$boucles[$idb];
	if ($boucle->id_table=='mnogosearch'){
		$boucle->modificateur['groupbysite'] = true;
	}
}
function critere_sur_le_site_dist($idb, &$boucles, $crit) {
	global $table_des_tables;
	$boucle = &$boucles[$idb];
	if ($boucle->id_table=='mnogosearch'){
		if (isset($crit->param[0])){
			$quoi = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
			$boucle->modificateur['ul'] = $quoi;
		}
	}
}
function critere_tri_dist($idb, &$boucles, $crit) {
	global $table_des_tables;
	$boucle = &$boucles[$idb];
	if ($boucle->id_table=='mnogosearch'){
		if (isset($crit->param[0])){
			$quoi = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
			$boucle->modificateur['tri'] = $quoi;
		}
	}
}
function critere_section_dist($idb, &$boucles, $crit) {
	global $table_des_tables;
	$boucle = &$boucles[$idb];
	if ($boucle->id_table=='mnogosearch'){
		if (isset($crit->param[0])){
			$quoi = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
			$boucle->modificateur['section'] = $quoi;
		}
	}
}
function critere_recherche_sections_dist($idb, &$boucles, $crit) {
	global $table_des_tables;
	$boucle = &$boucles[$idb];
	if ($boucle->id_table=='mnogosearch'){
		if (isset($crit->param[0])){
			$quoi = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
			$boucle->modificateur['recherche_sections'] = $quoi;
		}
	}
}


function mnogo_querystring($recherche,$debut,$nombre, $c=false){
	$default_qs=array('q'=>'','m'=>'bool','wm'=>'wrd','sp'=>1,'sy'=>1,'wf'=>'2221','type'=>'','ul'=>'','fmt'=>'xml','np'=>0,'ps'=>10,'GroupBySite'=>'no');
	$key_translate = array('recherche'=>'q','site'=>'ul');
	if (isset($GLOBALS['mnogosearch_default_qs']))
		$default_qs = $GLOBALS['mnogosearch_default_qs'];

	$key_translate = array_flip($key_translate);
	foreach($default_qs as $key=>$value){
		$k = $key;
		if (isset($key_translate[$key]))
			$k = $key_translate[$key];
		if (($value = _request($k,$c))!==NULL)
			$default_qs[$key] = $value;
	}
	$default_qs['fmt'] = 'xml'; // obligatoire
	// remplacer les operateurs ET,AND,OR,OU par leur forme & |
	$default_qs['q'] = urlencode(mnogo_formate_recherche(urldecode($recherche)));
	
	// gerer les pages
	$default_qs['ps'] = max(100,$nombre);
	$default_qs['np'] = (int)floor($debut/$default_qs['ps']);

	$req = "";
	foreach($default_qs as $key=>$value)
		$default_qs[$key]=$key."=".$value;
	return implode("&",$default_qs);
}

function mnogo_formate_recherche($recherche){
	$recherche = trim($recherche);
	$recherche = preg_replace(',\s(ET|AND)\s,',' & ',$recherche);
	$recherche = preg_replace(',\s(OU|OR)\s,',' | ',$recherche);
	$recherche = preg_replace(',\s(PAS|NOT)\s,',' ~ ',$recherche);
	$recherche = preg_replace(',\s(?=\s),','',$recherche);
	return $recherche;
}

function mnogo_hash($recherche=NULL, $c=false){
	if ($recherche==NULL) $recherche = _request('recherche',$c);
	$query = mnogo_querystring($recherche, 0,10, $c);
	$h = substr(md5($query), 0, 16);
	return $h;
}
function hash_where($recherche=NULL, $c=false){
	$h = mnogo_hash($recherche, $c);
	
	// Attention en MySQL 3.x il faut passer par HEX(hash)
	// alors qu'en MySQL 4.1 c'est interdit !
	$vers = spip_query("SELECT VERSION() AS v");
	$vers = spip_fetch_array($vers);
	if ((substr($vers['v'], 0, 1) >= 4
		AND substr($vers['v'], 2, 1) >= 1 )
		OR substr($vers['v'], 0, 1) >= 5)
		return  "HEX(hash)='$h'";
	else
		return "hash=0x$h";
}

function mnogo_checkresults($recherche, $debut, $nombre, $c=false){
	if ($recherche!==NULL){
		$hashwhere = hash_where($recherche, $c);
		$res = spip_query("SELECT * FROM spip_mnogosearch_summary WHERE $hashwhere");
		// verifier que cette recherche a deja ete faite
		$refresh = true;
		if ($row = spip_fetch_array($res)){
			if (time()-strtotime($row['maj'])<_MNOGO_LOCAL_CACHE_DELAI)
				$refresh = false;
		}
		// si oui, verifier que les resultats en memoire correspondent
		if (!$refresh){
			$res = spip_query("SELECT numero FROM spip_mnogosearch WHERE numero>=".spip_abstract_quote($debut)
			." AND numero<=".spip_abstract_quote(min($debut+$nombre,$row['total']))." AND valide='oui' AND $hashwhere");
			if (spip_num_rows($res)<$nombre)
				$refresh = true;
		}
		if ($refresh){
			include_spip('inc/mnogo_distant');
			mnogo_getresults($recherche,$debut,$nombre, $c);
			$res = spip_query("SELECT * FROM spip_mnogosearch_summary WHERE $hashwhere");
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
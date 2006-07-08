<?php

function mnogo_install(){
	mnogo_verifier_base();
}

function mnogo_uninstall(){
}

function mnogo_verifier_base(){
	$version_base = 0.10;
	$current_version = 0.0;
	if (   (isset($GLOBALS['meta']['mnogo_base_version']) )
			&& (($current_version = $GLOBALS['meta']['mnogo_base_version'])==$version_base))
		return;

	include_spip('base/mnogo_base');
	if ($current_version==0.0){
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
		ecrire_meta('mnogo_base_version',$current_version=$version_base);
	}
	ecrire_metas();
}
	
function mnogo_querystring($recherche,$debut,$nombre){
	$default_qs=array('q'=>'','m'=>'bool','wm'=>'wrd','sp'=>1,'sy'=>1,'wf'=>'2221','type'=>'','ul'=>'','fmt'=>'xml','np'=>0,'ps'=>10,'GroupBySite'=>'no');
	$key_translate = array('recherche'=>'q','site'=>'ul');

	foreach($_REQUEST as $key=>$value){
		if (isset($key_translate[$key]))
			$key = $key_translate[$key];
		if (isset($default_qs[$key]))
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

function mnogo_getresults($recherche, $debut, $nombre){
	global $tables_principales;
	global $mnogo_resultats_synthese;
	global $mnogo_resultats;
	$url = isset($GLOBALS['meta']['mnogo_url_search'])?$GLOBALS['meta']['mnogo_url_search']:"";
	$qs = mnogo_querystring($recherche,$debut,$nombre);
	$url .= (strpos($url,"?")!==FALSE)?"&$qs":"?$qs";

	$arbre = array();
	include_spip('inc/distant');
	$contenu = recuperer_page($url);
	if ($contenu){
		include_spip('inc/plugin');
		$arbre = parse_plugin_xml($contenu);
	}
	include_spip('inc/date');
	if (isset($arbre['recherche'][0])){
		$total = applatit_arbre($arbre['recherche'][0]['balise_MNOGO_TOTAL']);
		$premier = intval(applatit_arbre($arbre['recherche'][0]['balise_MNOGO_PREMIER']));
		$dernier = intval(applatit_arbre($arbre['recherche'][0]['balise_MNOGO_DERNIER']));
		$resume = applatit_arbre($arbre['recherche'][0]['balise_MNOGO_RESUME_RESULTATS']);
		// regarder si le resume etait la et a change
		$res = spip_query("SELECT * FROM spip_mnogosearch_summary WHERE ".hash_where($recherche));
		if ($row = spip_fetch_array($res)){
			$changed = false;
			$changed = $changed OR ($row['total']!=$total);
			$changed = $changed OR ($row['resume_resultats']!=$resume);
			if ($changed){
				spip_query("UPDATE FROM spip_mnogosearch SET valide='non' WHERE ".hash_where($recherche));
				spip_query("UPDATE FROM spip_mnogosearch_summary SET resume_resultats=".spip_abstract_quote($resume).", total=".spip_abstract_quote($total).", maj=NOW(), WHERE ".hash_where($recherche));
			}
		}
		else
			spip_query("INSERT INTO spip_mnogosearch_summary SET resume_resultats=".spip_abstract_quote($resume).", total=".spip_abstract_quote($total).", maj=NOW(), hash=0x".mnogo_hash($recherche));

		if (isset($arbre['recherche'][0]['resultats'][0]['resultat'][$dernier-$premier])){
			$hashwere = hash_where();
			$value['hash'] = "0x".mnogo_hash($recherche);
			foreach ($arbre['recherche'][0]['resultats'][0]['resultat'] as $key=>$liste){
				$value['numero'] = $key+$premier;
				$value['titre'] = spip_abstract_quote(applatit_arbre($liste['balise_MNOGO_ITEM_TITRE']));
				$value['points'] = intval(applatit_arbre($liste['balise_MNOGO_ITEM_POINTS']));
				$value['url'] = spip_abstract_quote(applatit_arbre($liste['balise_MNOGO_ITEM_URL']));
				$value['popularite'] = intval(10000*applatit_arbre($liste['balise_MNOGO_ITEM_POPULARITE']));
				$value['descriptif'] = spip_abstract_quote(applatit_arbre($liste['balise_MNOGO_ITEM_DESCRIPTIF']));
				$value['taille'] = intval(applatit_arbre($liste['balise_MNOGO_ITEM_TAILLE']));
				$value['mime_type'] = spip_abstract_quote(applatit_arbre($liste['balise_MNOGO_ITEM_TYPE']));
				$d = applatit_arbre($liste['balise_MNOGO_ITEM_DATE']);
				$d = strtotime($d);
				$value['date'] = spip_abstract_quote(format_mysql_date(date('Y',$d), date('m',$d), date('d',$d), date('h',$d), date('i',$d),date('s',$d)));
				$value['cache_url'] = spip_abstract_quote(applatit_arbre($liste['balise_MNOGO_ITEM_CACHE_URL']));
				$value['valide'] = spip_abstract_quote('oui');
				
				spip_query("REPLACE INTO spip_mnogosearch (".implode(',',array_keys($value)).") VALUES (".implode(',',$value).")");
			}
		}
	}
}

?>
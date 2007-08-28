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
	

function mnogo_getresults($recherche, $debut, $nombre, $c=false){
	global $tables_principales;
	global $mnogo_resultats_synthese;
	global $mnogo_resultats;
	$url = isset($GLOBALS['meta']['mnogo_url_search'])?$GLOBALS['meta']['mnogo_url_search']:"";
	$qs = mnogo_querystring($recherche,$debut,$nombre,$c);
	$url .= (strpos($url,"?")!==FALSE)?"&$qs":"?$qs";
	$hashwere = hash_where($recherche, $c);
	$hash = mnogo_hash($recherche, $c);


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
		$res = spip_query("SELECT * FROM spip_mnogosearch_summary WHERE $hashwere");
		if ($row = spip_fetch_array($res)){
			$changed = false;
			$changed = $changed OR ($row['total']!=$total);
			$changed = $changed OR ($row['resume_resultats']!=$resume);
			if ($changed){
				spip_query("UPDATE FROM spip_mnogosearch SET valide='non' WHERE $hashwere");
				spip_query("UPDATE FROM spip_mnogosearch_summary SET resume_resultats=".spip_abstract_quote($resume).", total=".spip_abstract_quote($total).", maj=NOW(), WHERE $hashwere");
			}
		}
		else
			spip_query("INSERT INTO spip_mnogosearch_summary SET resume_resultats=".spip_abstract_quote($resume).", total=".spip_abstract_quote($total).", maj=NOW(), hash=0x$hash");

		if (isset($arbre['recherche'][0]['resultats'][0]['resultat'][$dernier-$premier])){
			$value['hash'] = "0x".$hash;
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
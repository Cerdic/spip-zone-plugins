<?php

function codes_postaux_recherche_code($code)
{
	include_spip('base/abstract_sql');
	$where='';
	$items=sql_allfetsel('distinct id_code_postal as id,trim(code) as label','spip_codes_postaux','code like '.sql_quote(strtoupper($code).'%'));
	return $items;
}

function codes_postaux_recherche_commune($code){
	include_spip('inc/plugin');
	if(in_array('COG',array_keys(liste_plugin_actifs())))
		$items = sql_allfetsel('distinct id_code_postal as id,trim(cp.code) as label, concat(\'cog\',c.id_cog_commune) as id_cog_commune,trim(concat(MID(c.article,2,LENGTH(c.article_majuscule)-2),concat(\' \',c.nom))) as ville','spip_codes_postaux cp, spip_cog_communes_liens cl, spip_cog_communes c','cl.id_objet=cp.id_code_postal and cl.objet=\'code_postal\' and c.id_cog_commune=cl.id_cog_commune and ( c.nom_majuscule like '.sql_quote(strtoupper($code).'%').' or concat(MID(c.article_majuscule,2,LENGTH(c.article_majuscule)-2),concat(\' \',c.nom_majuscule)) like '.sql_quote(strtoupper($code).'%').' or cp.code like '.sql_quote(strtoupper($code).'%').')');
	else
		$items = sql_allfetsel('distinct id_code_postal as id,trim(code) as label,trim(titre) as ville','spip_codes_postaux cp','titre like '.sql_quote(strtoupper($code).'%').' or code like '.sql_quote(strtoupper($code).'%'));
	return $items;
}

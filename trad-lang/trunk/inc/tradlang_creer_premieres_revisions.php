<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de création des premières révisions 
 * 
 * @param array $module
 * 		Les informations du module en base (on a besoin des champs "module","lang_mere")
 * @param string $lang
 * 		La langue dans laquelle on souhaite créer la nouvelle version 
 */
function inc_tradlang_creer_premieres_revisions_dist($module=false,$lang=false,$nb=false){
	include_spip('inc/revisions');
	spip_timer('genie_tradlang');
	$count = 0;
	spip_log($module,'revisions_cron');
	if ($versionnes = liste_champs_versionnes('spip_tradlangs')) {
		$where = 'b.id_objet IS NULL';
		$where .= (isset($module) AND $module) ? ' AND a.module = '.sql_quote($module) : '';
		$where .= (isset($lang) AND $lang) ? ' AND a.lang = '.sql_quote($lang) : '';
		if(isset($nb) && is_numeric($nb) && $nb > 1)
			$nb = $nb;
		else
			$nb = 400;
		
		$tradlangs_sans_revisions = sql_select('a.id_tradlang','spip_tradlangs AS a LEFT JOIN spip_versions AS b ON b.objet = "tradlang" AND b.id_objet = a.id_tradlang',$where,'','a.id_tradlang DESC','0,'.$nb);
		while($tradlang = sql_fetch($tradlangs_sans_revisions)){
			$id_version = verifier_premiere_revision('spip_tradlangs', 'tradlang', $tradlang['id_tradlang'], $versionnes, -1);
			$count++;
		}
	}
	$blam = spip_timer('genie_tradlang');
	spip_log($blam,'revisions_cron');
	return $count;
}

?>
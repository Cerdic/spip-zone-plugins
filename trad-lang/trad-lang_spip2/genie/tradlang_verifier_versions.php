<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function genie_tradlang_verifier_versions_dist($t) {
	include_spip('inc/revisions');
	spip_timer('genie_tradlang');
	if ($versionnes = liste_champs_versionnes('spip_tradlangs')) {
		$tradlangs_sans_revisions = sql_select('a.id_tradlang','spip_tradlangs AS a LEFT JOIN spip_versions AS b ON b.objet = "tradlang" AND b.id_objet = a.id_tradlang','b.id_objet IS NULL','','a.id_tradlang DESC','0,100');
		$count = 0;
		while($tradlang = sql_fetch($tradlangs_sans_revisions)){
			$id_version = verifier_premiere_revision('spip_tradlangs', 'tradlang', $tradlang['id_tradlang'], $versionnes, -1);
			spip_log('CRON : Création de la première révision de tradlang '.$tradlang['id_tradlang'].' id_version = '.$id_version,'revisions_cron');
			spip_log($count,'revisions_cron');
			$count++;
		}
	}
	$blam = spip_timer('genie_tradlang');
	spip_log($blam,'revisions_cron');
	return 0;
}
?>
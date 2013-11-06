<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_WP2SPIP_DUMP ($p) {
	spip_log('wp2spip dump Start');
	$wp2spip=recuperer_fond('wp2spip');
	$ou='wp2spip.xml';
	if(!is_dir(_DIR_DUMP)){
		include_spip('inc/flock');
		sous_repertoire(_DIR_DUMP);	
	}
	ecrire_fichier(_DIR_DUMP . $ou, $wp2spip);
	spip_log('wp2spip dump Done');
	$p->code = "'Base Wordpress convertie au format xml SPIP 2.1.12  !<br/><br/>Rendez-vous &agrave; la page <a href=\'?exec=admin_tech\' title=\'\'>Maintenance</a> du site et importer la base wp2spip.xml<br/><br/>Attention cette restauration remplacera votre base SPIP actuelle!!'";
	return $p;
}
?>
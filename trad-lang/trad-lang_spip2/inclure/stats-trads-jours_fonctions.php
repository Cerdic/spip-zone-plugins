<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('prive/squelettes/inclure/stats-visites-data_fonctions');

function stats_tradlang_total($serveur='',$options=array()){
	$where = array(
		'objet = "tradlang"',
		'id_version > 0'
	);

	if(!isset($options['id_auteur']) OR !is_numeric($options['id_auteur']))
		$where[] = 'id_auteur > 0';
	else
		$where[] = 'id_auteur = '.intval($options['id_auteur']);

	$where = implode(" AND ",$where);
	$format = ($unite=='jour'?'%Y-%m-%d':'%Y-%m-01');
	$row = sql_fetsel("COUNT(*) AS total_absolu", "spip_versions",$where,'','','','',$serveur);
	return $row ? $row['total_absolu'] : 0;
}
?>

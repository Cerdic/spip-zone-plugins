<?php

include_spip('base/abstract_sql');


//configure la base spip et les metas
function archive_install() {
	$ok = false;
	//version en cours
	$archive_version = 0.4;
	
	//v�rifie si le plugin est initialis� ou non
	if ((!isset($GLOBALS['meta']['archive_version'])) || $GLOBALS['meta']['archive_version'] < $archive_version) {
		//recupere les champs de spip_articles
		$desc = spip_abstract_showtable("spip_articles", '', true);
		//ajoute le champ archive si champ inexistant
		if (!isset($desc['field']['archive'])){
				spip_query("ALTER TABLE spip_articles ADD `archive` BOOL AFTER `statut`");
		}
		//ajoute le champ archive_date si champ inexistant
		if (!isset($desc['field']['archive_date'])){
				spip_query("ALTER TABLE spip_articles ADD `archive_date` DATETIME AFTER `archive`");
		}
		//on pr�cise que le plugin est initialis� donc base de donn�e modifi�e
		ecrire_meta('archive_version',$archive_version);
		//regenere le cache des metas
		ecrire_metas();
		//retourne que tout ok
		$ok = true;
	}
	return $ok;
}


//apporte les modifications sur la base spip
function archive_uninstall() {
	$ok = false;
	//v�rifie si le plugin est initialis� ou non
	if (isset($GLOBALS['meta']['archive_version'])) {
		//ajoute le champ archive si le plugin n'est pas initialise
		$desc = spip_abstract_showtable("spip_articles", '', true);
		if (isset($desc['field']['archive'])){
				spip_query("ALTER TABLE spip_articles DROP `archive`");
		}
		if (isset($desc['field']['archive_date'])){
				spip_query("ALTER TABLE spip_articles DROP `archive_date`");
		}
		//on pr�cise que le plugin est initialis� donc base de donn�e modifi�e
		effacer_meta('archive_version');
		//regenere le cache des metas
		ecrire_metas();
		//retourne que tout ok
		$ok = true;
	}
	return $ok;
}

?>
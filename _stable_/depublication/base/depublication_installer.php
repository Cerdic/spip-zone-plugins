<?php

$current_version = 0.1;

function depublication_upgrade() {
	
	$version_base = $GLOBALS['meta']['depublication_version'];
	$current_version = 0.0;
	
	// Si la version installee est la derniere en date, on ne fait rien
	if ( (isset($GLOBALS['meta']['depublication_version']) ) && ($current_version == $version_base))
		return;
	
	
	$sql = "CREATE TABLE IF NOT EXISTS  `spip_articles_depublication` (
				`id` BIGINT( 21 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`id_article` BIGINT( 21 ) NOT NULL ,
				`depublication` DATETIME NOT NULL,
				`statut` VARCHAR(255) NULL
			)";
			
	spip_query($sql);
	
	// on enregistre le plugin dans la table spip_meta
	ecrire_meta('depublication_version',$version);
	ecrire_metas();

}



//supprime les données depuis la table spip_auteurs_ajouts
function depublication_vider_tables() {
	effacer_meta('depublication_version');
	ecrire_metas();
	spip_query("drop table `spip_articles_depublication`");
}


function depublication_articles() {

	$result = spip_query('select depublication, id_article, statut from spip_articles_depublication');
	while ($row = spip_fetch_array($result)) {
		$date = $row['depublication'];
		$id_article = $row['id_article'];
		
		if ($row['statut'] != 'depublie' && (strtotime($date) < strtotime(date('Y-m-d H:i:s'))) ) {
			// on dépublie l'article automatiquement
			spip_query('update spip_articles set statut="refuse" where id_article='.$id_article);
			spip_query('update spip_articles_depublication set statut="depublie" where id_article='.$id_article);
		}
	}

}


	
function depublication_install($action) {
		$version_base = $GLOBALS['meta']['depublication_version'];
		
		switch ($action) {
			case 'test':
 				return (isset($GLOBALS['meta']['depublication_version']) AND ($GLOBALS['meta']['depublication_version']>=$version_base));
				break;
			case 'install':
				depublication_upgrade();
				exit();
				break;
			case 'uninstall':
				depublication_vider_tables();
				break;
		}
	}
?>
<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Installation/maj du plugin
 *
 * Crée les champs archive_date sur les articles et sur les rubriques
 * 
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function archive_upgrade($nom_meta_base_version,$version_cible){

	$maj = array();
	
	$maj['create'] = array(
		array('maj_tables',array('spip_articles','spip_rubriques'))
	);
	
	$maj['0.2.0'] = array(
		array('maj_tables',array('spip_articles')),
		array('maj_archives')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Désinstallation du plugin
 * 
 * Supprime les champs archive_date des articles et des rubriques
 * 
 * @param string $nom_meta_base_version
 */
function archive_vider_tables($nom_meta_base_version) {
	sql_alter('TABLE spip_articles DROP COLUMN archive_date');
	sql_alter('TABLE spip_rubriques DROP COLUMN archive_date');
	effacer_meta('archive');
	effacer_meta($nom_meta_base_version);
}

/**
 * Mettre à jour les archives avec le champ archive à 1 vers le statut archive
 */
function maj_archives(){
	$archives = sql_allfetsel('id_article','spip_articles','archive=1');
	if(is_array($archives) && count($archives) > 0){
		foreach($archives as $archive){
			$id_article = $archive['id_article'];
			$modifs = array('statut' => 'archive');
			$modif = article_modifier($id_article,$modifs);
		}
	}
}
?>

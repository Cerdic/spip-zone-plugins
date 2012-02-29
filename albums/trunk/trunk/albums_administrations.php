<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// INSTALLATION
function albums_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_albums')),
		array('maj_tables', array('spip_albums_liens')),
	);

	// config : valeurs par defaut
	include_spip('inc/config');
	ecrire_config('albums/objets', array('spip_articles'));
	ecrire_config('albums/afficher_champ_categorie', ''); #desactive par defaut
	ecrire_config('albums/afficher_champ_descriptif', 'on');
	
	// si besoin, ajoute les albums a la liste des objets pour les documents-joints
	if ($documents_objets = lire_config('documents_objets')
		AND strpos($documents_objets, 'spip_albums,') === false 
	) {
		$documents_objets_avec_albums = $documents_objets . 'spip_albums,';
		ecrire_config('documents_objets', $documents_objets_avec_albums);
	}

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

// DESINSTALLATION
function albums_vider_tables($nom_meta_base_version) {

	// supression des tables
	sql_drop_table("spip_albums");
	sql_drop_table("spip_albums_liens");
	
	// suppression meta & config
	effacer_meta($nom_meta_base_version);
	effacer_config('albums');
	
	// si besoin, retire les albums de la liste des objets pour les documents-joints
	if ($documents_objets = lire_config('documents_objets')
		AND strpos($documents_objets, 'spip_albums,') === true 
	) {
		$documents_objets_sans_albums = str_replace('spip_albums,', '', $documents_objets);
		ecrire_config('documents_objets', $documents_objets_sans_albums);
	}
	
	// suppression des liens des documents lies aux albums
	if (sql_countsel("spip_documents_liens", "objet='album'")){
		sql_delete("spip_documents_liens", "objet='album'"); # variables sql_delete : $from $where ...
	}
}

?>

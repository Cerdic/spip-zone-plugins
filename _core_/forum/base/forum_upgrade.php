<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Installation/maj des tables forum
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function forum_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		
		if ($current_version==0.0){
			include_spip('base/forum');
			include_spip('base/create');
			// creer les tables
			creer_base();
			// mettre les metas par defaut
			$config = charger_fonction('config','inc');
			$config();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
		}
	}
}

/**
 * Desinstallation/suppression des tables forum
 *
 * @param string $nom_meta_base_version
 */
function forum_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_forum");
	sql_drop_table("spip_mots_forum");
	
	effacer_meta("mots_cles_forums");
	effacer_meta("forums_titre");
	effacer_meta("forums_texte");
	effacer_meta("forums_urlref");
	effacer_meta("forums_afficher_barre");
	effacer_meta("formats_documents_forum");
	effacer_meta("forums_publics");
	effacer_meta("forum_prive");
	effacer_meta("forum_prive_objets");
	effacer_meta("forum_prive_admin");

	effacer_meta($nom_meta_base_version);
}

?>
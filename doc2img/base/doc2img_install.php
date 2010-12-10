<?php

/**
 * Plugin doc2img
 * Installation / désinstallation du plugin
 */

/**
 *  Effectue l'ensemble des actions nécessaires au bon fonctionnement du plugin :
 *  - mise en place de la table doc2img
 *  - configuration par défaut de cfg
 *  - definition de la version en cours du plugin
 *
 * @param $nom_meta_base_version Le nom de la meta d'installation
 * @param $version_cible La version actuelle du plugin
 */
function doc2img_upgrade($nom_meta_base_version,$version_cible){

	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	$current_version = 0.0;

	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
		|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){

		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/create');
			include_spip('inc/flock');
			// A la première installation on crée les tables
			creer_base();

			// Creation du répertoire de destination pour compat
			$dir_doc2img = _DIR_IMG.'doc2img/';
			sous_repertoire(_DIR_IMG, 'doc2img');

            // Insertion d'une première configuration
            if(!is_array(lire_config('doc2img'))){
	            $cfg = array(
	                "format_document" => "pdf,bmp,tiff",
	                "repertoire_cible" => "doc2img",
	                "format_cible" => "png",
	                "proportion" => "on"
	            );
				ecrire_meta('doc2img',serialize($cfg));
            }

			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if (version_compare($current_version,'0.3','<')){
            //définition des paramètres de base
			if(!is_array(lire_config('doc2img'))){
	            $cfg = array(
	                "format_document" => "pdf,bmp,tiff",
	                "repertoire_cible" => "doc2img",
	                "format_cible" => "png",
	                "proportion" => "on"
	            );
				ecrire_meta('doc2img',serialize($cfg));
			}
			ecrire_meta($nom_meta_base_version,$current_version='0.3','non');
		}
		if (version_compare($current_version,'0.5','<')){
		    //on permet la numérotation des page
            sql_alter(
				"TABLE spip_doc2img
	                ADD page INTEGER NOT NULL DEFAULT 0;"
			);
			ecrire_meta($nom_meta_base_version,$current_version='0.5','non');
		}
		if (version_compare($current_version,'0.9','<')){
		    sql_query(
		        "CREATE UNIQUE INDEX document ON spip_doc2img (id_document,page)"
		    );
			ecrire_meta($nom_meta_base_version,$current_version='0.9','non');
		}
		if (version_compare($current_version,'0.92','<')){
            sql_alter(
                "TABLE spip_doc2img
                    ADD largeur INT"
            );
            sql_alter(
                "TABLE spip_doc2img
                    ADD hauteur INT"
            );
            sql_alter(
                "TABLE spip_doc2img
                    ADD taille INT"
            );
			ecrire_meta($nom_meta_base_version,$current_version='0.92','non');
		}
	}
}

// Supprimer les éléments du plugin
function doc2img_vider_tables($nom_meta_base_version) {
	include_spip('base/abstract_sql');
	sql_query("DROP TABLE spip_doc2img");
	effacer_meta('doc2img');
	effacer_meta($nom_meta_base_version);
}
?>
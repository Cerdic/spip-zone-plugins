<?php

/**
 * Plugin doc2img
 * Installation / désinstallation du plugin
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

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
	include_spip('base/create');
	include_spip('inc/config');
	$current_version = 0.0;

	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
		|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){

		if (version_compare($current_version,'0.0','<=')){
			
			// A la première installation on crée les tables
			maj_tables('spip_documents');

            // Insertion d'une première configuration
            if(!is_array(lire_config('doc2img'))){
	            $cfg = array(
	                "format_document" => "pdf,bmp,tiff",
	            	"resolution" => "150",
	                "repertoire_cible" => "doc2img",
	                "format_cible" => "png",
	                "proportion" => "on"
	            );
				ecrire_meta('doc2img',serialize($cfg));
            }
            
			if (class_exists('Imagick')) {
				if(!is_array($formats = lire_config('doc2img_imagick_extensions'))){
					$imagick = new Imagick();
					$formats = $imagick->queryFormats();
					ecrire_meta('doc2img_imagick_extensions',serialize($formats));
				}
			}

			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if (version_compare($current_version,'0.3','<')){
            //définition des paramètres de base
			if(!is_array(lire_config('doc2img'))){
	            $cfg = array(
	                "format_document" => "pdf,bmp,tiff",
	            	"resolution" => "150",
	                "repertoire_cible" => "doc2img",
	                "format_cible" => "png",
	                "proportion" => "on"
	            );
				ecrire_meta('doc2img',serialize($cfg));
			}
			ecrire_meta($nom_meta_base_version,$current_version='0.3','non');
		}
		if (version_compare($current_version,'0.93','<')){
			if (class_exists('Imagick')) {
				if(!is_array($formats = lire_config('doc2img_imagick_extensions'))){
					$imagick = new Imagick();
					$formats = $imagick->queryFormats();
					ecrire_meta('doc2img_imagick_extensions',serialize($formats));
				}
			}
			ecrire_meta($nom_meta_base_version,$current_version='0.93','non');
		}
		if (version_compare($current_version,'0.94','<')){
			/**
			 * Transformation des anciens doc2img en documents normaux
			 */
			maj_tables('spip_documents');
			doc2img_update_to_docs();
			ecrire_meta($nom_meta_base_version,$current_version='0.94','non');
			//sql_query("DROP TABLE spip_doc2img");
		}
	}
}

function doc2img_update_to_docs(){
	include_spip('inc/documents');
	$doc2imgs = sql_select('*','spip_doc2img');
	$ajouter_documents = charger_fonction('ajouter_documents', 'action');
	include_spip('action/editer_document');
	while($doc2img = sql_fetch($doc2imgs)){
		/**
		 * On déplace le document dans la table des documents
		 */
		$id_document = $doc2img['id_document'];
		$files = array(array('tmp_name'=>get_spip_doc($doc2img['fichier']),'name'=>basename(get_spip_doc($doc2img['fichier']))));
		$x = $ajouter_documents('new', $files,'document', $id_document, 'doc2img');
		if(intval($x)){
			/**
			 * Si on a un document :
			 * - on ajoute le numéro de page dans spip_documents 
			 * - on supprime le document en base
			 * - on supprime le fichier physique
			 */
			document_set($x,array("page" => $doc2img['page']));
			sql_delete('spip_doc2img','id_document='.intval($id_document).' AND fichier='.sql_quote($doc2img['fichier']));
			spip_unlink(get_spip_doc($doc2img['fichier']));
		}
	}
}
// Supprimer les éléments du plugin
function doc2img_vider_tables($nom_meta_base_version) {
	include_spip('base/abstract_sql');
	sql_drop_table('spip_doc2img');
	effacer_meta('doc2img');
	effacer_meta($nom_meta_base_version);
}
?>
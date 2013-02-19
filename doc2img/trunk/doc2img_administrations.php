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
function doc2img_upgrade($nom_meta_base_version, $version_cible){
	include_spip('base/abstract_sql');
	include_spip('base/create');
	$current_version = 0.0;

	$maj = array();
	
	// Première installation
	$maj['create'] = array(
		array('maj_tables', array('spip_documents')),
		array('doc2img_creer_config')
	);
	
	$maj['0.3'] = array(
		array('doc2img_creer_config')
	);
	
	$maj['0.93'] = array(
		array('doc2img_creer_config')
	);
	
	$maj['0.94s'] = array(
		array('maj_tables', array('spip_documents')),
		array('doc2img_creer_config'),
		array('doc2img_update_to_docs')
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function doc2img_creer_config(){
	include_spip('inc/config');
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
}

function doc2img_update_to_docs(){
	spip_log('on update les docs de doc2img','test');
	include_spip('inc/documents');
	include_spip('action/editer_document');
	$doc2imgs = sql_select('*','spip_doc2img');
	$ajouter_documents = charger_fonction('ajouter_documents', 'action');
	while($doc2img = sql_fetch($doc2imgs)){
		/**
		 * On déplace le document dans la table des documents
		 */
		$id_document = $doc2img['id_document'];
		$files = array(array('tmp_name'=>get_spip_doc($doc2img['fichier']),'name'=>basename(get_spip_doc($doc2img['fichier']))));
		$x = $ajouter_documents('new', $files,'document', $id_document, 'doc2img');
		if(intval(reset($x))){
			/**
			 * Si on a un document :
			 * - on ajoute le numéro de page dans spip_documents 
			 * - on supprime le doc2img en base
			 * - on supprime le fichier physique
			 */
			document_modifier(reset($x),array("page" => $doc2img['page']));
			sql_delete('spip_doc2img','id_document='.intval($id_document).' AND fichier='.sql_quote($doc2img['fichier']));
			spip_unlink(get_spip_doc($doc2img['fichier']));
		}
		if (time() >= _TIME_OUT){
			spip_log('on free et on retourne','test');
			sql_free($doc2imgs);
			return;
		}
	}
	sql_drop_table('spip_doc2img');
}
// Supprimer les éléments du plugin
function doc2img_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_doc2img');
	effacer_meta('doc2img');
	effacer_meta($nom_meta_base_version);
}
?>
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
	$maj = array();
	
	/**
	 * Première installation
	 * On ajoute les champs spécifiques à spip_documents
	 * On crée la première configuration
	 */
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
	
	$maj['0.94'] = array(
		array('maj_tables', array('spip_documents')),
		array('doc2img_creer_config'),
		array('doc2img_update_to_docs')
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de génération de configuration à l'installation
 * 
 * Si pas de configuration enregistrée, ajoute une configuration par défaut :
 * -* gestion des formats de fichier pdf, bmp, tiff
 * -* résolution à 150 dpi
 * -* le résultat sera une série de documents png
 * -* on garde les proportions
 * 
 * Si on a Imagick et que l'array des extensions gérées par Imagick n'existe pas, on le remplit
 * 
 */
function doc2img_creer_config(){
	include_spip('inc/config');
    if(!is_array(lire_config('doc2img'))){
        $cfg = array(
            "format_document" => "pdf,bmp,tiff",
        	"resolution" => "150",
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

/**
 * Fonction de mise à jour des anciens documents (dans la table spip_doc2img)
 * en documents standards liés au document original
 * 
 * Utilisé lors du passage à la version_base 0.94
 */
function doc2img_update_to_docs(){
	include_spip('inc/documents');
	include_spip('action/editer_document');
	$doc2imgs = sql_select('*','spip_doc2img');
	$ajouter_documents = charger_fonction('ajouter_documents', 'action');
	while($doc2img = sql_fetch($doc2imgs)){
		/**
		 * On déplace le document doc2img dans la table des documents
		 */
		$id_document = $doc2img['id_document'];
		$files = array(array('tmp_name'=>get_spip_doc($doc2img['fichier']),'name'=>basename(get_spip_doc($doc2img['fichier']))));
		$x = $ajouter_documents('new', $files,'document', $id_document, 'doc2img');
		if(intval(reset($x))){
			/**
			 * Si on a un document doc2img:
			 * - on ajoute le numéro de page dans spip_documents 
			 * - on supprime le doc2img en base
			 * - on supprime le fichier physique
			 */
			document_modifier(reset($x),array("page" => $doc2img['page']));
			sql_delete('spip_doc2img','id_document='.intval($id_document).' AND fichier='.sql_quote($doc2img['fichier']));
			spip_unlink(get_spip_doc($doc2img['fichier']));
		}
		if (time() >= _TIME_OUT){
			sql_free($doc2imgs);
			return;
		}
	}
	sql_drop_table('spip_doc2img');
}
/**
 * Fonction de suppression du plugin
 * 
 * On efface les deux métas de configuration doc2img et doc2img_imagick_extensions
 * On efface également la méta d'installation
 * 
 * TODO Peut être supprimer les documents liés au document?
 * 
 * @param string $nom_meta_base_version
 * 		Le nom de la méta d'installation
 */
function doc2img_vider_tables($nom_meta_base_version) {
	effacer_meta('doc2img');
	effacer_meta('doc2img_imagick_extensions');
	effacer_meta($nom_meta_base_version);
}
?>
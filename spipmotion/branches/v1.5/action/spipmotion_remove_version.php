<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/charsets');	# pour le nom de fichier
include_spip('inc/actions');

/**
 * Action de suppression de version de document
 */
function action_spipmotion_remove_version_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(",^(\d+)\W(\w+)$,", $arg, $r)){
		spip_log("action_spipmotion_remove_version_dist incompris: " . $arg);
		$redirect = urldecode(_request('redirect'));
		return;
	}

	list(, $id_document, $extension) = $r;
	spipmotion_supprimer_versions($id_document,$extension);
	spip_log("suppression de la version $extension de $id_document",'spipmotion');
	
	if(_request('redirect')){
		$redirect = str_replace('&amp;','&',urldecode(_request('redirect')));
		redirige_par_entete($redirect);
	}
	return;
}

/**
 * Supprime les versions du document que l'on souhaite encoder
 * - Supprime les fichiers existants et leurs insertions en base
 * - Supprime la présence de ces documents dans la file d'attente
 *
 * Cette fonction n'est plus utilisée puisqu'on supprime uniquement au niveau du
 * lancement de l'encodage
 * 
 * @param int $id_document L'id_document original
 * @param string $extension L'extension spécifique à supprimer
 */
function spipmotion_supprimer_versions($id_document,$extension=null){
	
	if($extension){
		$v = sql_select("lien.id_document,document.id_vignette,document.fichier",
						"spip_documents as document LEFT JOIN spip_documents_liens as lien ON document.id_document=lien.id_document",
						"lien.id_objet=".intval($id_document)." AND lien.objet='document' AND document.extension=".sql_quote($extension));
	}else{
		$v = sql_select("lien.id_document,document.id_vignette,document.fichier",
						"spip_documents as document LEFT JOIN spip_documents_liens as lien ON document.id_document=lien.id_document",
						"lien.id_objet=".intval($id_document)." AND lien.objet='document'");
	}

	include_spip('inc/documents');
	/**
	 * Pour chaque version du document original
	 */
	while($version = sql_fetch($v)){
		/**
		 * On ajoute l'id_document dans la liste des documents
		 * à supprimer de la base
		 * On supprime le fichier correspondant
		 */
		$liste[] = $version['id_document'];
		if (@file_exists($f = get_spip_doc($version['fichier']))) {
			supprimer_fichier($f);
		}

		/**
		 * Si le document a une vignette :
		 * - On ajoute l'id_document dans la liste à supprimer
		 * - On supprime le fichier correspondant à la vignette
		 */
		if($version['id_vignette'] > 0){
			$liste[] = $version['id_vignette'];
			$fichier = sql_getfetsel('fichier','spip_documents','id_document='.$version['id_vignette']);
			if (@file_exists($f = get_spip_doc($fichier))) {
				supprimer_fichier($f);
			}
		}

	}
	if(is_array($liste)){
		$in = sql_in('id_document', $liste);
		sql_delete("spip_documents", $in);
		sql_delete("spip_documents_liens", $in);
		sql_delete("spip_facd_conversions", "id_document=".intval($id_document).' AND statut != '.sql_quote('oui'));
	}

	include_spip('inc/invalideur');
	suivre_invalideur(1);
}
?>
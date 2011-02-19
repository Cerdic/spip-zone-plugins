<?php
/**
 * SPIPmotion
 * Suppression d'une version spécifique ou de toutes les versions d'un document encodé
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 * 2008-2011 - Distribué sous licence GNU/GPL
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
	include_spip('action/spipmotion_ajouter_file_encodage');
	spipmotion_supprimer_versions($id_document,$extension);
	spip_log("suppression de la version $extension de $id_document",'spipmotion');
	
	if(_request('redirect')){
		$redirect = str_replace('&amp;','&',urldecode(_request('redirect')));
		redirige_par_entete($redirect);
	}
	return;
}

?>
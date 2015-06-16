<?php
/**
 * Crayons 
 * plugin for spip 
 * (c) Fil, toggg 2006-2013
 * licence GPL
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Upload de documents
 * 
 * Cette action recoit des fichiers ($_FILES)
 * et les affecte a l'objet courant ;
 * puis renvoie la liste des documents joints
 */
function action_crayons_upload() {

	$type = preg_replace('/\W+/', '', strval(_request('type')));
	$id = intval(_request('id'));

	// check securite :-)
	include_spip('inc/autoriser');
	if (!autoriser('joindredocument',$type,$id)) {
		echo "Erreur: upload interdit";
		return false;
	}

	// on n'accepte qu'un seul document à la fois, dans la variable 'upss'
	if ($file = $_FILES['upss']
		AND $file['error'] == 0) {

		$source = $file['tmp_name'];  # /tmp/php/phpxxx
		$nom_envoye = $file['name'];  # mon-image.jpg	
		
		include_spip('plugins/installer'); // spip_version_compare dans SPIP 3.x 
		include_spip('inc/plugin'); // spip_version_compare dans SPIP 2.x 
		if (function_exists('spip_version_compare')) { // gerer son absence dans les branche precedente a SPIP 2.x
			if (spip_version_compare($GLOBALS['spip_version_branche'], '3.0.0alpha', '>=')) 
				define('_SPIP3', true);
		} 
		if (defined('_SPIP3')) {
			include_spip('action/ajouter_documents');
			$ajouter_un_document = charger_fonction('ajouter_un_document','action');
			$id = $ajouter_un_document("new", $file, $type, $id, 'document');
		} else {
			include_spip('inc/ajouter_documents');
			$id = ajouter_un_document($source, $nom_envoye, $type, $id, 'document', $id_document=0, $documents_actifs, $titrer=true);
		}
	}

	if (!$id)
		$erreur = "erreur !";

	$a = recuperer_fond('modeles/uploader_item',array('id_document' => $id, 'erreur' => $erreur));

	echo $a;
}

?>

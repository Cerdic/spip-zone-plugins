<?php

// upload d'images
/*
 * Cette action recoit des fichiers ($_FILES)
 * et les affecte a l'objet courant ;
 * puis renvoie la liste des documents joints
 *
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
		include_spip('inc/ajouter_documents');

		$source = $file['tmp_name'];  # /tmp/php/phpxxx
		$nom_envoye = $file['name'];  # mon-image.jpg

		$id = ajouter_un_document($source, $nom_envoye, $type, $id, 'document', $id_document=0, &$documents_actifs, $titrer=true);
	}

	if (!$id) {
		$erreur = "errur !";
	}

	$a = recuperer_fond('modeles/uploader_item',array('id_document' => $id, 'erreur' => $erreur));

	echo $a;
}

?>

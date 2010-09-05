<?php

/**
 * Enregistre dans les fichiers sons les tags ID3
 *
 * @param int $id_document
 * @param array $infos
 * @param array $images
 * @param array $formats
 */

function inc_getid3_ecrire_infos($id_document,$infos=array(),$images=null,$formats = array('id3v1', 'id3v2.3')){
	$err = array();
	if(!intval($id_document)){
		return;
	}
	include_spip('inc/documents');
	$document = sql_fetsel("docs.id_document,docs.titre,docs.extension,docs.fichier,docs.taille,docs.mode", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".intval($id_document));
	$document_chemin = get_spip_doc($document['fichier']);

	include_spip('getid3/getid3');
	$getid3 = new getID3;
	if(!$getid3){
		return false;
	}
	include_spip('getid3/write');
	$getid3->encoding         = 'UTF-8';
	$getid3->encoding_id3v1   = 'ISO-8859-1';
	$getid3->option_tags_html = false;

	$ecrire = new getid3_writetags;
	$ecrire->filename			= $document_chemin;
	$ecrire->tagformats			= $formats;
	$ecrire->tag_encoding		= 'UTF-8';
	$ecrire->overwrite_tags		= true;
	$ecrire->remove_other_tags	= true;

	/**
	 * Eviter des problèmes d'insertion
	 * Au pire des cas on insère des tags vides
	 */
	$ecrire->tag_data = array(
		'title' => array(),
		'artist' => array(),
		'album' => array(),
		'track' => array(),
		'genre' => array(),
		'year' => array(),
		'comment' => array(),
	);

	/**
	 * On utilise nos valeurs
	 */
	foreach ($infos as $info => $value) {
		$ecrire->tag_data[$info][] = $value;
	}

	/**
	 * Ajout des images
	 */
	if(is_array($images)){
		foreach ($images as $image){
			if(!is_array($image)){
				$image_finale['chemin'] = $image;
				$image_finale['picturetypeid'] = '3';
				$image_finale['description'] = 'Front Cover';
				$image_infos = getimagesize($image_finale['chemin']);
				$image_finale['mime'] = image_type_to_mime_type($image_infos[2]);
				$image = $image_finale;
			}
			$ecrire->tag_data['attached_picture'][] = array(
				'data' => file_get_contents($image['chemin']),
				'picturetypeid' => $image['picturetypeid'],
				'description' => $image['description'],
				'mime' => $image['mime']
			);
		}
	}

	/**
	 * On écrit le tout
	 */
	$ecrire->WriteTags();

	/**
	 * Les warnings
	 */
	if (!empty($ecrire->warnings)) {
    	$err = array_merge($err,$ecrire->warnings);
  	}

  	/**
  	 * Les erreurs
  	 */
	if (!empty($ecrire->errors)) {
		$err = array_merge($err,$ecrire->errors);
	}

	return $err;
}
?>

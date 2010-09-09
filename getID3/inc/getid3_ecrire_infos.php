<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

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
	$TagData = array();
	if(!intval($id_document)){
		return;
	}
	include_spip('inc/documents');
	$document_chemin = get_spip_doc(sql_getfetsel("fichier", "spip_documents","id_document=".intval($id_document)));

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
	$ecrire->remove_other_tags	= false;

	/**
	 * On utilise nos valeurs
	 */
	foreach ($infos as $info => $value) {
		$TagData[$info][] = $value;
	}
	
	$TagData = pipeline('pre_edition',
		array(
			'args' => array(
				'table' => 'spip_documents', // compatibilite
				'table_objet' => 'documents',
				'spip_table_objet' => 'spip_documents',
				'type' =>'document',
				'id_objet' => $id_document,
				'action' => 'getid3_ecrire_infos',
				'operation' => 'getid3_ecrire_infos', // compat <= v2.0
			),
			'data' => $TagData
		)
	);
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
			$TagData['attached_picture'][] = array(
				'data' => file_get_contents($image['chemin']),
				'picturetypeid' => $image['picturetypeid'],
				'description' => $image['description'],
				'mime' => $image['mime']
			);
		}
	}
	$ecrire->tag_data = $TagData;
	
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

	$taille = filesize($document_chemin);
	include_spip('inc/modifier');
	revision_document($id_document, array('taille'=>$taille));
	
	pipeline('post_edition',
		array(
			'args' => array(
				'table' => 'spip_documents', // compatibilite
				'table_objet' => 'documents',
				'spip_table_objet' => 'spip_documents',
				'type' =>'document',
				'id_objet' => $id_document,
				'action' => 'getid3_ecrire_infos',
				'operation' => 'getid3_ecrire_infos', // compat <= v2.0
			),
			'data' => $infos
		)
	);
	return $err;
}
?>

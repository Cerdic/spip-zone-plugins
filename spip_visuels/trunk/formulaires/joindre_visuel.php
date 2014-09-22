<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_joindre_visuel_charger_dist($objet,$id_objet){
	$valeurs = array(
		'fichier_visuel_upload' => _request('fichier_visuel_upload'),
		'objet' => $objet,
		'id_objet' => $id_objet
	);

	return $valeurs;
}


function formulaires_joindre_visuel_verifier_dist(){
	$erreurs = array();

	if (count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie comporte des erreurs';
	return $erreurs;

}

function formulaires_joindre_visuel_traiter_dist($objet,$id_objet){
	
	$champs = array(
		'fichier_visuel_upload' => _request('fichier_visuel_upload'),
		'objet' => $objet,
		'id_objet' => $id_objet
	);

	foreach ($_FILES['fichier_visuel_upload']['name'] as $key => $value) {
		if ($value) {
			$fichiers[$key]['name'] = $value;
		}
	}
	foreach ($_FILES['fichier_visuel_upload']['type'] as $key => $value) {
		if ($value) {
			$fichiers[$key]['type'] = $value;
		}
	}
	foreach ($_FILES['fichier_visuel_upload']['tmp_name'] as $key => $value) {
		if ($value) {
			$fichiers[$key]['tmp_name'] = $value;
		}
	}
	foreach ($_FILES['fichier_visuel_upload']['error'] as $key => $value) {
		if ($value!=4) {
			$fichiers[$key]['error'] = $value;
		}
	}
	foreach ($_FILES['fichier_visuel_upload']['size'] as $key => $value) {
		if ($value) {
			$fichiers[$key]['size'] = $value;
		}
	}


	
	foreach ($fichiers as $cle => $valeur) {
		$id_document[$cle] = joindre_le_visuel($objet,$id_objet,$valeur);
	}

	foreach ($id_document as $numero => $id) {
		$items_document = sql_allfetsel("*", "spip_documents", "id_document='".intval($id)."'");
		sql_delete("spip_documents", "id_document=$id");
		sql_delete("spip_documents_liens", "id_document=$id AND id_objet=$id_objet AND objet='$objet'");

		// TODO A tester les alter
		// sql_alter("TABLE spip_documents AUTO_INCREMENT=0");
		// sql_alter("TABLE spip_documents_liens AUTO_INCREMENT=0");

		$valeurs = array(
			'extension' => $items_document[0]['extension'],
			'date' => $items_document[0]['date'],
			'fichier' => $items_document[0]['fichier'],
			'taille' => $items_document[0]['taille'],
			'largeur' => $items_document[0]['largeur'],
			'hauteur' => $items_document[0]['hauteur']
		);

		$nouveau[$numero] = sql_insertq(
			'spip_visuels',
			$valeurs
		);

		sql_insertq(
			'spip_visuels_liens',
			array(
				'id_visuel' => $nouveau[$numero],
				'id_objet' => $id_objet,
				'objet' => $objet
			)
		);

	}

	return array('message_ok'=> "ok");
}

/* ****************
	Les fonctions
******************* */


function joindre_le_visuel($objet='article',$id_objet,$fichier){
	if (!empty($fichier)) {
		$id_document='new';
		$mode = 'auto';
		$galerie = false;
		$proposer_media=true;
		$proposer_ftp=true;
		
		$files = $fichier;
		
		$ajouter_documents = charger_fonction('ajouter_documents', 'action');
		include_spip('inc/joindre_document');
		$mode = joindre_determiner_mode_perso($mode,$id_document,$objet);
		$ajouter_un_document = charger_fonction('ajouter_un_document','action');
		$nouveaux_doc = $ajouter_un_document($id_document, $files, $objet, $id_objet, $mode);

		if (defined('_tmp_dir'))
			effacer_repertoire_temporaire(_tmp_dir);
		return $nouveaux_doc;
	}
}

// Copie/Conforme de medias/formulaires/joindre_document.php TODO => ne pourrait-on pas l'inclure directement ??
function joindre_determiner_mode_perso($mode,$id_document,$objet){
	if ($mode=='auto'){
		if (intval($id_document))
			$mode = sql_getfetsel('mode','spip_documents','id_document='.intval($id_document));
		if (!in_array($mode,array('choix','document','image'))){
			$mode='choix';
			if ($objet AND !in_array(table_objet_sql($objet),explode(',',$GLOBALS['meta']["documents_objets"])))
				$mode = 'image';
		}
	}
	return $mode;
}

?>
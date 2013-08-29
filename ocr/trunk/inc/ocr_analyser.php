<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction autonome analysant un document donné en paramètre
 *
 *  Ensemble des actions necessaires à l'analyse OCR d'une image
 *
 * @param int $id_document identifiant du document à convertir
 */
function ocr_analyser($id_document, $dry_run=false) {
	spip_log('Analyse OCR du document '.$id_document, 'ocr');

	include_spip('inc/config');
	$config = lire_config('ocr',array());
	if ($config['ocr_bin']) {
		$bin = $config['ocr_bin'];
	} else {
		// TODO : essayer de trouver tout seul l'exécutable
		spip_log('Erreur analyse OCR : Il faut specifier l\'exécutable dans le panneau de configuration');
		$resultat['erreur'] = _T('ocr:analyser_erreur_executable_introuvable');
		return $resultat;
	}
	$opt = $config['ocr_opt'] ? $config['ocr_opt'] : '';

	$resultat = array('texte'=>'','erreur'=>'');
	$document = ocr_document($id_document);
	spip_log($document, 'ocr');

	$fichier = $document['fichier'];

	if (!$fichier) {
		$resultat['erreur'] = _T('ocr:analyser_erreur_document_inexistant');
		return $resultat;
	}
	
	$dest = $document['cible_url'].$document['basename'];
	
	$cmd = $bin.$options.' '.$fichier.' '.$dest.' '.$opt;
	spip_log('Commande d\'analyse OCR : "'.$cmd.'"', 'ocr');
	exec($cmd, $output, $status_code);
	
	$resultat['texte'] = '';
	$resultat['erreur'] = ocr_texte_erreur($status_code);

	if ($resultat['erreur']) {
		spip_log('Erreur : '.$resultat['erreur'], 'ocr');
	} else  {
		// on ouvre et on lit le .txt
		// TODO : comment connaitre l'encoding du fichier ?
		$nouveaufichier = $dest.'.txt';
		if (file_exists($nouveaufichier) && is_readable($nouveaufichier)) {
			$texte = file_get_contents($nouveaufichier);
			unlink($nouveaufichier);
			$resultat['texte'] = $texte;
		} else {
			$resultat['erreur'] = _T('ocr:analyser_erreur_fichier_resultat');
		}
	}
	
	if (!$dry_run) {
		// on modifie le champ "ocr" du document dans la base
		spip_log('Modification du champ "ocr" du document id_document='.$id_document.' dans la base', 'ocr');
	}
	
	return $resultat;
}

/**
 * Fonction pour convertir le status_code de tesseract en texte d'erreur
 *
 *  Calcul un tableau :
 *  - avec informations sur le documents (nom, repertoire, nature)
 *  - determine les informations des documents finaux (nom, respertoire, extension)
 *
 * @param $status_code status code retourné par la commande tesseract
 * @return $erreur vide si pas d'erreur ou texte d'erreur selon le status code
 */
function ocr_texte_erreur($status_code) {
	switch ($status_code) {
		case 0:
			$erreur = '';
			break;
		case 1:
		case 2:
		case 3:
			$erreur = _T('ocr:analyser_erreur_'.$status_code);
			break;
		default:
			$erreur = _T('ocr:analyser_erreur_autre');
	}
	return $erreur;
}

/**
 * Fonction pour connaitre les infos fichiers du document
 *
 *  Génère un table avec :
 *  - des informations sur le document (nom, extension, repertoire)
 *  - des informations pour le document à générer (nom, repertoire)
 *
 * @param $id_document identifiant du document
 * @return $document : liste de données caractérisant le document
 */
function ocr_document($id_document) {

    //on recupere l'url du document
    $fichier = sql_fetsel(
        'fichier,extension',
        'spip_documents',
        'id_document='.intval($id_document)
    );

    //chemin relatif du fichier
    include_spip('inc/documents');
    $fichier_reel = get_spip_doc($fichier['fichier']);

    //url relative du repertoire contenant le fichier , on retire aussi le / en fin
    $document['fichier'] = $fichier_reel;

    //information sur le nom du fichier
    $document['extension'] = $fichier['extension'];
    $document['name'] = basename($fichier_reel);
    $document['basename'] = basename($document['name'], '.png');

    // url relative du repertoire cible
    if(!is_dir(_DIR_VAR."cache-ocr")) {
		//creation du repertoire cible
    	sous_repertoire(_DIR_VAR,"cache-ocr");
	}
	$document['cible_url'] = _DIR_VAR."cache-ocr".'/';

    return $document;
}

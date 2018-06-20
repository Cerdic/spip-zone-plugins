<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('base/abstract_sql');

/**
 * Extraire le contenu d'un document donné
 *
 *
 * @param array $document le document à trairer avec au moins un id et un fichier
 * @param string $callback_function
 * @return array Sdata un tableau de donnée, si non traité alors false
 */
function inc_extraire_document_dist($document = array(), $callback_function=null) {
	// Pour garder en mémoire les extracteurs déjà trouvés
	static $extracteurs_ok = array();
	
	// On commence par chercher le fichier à travailler
	if (
		!isset($document['id_document'])
		or !is_numeric($document['id_document'])
	) {
		return false;
	}
	
	if (
		!isset($document['fichier'])
		or !$document['fichier']
		or !isset($document['distant'])
		or !$document['distant']
	) {
		$document = sql_fetsel("id_document,fichier,distant", "spip_documents", "id_document=". intval($document['id_document']));
	}
	
	if (empty($document)) {
		return false;
	}
	
	include_spip('inc/distant');
	include_spip('inc/documents');

	$fichier = '';
	//Obtenir le fichier pour extraction
	if($document['fichier']) {
		if ($document['distant'] == 'oui') {
			if ($fichier = copie_locale($document['fichier'])) {
				// copie locale est la fonction derogatoire qui ne fournit pas un chemin relatif a la racine -> on corrige
				$fichier = _DIR_RACINE . $fichier;
			}
		}
		else {
			$fichier = get_spip_doc($document['fichier']);
		}
	}

	if (!$fichier or !file_exists($fichier)) {
		return false;
	}
	
	//Déterminer le format MIME pour définir le bon extracteur
	//Pour PHP < 5.3, il faut installer la PECL http://pecl.php.net/package/Fileinfo
	//Pour PHP >= 5.3, c'est chargé en natif

	//Determiner les mime type non standard comme les vnd (docx, ....)
	//http://fr.wikipedia.org/wiki/Type_MIME
	$finfo = finfo_open(FILEINFO_MIME_TYPE,find_in_path("finfo/magic")); // Demande le mime type
	$mime = finfo_file($finfo, $fichier);

	//Si on ne reconnait pas le mime type, on teste sur la base par défaut
	if ($mime == "application/octet-stream") {
		$finfo = finfo_open(FILEINFO_MIME_TYPE); // Demande le mime type
		$mime = finfo_file($finfo, $fichier);
	}
	finfo_close($finfo);


	// On cherche le contenu
	$infos = array(
		'mime-type' => $mime,
		'contenu' => false,
	);

	// si on a deja un contenu connu (extraction stockee en base par l'appelant)
	// l'utiliser en fallback
	if (isset($document['contenu']) and $document['contenu']) {
		$infos['contenu'] = $document['contenu'];
		if (isset($document['contenu_filehash']) and $document['contenu_filehash']) {
			$infos['contenu_filehash'] = $document['contenu_filehash'];
		}
	}


	// On cherche le bon extracteur de jus de fichier
	// le test n'est fait qu'une seule fois par type MIME, on garde le résultat en mémoire
	$chemin_mime = preg_replace('/[^\w_]+/','_', $mime); // on accepte seulement chiffre, lettre, et _
	$fonction_extraire = null;
	// Si on a déjà un extracteur pour ce type MIME
	if (isset($extracteurs_ok[$chemin_mime])) {
		$fonction_extraire = $extracteurs_ok[$chemin_mime];
	}
	else {
		// On cherche dans l'ordre :
		// extraire/defaut/application_pdf/10_superlib_pdf.php
		// extraire/defaut/10_superlib_generic.php
		// extraire/fallback/application_pdf/10_pourri_pdf.php
		// extraire/fallback/10_pourri_generic.php
		foreach (array(
			"extraire/defaut/{$chemin_mime}/",
			"extraire/defaut/",
			"extraire/fallback/{$chemin_mime}/",
			"extraire/fallback/",
		) as $dossier) {
			if ($extracteurs = find_all_in_path($dossier, '[.]php$')) {
				$extracteurs = array_keys($extracteurs);
				sort($extracteurs); // On trie par nom pour chercher dans un ordre
				
				// On teste si ça marche dans l'ordre
				foreach ($extracteurs as $fonction) {
					$fonction = substr($fonction, 0, strlen($fonction)-4);
					
					// Si la librairie trouvée existe et que le test dit qu'elle est bien active
					if (
						$fonction_test = charger_fonction('test', $dossier.$fonction)
						and $fonction_test($mime)
					) {
						// On cherche la vraie fonction d'extraction et on la garde en mémoire
						$fonction_extraire = charger_fonction('extraire', $dossier.$fonction);
						$extracteurs_ok[$chemin_mime] = $fonction_extraire;
						break 2; // On arrête la recherche, on a trouvé le meilleur extracteur
					}
				}
			}
		}
	}

	if ($fonction_extraire) {
		$contenu_filehash = substr(md5(basename($fichier) . ':' . filemtime($fichier) . ':' . filesize($fichier) . ':' . $fonction_extraire . ($callback_function? ':' . $callback_function : '')),0,8);
		// si pas de contenu connu, ou si le hash du fichier a change (ou l'extracteur) ou var_mode
		// on rejoue le parsing
		if (!isset($document['contenu'])
		  or !isset($document['contenu_filehash'])
		  or $document['contenu_filehash'] !== $contenu_filehash
		  or _VAR_MODE) {
			if (
				$fonction_extraire
				and $extraction = $fonction_extraire($fichier, $infos)
				and is_array($extraction)
				and isset($extraction['contenu'])
			) {
				if ($callback_function) {
					$extraction['contenu'] = $callback_function($extraction['contenu']);
				}
				$infos = array_merge($infos, $extraction);
				$infos['contenu_filehash'] = $contenu_filehash;
			}
		}
	}

	return $infos;
}
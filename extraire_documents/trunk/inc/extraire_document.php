<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('base/abstract_sql');

/**
 * Extraire le contenu d'un document donné
 *
 *
 * @param $document le document à trairer avec au moins un id et un fichier
 * @return Sdata un tableau de donnée, si non traité alors false
 */
function inc_extraire_document($document = array()) {
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
		or !is_numeric($document['fichier'])
	) {
		$document = sql_fetsel("id_document,fichier", "spip_documents", "id_document = ".$document['id_document']);
	}
	
	if (empty($document)) {
		return false;
	}
	
	include_spip('inc/distant');
	include_spip('inc/documents');
	
	//Obtenir le fichier pour extraction
	if (!$fichier = copie_locale(get_spip_doc($document['fichier']), 'test')) {
		return false;
	}
	
	//Déterminer le format MIME pour définir le bon extracteur
	//Pour PHP < 5.3, il faut installer la PECL http://pecl.php.net/package/Fileinfo
	//Pour PHP >= 5.3, c'est chargé en natif

	//Determiner les mime type non standard comme les vnd (docx, ....)
	//http://fr.wikipedia.org/wiki/Type_MIME
	$finfo = finfo_open(FILEINFO_MIME_TYPE,_DIR_PLUGIN_EXTRAIREDOC."finfo/magic"); // Demande le mime type
	$mime = finfo_file($finfo, _DIR_RACINE.$fichier);

	//Si on ne reconnait pas le mime type, on teste sur la base par défaut
	if ($mime == "application/octet-stream") {
		$finfo = finfo_open(FILEINFO_MIME_TYPE); // Demande le mime type
		$mime = finfo_file($finfo, _DIR_RACINE.$fichier);
	}
	finfo_close($finfo);

	//Ne pas traiter si la mémoire est insuffisante
	//On doit avoir au moins 3 fois la taille du fichier de disponible avant traitement (choix empirique)
	//http://stackoverflow.com/questions/10208698/checking-memory-limit-in-php
	$memory_used = memory_get_usage();
	$memory_limit = ini_get('memory_limit');
	// S'il n'y a PAS de limite de mémoire on en invente une super large
	if ($memory_limit == -1) {
		$memory_limit = '512M';
	}
	$file_size = filesize(_DIR_RACINE.$fichier);
	if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
		if ($matches[2] == 'M') {
			$memory_limit = (int)$matches[1] * 1024 * 1024; // nnnM -> nnn MB
		}
		else if ($matches[2] == 'K') {
			$memory_limit = (int)$matches[1] * 1024; // nnnK -> nnn KB
		}
	}
	$memory_available = $memory_limit - $memory_used - 3 * $file_size;

	if ($memory_available < 0) {
		return false;
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
	
	// On cherche le contenu
	$infos = array(
		'mime-type' => $mime,
		'contenu' => false,
	);
	
	if (
		$fonction_extraire
		and $extraction = $fonction_extraire($fichier, $infos)
		and is_array($extraction)
	) {
		$infos = array_merge($infos, $extraction);
	}
	
	return $infos;
}

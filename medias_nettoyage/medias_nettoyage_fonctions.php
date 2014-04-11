<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

	include_spip('base/abstract_sql');
	include_spip('inc/documents');
	include_spip('inc/chercher_logo');

/**
 * Lister les extensions enregistrées dans la table spip_documents.
 *
 * @return array Tableau des extensions uniques
 */
function medias_lister_extensions_documents () {
	$extensions = array();
	$extensions_cibles = sql_allfetsel('DISTINCT extension', 'spip_documents');
	foreach ($extensions_cibles as $extension) {
		$extensions[] = $extension['extension'];
	}
	// On rajoute le répertoire "vignettes"
	$extensions[] = 'vignettes';
	return $extensions ;
}

function medias_creer_extensions_documents_repertoires () {
	$extensions = medias_lister_extensions_documents();

	foreach ($extensions as $extension) {
		if(!is_dir(_DIR_IMG . $extension)) {
			mkdir(_DIR_IMG . $extension, 0777);
		}
	}
}
/**
 * Lister les répertoires présents dans IMG/ sans les sous-répertoires.
 *
 * @return array
 */
function medias_lister_repertoires_img () {
	$repertoires = array();

	$rep_img = array_diff(scandir(_DIR_IMG), array('..','.','.svn')); // On ne liste pas le répertoire .svn
	foreach ($rep_img as $repertoire) {
		if (is_dir(_DIR_IMG . $repertoire)) {
			$repertoires[] = _DIR_IMG . $repertoire;
		}
	}
	return $repertoires;
}
/**
 * On liste tous les fichiers non distants enregistrés en BDD
 *
 * @return array
 */
function medias_lister_documents_bdd () {
	$docs_fichiers = array();

	$docs_bdd = sql_allfetsel('fichier', 'spip_documents',"distant='non' AND fichier!=''");
	foreach ($docs_bdd as $doc) {
		$docs_fichiers[] = preg_replace("/\/\//", "/", get_spip_doc($doc['fichier'])); // On formate par rapport au répertoire ../IMG/ On évite les doubles // qu'il peut y avoir
	}
	$docs_fichiers = array_filter($docs_fichiers); // on enlève les url vides issues de la base
	sort($docs_fichiers); // On trie dans l'ordre alphabétique

	return $docs_fichiers;
}

/**
 * Donner la taille en octets des documents non-distants enregistrés en BDD
 *
 * @return integer
 */
function medias_lister_documents_bdd_taille(){
	$docs_bdd = sql_fetsel('SUM(taille)', 'spip_documents',"distant='non' AND fichier!=''");
	return $docs_bdd['SUM(taille)'];
}

/**
 * Afficher le nombre de documents enregistrés en BDD
 *
 * @return integer
 */
function medias_lister_documents_bdd_complet_compteur () {
	return sql_countsel('spip_documents');
}

/**
 * Donner la taille en octets de tous les documents enregistrés en BDD
 *
 * @return integer
 */
function medias_lister_documents_bdd_complet_taille(){
	$docs_bdd = sql_fetsel('SUM(taille)', 'spip_documents',"id_document > 0");
	return $docs_bdd['SUM(taille)'];
}

/**
 * Lister les documents enregistrés en BDD mais n'ayant plus de fichiers physiques dans IMG/
 *
 * @uses medias_lister_documents_bdd()
 * @uses medias_lister_documents_repertoire()
 * @return array
 */
function medias_lister_documents_bdd_orphelins(){
	$tableau = array_unique(array_diff(medias_lister_documents_bdd(), medias_lister_documents_repertoire()));
	sort($tableau);
	return $tableau;
}

/**
 * Donner la taille en octets des documents enregistrés en BDD
 *
 * @return integer
 */
function medias_lister_documents_bdd_orphelins_taille(){
	$documents_orphelins 	= medias_lister_documents_bdd_orphelins();
	$taille 				= 0;

	if (count($documents_orphelins) > 0) {
		$documents_bdd = sql_allfetsel('fichier,taille','spip_documents', "fichier IN ('" . join("','",preg_replace("/..\/IMG\//", '', $documents_orphelins)) . "')");
		foreach ($documents_bdd as $document_bdd) {
				if (!file_exists(get_spip_doc($document_bdd['fichier']))) {
					$taille = $taille + (intval($document_bdd['taille'])/1000); // On type la taille issue la bdd en integer puis on divise par 1000 pour éviter la limite de l'integer php.
				}
		}
	}
	return $taille * 1000;
}

/**
 * Lister les documents présents dans le répertoire des extensions de IMG/
 *
 * @uses medias_lister_extensions_documents()
 * @uses medias_lister_logos_fichiers()
 * @return array
 */
function medias_lister_documents_repertoire () {
	$repertoire_img = _DIR_IMG ;
	$docs_fichiers = array();

	foreach (medias_lister_extensions_documents() as $extension) {
		// On va chercher dans IMG/$extension/*.*
		$fichiers = glob($repertoire_img . "$extension/*.*");
		foreach ($fichiers as $fichier) {
			$docs_fichiers[] = preg_replace("/\/\//", "/", $fichier);
		}
	}
	// On va chercher dans IMG/*.*
	$fichiers = glob($repertoire_img . "*.*");
	foreach ($fichiers as $fichier) {
		$docs_fichiers[] = $fichier;
	}
	$tableau = array_unique(array_diff($docs_fichiers, medias_lister_logos_fichiers()));
	sort($tableau);

	return $tableau;
}

/**
 * Retourner la taille en octets des fichiers physiques présents
 * dans les répertoires d'extensions de IMG
 *
 * @uses medias_lister_documents_repertoire()
 * @uses medias_calculer_taille_fichiers()
 * @return integer
 */
function medias_lister_documents_repertoire_taille () {
	return medias_calculer_taille_fichiers(medias_lister_documents_repertoire());
}

/**
 * Lister les fichiers physiques présents dans IMG/ mais qui ne sont plus dans la BDD.
 *
 * @uses medias_lister_documents_repertoire()
 * @uses medias_lister_documents_bdd()
 * @return array
 */
function medias_lister_documents_repertoire_orphelins (){
	$tableau = array_unique(array_diff(medias_lister_documents_repertoire(), medias_lister_documents_bdd()));
	sort($tableau);
	return $tableau;
}

/**
 * Retourner la taille en octets des fichiers physiques orphelins
 * présents dans les répertoires d'extensions de IMG
 *
 * @uses medias_lister_documents_repertoire_orphelins()
 * @uses medias_calculer_taille_fichiers()
 * @return integer
 */
function medias_lister_documents_repertoire_orphelins_taille () {
	return medias_calculer_taille_fichiers(medias_lister_documents_repertoire_orphelins());
}

/**
 * Lister tous les fichiers contenus dans le répertoire IMG/
 * y compris les logos.
 *
 * @param string $repertoire_img
 *        On peut passer un nom de répertoire/chemin en paramètre.
 *        Par défaut, on prend le répertoire IMG/
 * @return array
 */
function medias_lister_documents_repertoire_complet ($repertoire_img = _DIR_IMG){
	$docs_fichiers = array();

	// On va chercher dans IMG/distant/*/*.*
	$fichiers = glob($repertoire_img . "*/*/*.*");
	foreach ($fichiers as $fichier) {
		$docs_fichiers[] = preg_replace("/\/\//", "/", $fichier);
	}
	// On va chercher dans IMG/*/*.*
	$fichiers = glob($repertoire_img . "*/*.*");
	foreach ($fichiers as $fichier) {
		$docs_fichiers[] = preg_replace("/\/\//", "/", $fichier);
	}
	// On va chercher dans IMG/*.*
	$fichiers = glob($repertoire_img . "*.*");
	foreach ($fichiers as $fichier) {
		$docs_fichiers[] = preg_replace("/\/\//", "/", $fichier);
	}

	$docs_fichiers = array_unique($docs_fichiers);
	sort($docs_fichiers);

	return $docs_fichiers;
}

/**
 * Retourner la taille en octets des fichiers physiques présents
 * dans IMG/
 *
 * @uses medias_lister_documents_repertoire_complet()
 * @uses medias_calculer_taille_fichiers()
 *
 * @return integer
 */
function medias_lister_documents_repertoire_complet_taille ($repertoire_img = _DIR_IMG) {
	return medias_calculer_taille_fichiers(medias_lister_documents_repertoire_complet($repertoire_img));
}

/**
 * Lister les logos des objets suivants :
 * - articles (art)
 * - rubriques (rub)
 * - brèves (breve)
 * - sites syndiqués (site)
 * - mot-clé (mot)
 * - auteurs (aut)
 *
 * @todo étendre à d'autres objets éditoriaux.
 *
 * @return array
 */
function medias_lister_logos_fichiers($mode = null){
	$repertoire_img 	= _DIR_IMG ;
	$docs_fichiers_on 	= array();
	$docs_fichiers_off 	= array();
	$logos_objet = array('art','rub','breve','site','mot','aut');

	// On va chercher dans IMG/*.*
	$fichiers = glob($repertoire_img . "{" . join(",",$logos_objet) ."}{on,off}*.*",GLOB_BRACE); // la regex de GLOB_BRACE est très basique...
	foreach ($fichiers as $fichier) {

		if (preg_match("/(" . join("|",$logos_objet) .")on\d+.(jpg|gif|png)$/", $fichier)) { // ... Donc on fait une regex plus poussée avec un preg_match
			$docs_fichiers_on[] = preg_replace("/\/\//", "/", $fichier);
		}
		if (preg_match("/(" . join("|",$logos_objet) .")off\d+.(jpg|gif|png)$/", $fichier)) {
			$docs_fichiers_off[] = preg_replace("/\/\//", "/", $fichier);
		}
	}
	if ($mode == 'on') {
		$docs_fichiers_on = array_unique($docs_fichiers_on);
		sort($docs_fichiers_on); // On trie dans l'ordre alphabétique
		return $docs_fichiers_on;
	} else if ($mode == 'off') {
		$docs_fichiers_off = array_unique($docs_fichiers_off);
		sort($docs_fichiers_off); // On trie dans l'ordre alphabétique
		return $docs_fichiers_off;
	} else {
		$docs_fichiers = array_unique(array_merge($docs_fichiers_on,$docs_fichiers_off));
		sort($docs_fichiers); // On trie dans l'ordre alphabétique
		return $docs_fichiers;
	}

}

/**
 * Retourner la taille en octets des logos présents
 * dans IMG/
 *
 * @uses medias_lister_logos_fichiers()
 * @uses medias_calculer_taille_fichiers()
 * @return integer
 */
function medias_lister_logos_fichiers_taille ($mode = null){
	return medias_calculer_taille_fichiers(medias_lister_logos_fichiers($mode));
}

/**
 * Fonction générique pour calculer la taille des fichiers passés en paramètre
 *
 * @param  array  $fichiers
 *         Tableau contenant l'url des fichiers physiques
 * @return integer
 *         On multiplie par 1000 la variable taille pour avoir le chiffre réel
 *         C'est un hack pour contourner la limite d'integer (4 bytes => 0xefffffff).
 *         Au dessus de 4026531839, il passe à float négatif.
 *         // a vérifier tout de même selon l'OS 32bit ou 64bit.
 */
function medias_calculer_taille_fichiers ($fichiers = array()) {
	$taille = 0;
	if (count($fichiers) > 0) {
		foreach ($fichiers as $fichier) {
			if (file_exists($fichier)) {
				$taille += filesize($fichier) /1000;
			}
		}
		if (is_float($taille) AND $taille > 0) {
			return $taille *1000;
		} else {
			return $taille;
		}
	} else {
		return $taille;
	}
}

/**
 * Lister le contenu du répertoire IMG/orphelins
 *
 * @uses medias_lister_documents_repertoire_complet()
 * @return array
 */
function medias_lister_repertoires_orphelins_contenu () {
	$repertoire_orphelins 	= _DIR_IMG . 'orphelins/';
	$docs_fichiers 			= array();

	if (is_dir($repertoire_orphelins)) {
		$docs_fichiers = medias_lister_documents_repertoire_complet($repertoire_orphelins);
	}
	return $docs_fichiers;
}

/**
 * Lister le contenu du répertoire IMG/orphelins
 *
 * @uses medias_lister_documents_repertoire_complet()
 * @return integer
 */
function medias_lister_repertoires_orphelins_contenu_taille () {
	$repertoire_orphelins 	= _DIR_IMG . 'orphelins/';
	$taille 				= 0;

	if (is_dir($repertoire_orphelins)) {
		return medias_calculer_taille_fichiers(medias_lister_documents_repertoire_complet_taille($repertoire_orphelins));
	} else {
		return intval($taille);
	}
}

/**
 * Fonction 'bidon' pour tester une fonction rapidement sur la page ?exec=test_medias
 *
 * @return array
 */
function test_medias(){
	$test = array();
	$test = medias_lister_logos_fichiers();
	return $test;
}

/**
 * On déplace tous les fichiers orphelins vers un répertoire orphelins dans IMG/
 * On ne les supprime pas!
 *
 * @uses medias_lister_documents_repertoire_orphelins()
 *
 * @return array
 */
function medias_deplacer_documents_repertoire_orphelins () {
	$fichiers_orphelins 	= medias_lister_documents_repertoire_orphelins();
	$fichiers_deplaces 		= array();
	$message_log 			= array();
	$message_log[] 			= date_format(date_create(), 'Y-m-d H:i:s') . ' : Début de la procédure de déplacement.';

	// Si on n'a pas de fichiers orphelins, on ne lance pas la procédure.
	if (count($fichiers_orphelins) > 0) {
		foreach ($fichiers_orphelins as $fichier) {
			$destination = preg_replace("/..\/IMG\//", "../IMG/orphelins/", $fichier);
			$chemin = explode('/', $destination);
			$repertoires = '';
			$profondeur = count($chemin) - 1;
			$i = 0;
			while ($i < $profondeur) {
				$repertoires = $repertoires . $chemin[$i] . '/';
				$i++;
			}
			$repertoires = preg_replace("/\//$", "", $repertoires);
			if (!is_dir($repertoires)) {
				mkdir($repertoires,0777);
				$message_log[] = date_format(date_create(), 'Y-m-d H:i:s') . ' : le répertoire ' . $repertoires . ' a été créé.';
			}

			rename($fichier, $destination);
			$message_log[] = date_format(date_create(), 'Y-m-d H:i:s') . ' : le fichier ' . $fichier . ' a été déplacé vers ' . $destination .'.';
			$fichiers_deplaces[] = $destination;
		}
	} else {
		$message_log[] = date_format(date_create(), 'Y-m-d H:i:s') . ' : Il ne semble pas avoir de documents orphelins dans IMG/';
	}

	$message_log[] = date_format(date_create(), 'Y-m-d H:i:s') . ' : Fin de la procédure de déplacement.';
	spip_log("\n" . join("\n",$message_log) . "\n","documents_orphelins");
	return $fichiers_deplaces;
}



?>
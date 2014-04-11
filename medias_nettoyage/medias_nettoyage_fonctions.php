<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

	include_spip('base/abstract_sql');
	include_spip('inc/documents');
	include_spip('inc/chercher_logo');

/**
 * Lister les extensions enregistrées dans la table spip_documents.
 *
 * @return array
 *         Tableau des extensions uniques
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

/**
 * Créer les répertoires des extensions des documents enregistrés en BDD.
 *
 * @param  string $repertoire_img
 *         Par défaut, on prend _DIR_IMG en référence.
 *         On peut l'utiliser aussi pour le répertoire IMG/orphelins
 * @return void
 */
function medias_creer_extensions_repertoires ($repertoire_img = _DIR_IMG) {
	$extensions = medias_lister_extensions_documents();

	foreach ($extensions as $extension) {
		if(!is_dir($repertoire_img . $extension)) {
			mkdir($repertoire_img . $extension, 0777);
		}
	}
	return;
}

/**
 * Créer le répertoire "IMG/orphelins".
 * Plus pratique d'avoir une fonction
 * qu'on appellera en cas de besoin.
 *
 * @return void
 */
function medias_creer_repertoires_orphelins () {
	if (!is_dir(_MEDIAS_NETTOYAGE_REP_ORPHELINS)) {
		mkdir(_MEDIAS_NETTOYAGE_REP_ORPHELINS,0777);
	}
	return;
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
 * Déplacer tous les répertoires de types 'cache-*' et 'icones*'
 * SPIP normalement, avec la page réparer la base, devrait répérer ce type
 * de dossier. Mais il peut arriver parfois qu'on récupère des sites qui
 * pour X raisons n'ont pas été nettoyé de ces coquilles.
 *
 * @uses medias_creer_repertoires_orphelins()
 *
 * @return void
 */
function medias_deplacer_rep_obsoletes () {
	$pattern_obsoletes		= array("cache-","icones");
	$repertoire_img 		= _DIR_IMG;
	$repertoire_orphelins 	= _MEDIAS_NETTOYAGE_REP_ORPHELINS;
	$repertoires_obsoletes 	= array();
	$message_log 			= array();
	spip_log(date_format(date_create(), 'Y-m-d H:i:s') . ' : Début de la procédure de déplacement des répertoires obsolètes.',"documents_orphelins");

	// On crée le répertoire IMG/orphelins
	medias_creer_repertoires_orphelins();

	// on cherche les fichiers de type cache-20x20-blabla.ext
	$fichiers_obsoletes = find_all_in_path('IMG/','/cache-');

	foreach ($pattern_obsoletes as $pattern) {
		$repertoires = glob($repertoire_img . $pattern . "*");
		$repertoires_obsoletes = array_merge($repertoires_obsoletes,$repertoires);
	}
	// on fusionne avec les fichiers obsolètes
	$repertoires_obsoletes = array_merge($repertoires_obsoletes,$fichiers_obsoletes);

	// on enlève les valeurs vides du tableau.
	$repertoires_obsoletes = array_filter($repertoires_obsoletes);

	if (count($repertoires_obsoletes) > 0) {
		foreach ($repertoires_obsoletes as $repertoire_source) {
			$repertoire_destination = preg_replace("/..\/IMG\//", $repertoire_orphelins, $repertoire_source);
			rename($repertoire_source, $repertoire_destination);
			$message_log[] = date_format(date_create(), 'Y-m-d H:i:s') . ' : Déplacement de '. $repertoire_source . ' vers ' . $repertoire_destination;
		}
	} else {
		// S'il n'y a pas de dossiers obsolètes, on met un message histoire de ne pas rester dans le brouillard.
		$message_log[] = date_format(date_create(), 'Y-m-d H:i:s') . ' : Il n\'y a pas de dossiers obsolètes';
	}
	spip_log("\n" . join("\n",$message_log) . "\n","documents_orphelins");
	spip_log(date_format(date_create(), 'Y-m-d H:i:s') . ' : Fin de la procédure de déplacement des répertoires obsolètes.',"documents_orphelins");
	return;
}

/**
 * Lister tous les fichiers non distants enregistrés en BDD
 *
 * @return array
 *         Tableau contenant les urls des fichiers
 */
function medias_lister_documents_bdd () {
	$docs_fichiers = array();

	$docs_bdd = sql_allfetsel('fichier', 'spip_documents',"distant='non' AND fichier!=''");
	foreach ($docs_bdd as $doc) {
		/**
		 * On formate par rapport au répertoire ../IMG/
		 * On évite les doubles // qu'il peut y avoir
		 */
		$docs_fichiers[] = preg_replace("/\/\//", "/", get_spip_doc($doc['fichier']));
	}
	// on enlève les url vides issues de la base :
	$docs_fichiers = array_filter($docs_fichiers);

	// On trie dans l'ordre alphabétique :
	sort($docs_fichiers);

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
 * @return integer|string
 */
function medias_lister_documents_bdd_complet_compteur () {
	return sql_countsel('spip_documents');
}

/**
 * Donner la taille en octets de tous les documents enregistrés en BDD
 *
 * @return integer|string
 */
function medias_lister_documents_bdd_complet_taille(){
	$docs_bdd = sql_fetsel('SUM(taille)', 'spip_documents',"id_document > 0");
	return $docs_bdd['SUM(taille)'];
}

/**
 * Lister les documents enregistrés en BDD
 * mais n'ayant plus de fichiers physiques dans IMG/
 *
 * @uses medias_lister_documents_bdd()
 * @uses medias_lister_documents_repertoire()
 *
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
 * @uses medias_lister_documents_bdd_orphelins()
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
 *
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
 *
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
 *
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
	$logos_objet 		= array('art','rub','breve','site','mot','aut');

	// On va chercher dans IMG/*.*
	$fichiers = glob($repertoire_img . "{" . join(",",$logos_objet) ."}{on,off}*.*",GLOB_BRACE); // la regex de GLOB_BRACE est très basique...

	foreach ($fichiers as $fichier) {
		// ... Donc on fait une regex plus poussée avec un preg_match
		if (preg_match("/(" . join("|",$logos_objet) .")on\d+.(jpg|gif|png|bmp)$/", $fichier)) {
			$docs_fichiers_on[] = preg_replace("/\/\//", "/", $fichier);
		}
		if (preg_match("/(" . join("|",$logos_objet) .")off\d+.(jpg|gif|png|bmp)$/", $fichier)) {
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
 *
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
 *         On multiplie par 1000 la variable $taille pour avoir le chiffre réel
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
		if (is_float($taille) OR $taille > 0) {
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
 *
 * @return array
 */
function medias_lister_repertoires_orphelins_contenu () {
	$repertoire_orphelins 	= _MEDIAS_NETTOYAGE_REP_ORPHELINS;
	$docs_fichiers 			= array();

	if (is_dir($repertoire_orphelins)) {
		$docs_fichiers = medias_lister_documents_repertoire_complet($repertoire_orphelins);
	}
	return $docs_fichiers;
}

/**
 * Lister le contenu du répertoire IMG/orphelins
 *
 * @uses medias_calculer_taille_fichiers()
 * @uses medias_lister_documents_repertoire_complet()
 *
 * @return integer
 */
function medias_lister_repertoires_orphelins_contenu_taille () {
	$repertoire_orphelins 	= _MEDIAS_NETTOYAGE_REP_ORPHELINS;
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
	$test = medias_deplacer_documents_repertoire_orphelins();
	return $test;
}

/**
 * On déplace tous les fichiers orphelins vers un répertoire orphelins dans IMG/
 * On ne les supprime pas!
 *
 * @uses medias_creer_extensions_repertoires()
 * @uses medias_lister_documents_repertoire_orphelins()
 *
 * @return array
 */
function medias_deplacer_documents_repertoire_orphelins () {
	/**
	 * On crée un log vraiment au début du script.
	 * Ainsi, on sait déjà en regardant les logs
	 * si le script est lancé ou pas.
	 */
	spip_log(date_format(date_create(), 'Y-m-d H:i:s') . ' : Début de la procédure de déplacement.','documents_orphelins');

	$fichiers_orphelins 	= medias_lister_documents_repertoire_orphelins();
	$fichiers_deplaces 		= array();
	$message_log 			= array();
	$repertoire_orphelins 	= _MEDIAS_NETTOYAGE_REP_ORPHELINS;
	if (!is_dir($repertoire_orphelins)) {
		mkdir($repertoire_orphelins,0777);
	}
	// On crée les répertoires d'extensions dans IMG/orphelins
	medias_creer_extensions_repertoires($repertoire_orphelins);

	// Si on n'a pas de fichiers orphelins, on ne lance pas la procédure.
	if (count($fichiers_orphelins) > 0) {
		foreach ($fichiers_orphelins as $fichier) {
			$destination = preg_replace("/..\/IMG\//", $repertoire_orphelins, $fichier);
			$chemin = explode('/', $destination);
			$repertoires = '';
			$profondeur = count($chemin) - 1;
			$i = 0;
			// On a déjà créé les répertoires d'extensions, mais on laisse cette sécu au cas où on a d'autres répertoires à créer.
			while ($i < $profondeur) {
				$repertoires = $repertoires . $chemin[$i] . '/';
				$i++;
			}
			if (!is_dir($repertoires)) {
				mkdir($repertoires,0777);
				$message_log[] = date_format(date_create(), 'Y-m-d H:i:s') . ' : le répertoire ' . $repertoires . ' a été créé.';
			}
			// Hop, on déplace notre fichier vers IMG/orphelins
			rename($fichier, $destination);
			$message_log[] = date_format(date_create(), 'Y-m-d H:i:s') . ' : le fichier ' . $fichier . ' a été déplacé vers ' . $destination .'.';
			// On construit un tableau dans le cas où qqn voudrait utiliser cette donnée.
			// Pour le moment inutilisé.
			$fichiers_deplaces[] = $destination;
		}
	} else {
		$message_log[] = date_format(date_create(), 'Y-m-d H:i:s') . ' : Il ne semble pas avoir de documents orphelins dans IMG/';
	}

	spip_log("\n-------\n" . join("\n",$message_log) . "\n-------\n","documents_orphelins");
	/**
	 * Et là, on marque bien la fin du script dans les logs.
	 */
	spip_log(date_format(date_create(), 'Y-m-d H:i:s') . ' : Fin de la procédure de déplacement.','documents_orphelins');

	return true;
}


?>
<?php
/**
 * Fonctions principales du plugin "Nettoyer la médiathèque"
 * 
 * @plugin     Nettoyer la médiathèque
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Medias_nettoyage\Fonctions
 */

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
 * @uses medias_lister_extensions_documents()
 * @uses _DIR_IMG
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
			@mkdir($repertoire_img . $extension, _SPIP_CHMOD);
		}
	}
	return;
}

/**
 * Créer le répertoire "IMG/orphelins".
 * Plus pratique d'avoir une fonction qu'on appellera en cas de besoin.
 *
 * @uses _MEDIAS_NETTOYAGE_REP_ORPHELINS
 * 
 * @return void
 */
function medias_creer_repertoires_orphelins () {
	if (!is_dir(_MEDIAS_NETTOYAGE_REP_ORPHELINS)) {
		@mkdir(_MEDIAS_NETTOYAGE_REP_ORPHELINS,_SPIP_CHMOD);
	}
	return;
}

/**
 * Lister les répertoires présents dans IMG/ sans les sous-répertoires.
 * 
 * @param  string $repertoire_img 
 *         Par défaut, on prend _DIR_IMG en référence.
 *         On peut l'utiliser aussi pour le répertoire IMG/orphelins ou tout autre nom de répertoire.
 * @return array
 */
function medias_lister_repertoires ($repertoire_img = _DIR_IMG) {
	$repertoires = array();
	// On vérifie que $repertoire_img passé en paramètre est bien un répertoire existant.
	// cf. ../IMG/orphelins qui ne serait pas encore créé.
	if (is_dir($repertoire_img)) {
		$rep_img = array_diff(scandir($repertoire_img), array('..','.','.svn')); // On ne liste pas le répertoire .svn
		foreach ($rep_img as $repertoire) {
			if (is_dir($repertoire_img . $repertoire)) {
				$repertoires[] = $repertoire_img . $repertoire;
			}
		}
	}

	return (array) $repertoires;
}

/**
 * Lister tous les fichiers non distants enregistrés en BDD
 *
 * @uses get_spip_doc()
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

	return (array) $docs_fichiers;
}

/**
 * Donner la taille en octets des documents non-distants enregistrés en BDD
 *
 * @return integer
 */
function medias_lister_documents_bdd_taille(){
	$docs_bdd = sql_fetsel('SUM(taille) AS taille_totale', 'spip_documents',"distant='non' AND fichier!=''");
	return $docs_bdd['taille_totale'];
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
	$docs_bdd = sql_fetsel('SUM(taille) AS taille_totale', 'spip_documents',"id_document > 0");
	return $docs_bdd['taille_totale'];
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
	$docs_bdd = array_unique(array_diff(medias_lister_documents_bdd(), medias_lister_documents_repertoire()));
	sort($docs_bdd);
	return (array) $docs_bdd;
}

/**
 * Donner la taille en octets des documents enregistrés en BDD
 *
 * @uses medias_lister_documents_bdd_orphelins()
 * @uses _DIR_IMG
 * @uses get_spip_doc()
 *
 * @return integer
 */
function medias_lister_documents_bdd_orphelins_taille(){
	$documents_orphelins 	= medias_lister_documents_bdd_orphelins();
	$taille 		= 0;
	$pattern_img 		= "/" . preg_replace("/\//", "\/", _DIR_IMG) . "/";

	if (count($documents_orphelins) > 0) {
		$documents_bdd = sql_allfetsel('fichier,taille','spip_documents', "fichier IN ('" . join("','",preg_replace($pattern_img, '', $documents_orphelins)) . "')");
		foreach ($documents_bdd as $document_bdd) {
			if (!file_exists(get_spip_doc($document_bdd['fichier']))) {
				$taille = $taille + ($document_bdd['taille']/1000); // On divise par 1000 pour éviter la limite de l'integer php.
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
 * @param string $repertoire_img
 *        On peut passer un nom de répertoire/chemin en paramètre.
 *        Par défaut, on prend le répertoire IMG/
 * @return array
 */
function medias_lister_documents_repertoire ($repertoire_img = _DIR_IMG) {
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
	$docs_fichiers = array_unique(array_diff($docs_fichiers, medias_lister_logos_fichiers()));
	sort($docs_fichiers);

	return (array) $docs_fichiers;
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
	$docs_fichiers = array_unique(array_diff(medias_lister_documents_repertoire(), medias_lister_documents_bdd()));
	sort($docs_fichiers);
	return (array) $docs_fichiers;
}

/**
 * Retourner la taille en octets des fichiers physiques orphelins
 * présents dans les répertoires d'extensions de IMG
 *
 * @uses medias_lister_documents_repertoire_orphelins()
 * @uses medias_calculer_taille_fichiers()
 * 
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
        // Exemple : IMG/distant/jpg/nom_fichier.jpg
	$fichiers = glob($repertoire_img . "*/*/*.*");
	foreach ($fichiers as $fichier) {
		$docs_fichiers[] = preg_replace("/\/\//", "/", $fichier); // On évite les doubles slashs '//' qui pourrait arriver comme un cheveu sur la soupe.
	}

	// On va chercher dans IMG/*/*.*
        // Exemple : IMG/pdf/nom_fichier.pdf
	$fichiers = glob($repertoire_img . "*/*.*");
	foreach ($fichiers as $fichier) {
		$docs_fichiers[] = preg_replace("/\/\//", "/", $fichier);
	}

	// On va chercher dans IMG/*.*
        // Exemple : IMG/arton4.png
	$fichiers = glob($repertoire_img . "*.*");
	foreach ($fichiers as $fichier) {
		$docs_fichiers[] = preg_replace("/\/\//", "/", $fichier);
	}

	$docs_fichiers = array_unique($docs_fichiers);
	sort($docs_fichiers);

	return (array) $docs_fichiers;
}

/**
 * Retourner la taille en octets des fichiers physiques présents
 * dans IMG/
 *
 * @uses medias_lister_documents_repertoire_complet()
 * @uses medias_calculer_taille_fichiers()
 *
 * @param string $repertoire_img
 *        On peut passer un nom de répertoire/chemin en paramètre.
 *        Par défaut, on prend le répertoire IMG/
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
 * @param null|string $mode
 *        + `null` : stockera dans le tableau tous les logos, 
 *        quelque soit le mode du logo
 *        + `on` : stockera dans le tableau tous les logos du mode "on"
 *        + `off` : stockera dans le tableau tous les logos du mode "off"
 * @param string $repertoire_img
 *        On peut passer un nom de répertoire/chemin en paramètre.
 *        Par défaut, on prend le répertoire IMG/
 * @return array
 */
function medias_lister_logos_fichiers($mode = null, $repertoire_img = _DIR_IMG){

	if (intval(spip_version()) == 2) {
		include_spip('base/connect_sql');
	} else if (intval(spip_version()) == 3) {
		include_spip('base/objets');
	}

	global $formats_logos;
	$docs_fichiers_on 	= array();
	$docs_fichiers_off 	= array();
	$logos_objet 		= array('art','rub','breve','site','mot','aut');

	// On va chercher toutes les tables connues de SPIP
	foreach (sql_alltable() as $table) {
		// On cherche son type d'objet et on l'ajoute aux logos
		// Il y a aussi dans ces objets la référence à 'article', 'rubrique' et 'auteur'
		// On peut les laisser, ça ne mange pas de pain de prendre en compte les "tordus" ;-)
		$logos_objet[] = objet_type($table);
	}
	// On enlève les doublons
	$logos_objet = array_unique($logos_objet);
	sort($logos_objet);

	// On va chercher dans IMG/*(on|off)*.*
	$fichiers = glob($repertoire_img . "{" . join(",",$logos_objet) ."}{on,off}*.*",GLOB_BRACE); // la regex de GLOB_BRACE est très basique...

	foreach ($fichiers as $fichier) {
		// ... Donc on fait une regex plus poussée avec un preg_match
		if (preg_match("/(" . join("|",$logos_objet) .")on\d+.(" . join("|", $formats_logos) .")$/", $fichier)) {
			$docs_fichiers_on[] = preg_replace("/\/\//", "/", $fichier);
		}
		if (preg_match("/(" . join("|",$logos_objet) .")off\d+.(" . join("|",$formats_logos) .")$/", $fichier)) {
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
 * @param null|string $mode
 *        + `null` : calculera le poids de tous les logos, 
 *        quelque soit le mode du logo
 *        + `on` : calculera le poids de tous les logos du mode "on"
 *        + `off` : calculera le poids de tous les logos du mode "off"
 * @return integer|string
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
 * Lister les répertoires à la racine de IMG/orphelins.
 * Cette fonction vérifie l'existence du répertoire IMG/orphelins
 * avant de lister les répertoires.
 *
 * @uses medias_lister_repertoires()
 * @uses _MEDIAS_NETTOYAGE_REP_ORPHELINS
 *
 * @return array
 */
function medias_lister_repertoires_orphelins () {
	if (is_dir(_MEDIAS_NETTOYAGE_REP_ORPHELINS)) {
		return medias_lister_repertoires(_MEDIAS_NETTOYAGE_REP_ORPHELINS);
	} else {
		return array();
	}
}

/**
 * Lister le contenu du répertoire IMG/orphelins
 *
 * @uses medias_lister_documents_repertoire_complet()
 * @uses _MEDIAS_NETTOYAGE_REP_ORPHELINS
 *
 * @return array
 */
function medias_lister_repertoires_orphelins_fichiers () {
	$repertoire_orphelins 	= _MEDIAS_NETTOYAGE_REP_ORPHELINS;
	$docs_fichiers 		= array();

	if (is_dir($repertoire_orphelins)) {
		$docs_fichiers = medias_lister_documents_repertoire_complet($repertoire_orphelins);
	}
	return (array) $docs_fichiers;
}

/**
 * Lister le contenu du répertoire IMG/orphelins
 *
 * @uses medias_calculer_taille_fichiers()
 * @uses medias_lister_documents_repertoire_complet()
 * @uses _MEDIAS_NETTOYAGE_REP_ORPHELINS
 *
 * @return integer
 */
function medias_lister_repertoires_orphelins_fichiers_taille () {
	$repertoire_orphelins 	= _MEDIAS_NETTOYAGE_REP_ORPHELINS;
	$taille 		= 0;

	if (is_dir($repertoire_orphelins)) {
		return medias_calculer_taille_fichiers(medias_lister_documents_repertoire_complet($repertoire_orphelins));
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
	$test = @unserialize($GLOBALS['meta']['medias_nettoyage']);
	return $test;
}

/**
 * Déplacer tous les répertoires de types 'cache-*' et 'icones*'
 * SPIP normalement, avec la page "réparer la base", devrait répérer ce type
 * de dossier. Mais il peut arriver parfois qu'on récupère des sites qui
 * pour X raisons n'ont pas été nettoyé de ces coquilles.
 *
 * @uses medias_creer_repertoires_orphelins()
 * @uses _DIR_IMG
 * @uses _MEDIAS_NETTOYAGE_REP_ORPHELINS
 *
 * @return void
 */
function medias_deplacer_rep_obsoletes () {
	spip_log(date_format(date_create(), 'Y-m-d H:i:s') . ' : Début de la procédure de déplacement des répertoires obsolètes.',"medias_nettoyage");

	$pattern_obsoletes	= array("cache-","icones");
	$repertoire_img 	= _DIR_IMG;
	$repertoire_orphelins 	= _MEDIAS_NETTOYAGE_REP_ORPHELINS;
	$repertoires_obsoletes 	= array();
	$message_log 		= array();
	$pattern_img 		= "/" . preg_replace("/\//", "\/", $repertoire_img) . "/";

	// On crée le répertoire IMG/orphelins
	medias_creer_repertoires_orphelins();

	// on cherche les fichiers de type IMG/cache-20x20-blabla.ext
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
			$repertoire_destination = preg_replace($pattern_img, $repertoire_orphelins, $repertoire_source);
			@rename($repertoire_source, $repertoire_destination);
			$message_log[] = date_format(date_create(), 'Y-m-d H:i:s') . ' : Déplacement de '. $repertoire_source . ' vers ' . $repertoire_destination;
		}
	} else {
		// S'il n'y a pas de dossiers obsolètes, on met un message histoire de ne pas rester dans le brouillard.
		$message_log[] = date_format(date_create(), 'Y-m-d H:i:s') . ' : Il n\'y a pas de dossiers ou de fichiers obsolètes';
	}
	spip_log("\n-------\n" . join("\n",$message_log) . "\n-------\n","medias_nettoyage");
	spip_log(date_format(date_create(), 'Y-m-d H:i:s') . ' : Fin de la procédure de déplacement des répertoires obsolètes.',"medias_nettoyage");
	return;
}

/**
 * On déplace tous les fichiers orphelins vers un répertoire orphelins dans IMG/
 * On ne les supprime pas!
 *
 * @uses medias_creer_repertoires_orphelins()
 * @uses medias_creer_extensions_repertoires()
 * @uses medias_lister_documents_repertoire_orphelins()
 * @uses _DIR_IMG
 * @uses _MEDIAS_NETTOYAGE_REP_ORPHELINS
 *
 * @return array
 */
function medias_deplacer_documents_repertoire_orphelins () {
	/**
	 * On crée un log vraiment au début du script.
	 * Ainsi, on sait déjà en regardant les logs
	 * si le script est lancé ou pas.
	 */
	spip_log(date_format(date_create(), 'Y-m-d H:i:s') . ' : Début de la procédure de déplacement.',"medias_nettoyage");

	$fichiers_orphelins 	= medias_lister_documents_repertoire_orphelins();
	$fichiers_deplaces 	= array();
	$message_log 		= array();
	$repertoire_orphelins 	= _MEDIAS_NETTOYAGE_REP_ORPHELINS;
	$pattern_img 		= "/" . preg_replace("/\//", "\/", _DIR_IMG) . "/";

	// On crée le répertoire IMG/orphelins s'il n'existe pas
	medias_creer_repertoires_orphelins();
	// On crée les répertoires d'extensions dans IMG/orphelins
	medias_creer_extensions_repertoires($repertoire_orphelins);

	// Si on n'a pas de fichiers orphelins, on ne lance pas la procédure.
	if (count($fichiers_orphelins) > 0) {
		foreach ($fichiers_orphelins as $fichier) {
			$destination = preg_replace($pattern_img, $repertoire_orphelins, $fichier);
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
				@mkdir($repertoires,_SPIP_CHMOD);
				$message_log[] = date_format(date_create(), 'Y-m-d H:i:s') . ' : le répertoire ' . $repertoires . ' a été créé.';
			}
			// Hop, on déplace notre fichier vers IMG/orphelins
			@rename($fichier, $destination);
			$message_log[] = date_format(date_create(), 'Y-m-d H:i:s') . ' : le fichier ' . $fichier . ' a été déplacé vers ' . $destination .'.';
			// On construit un tableau dans le cas où qqn voudrait utiliser cette donnée.
			// Pour le moment inutilisé.
			$fichiers_deplaces[] = $destination;
		}
	} else {
		$message_log[] = date_format(date_create(), 'Y-m-d H:i:s') . ' : Il ne semble pas avoir de documents orphelins dans IMG/';
	}

	spip_log("\n-------\n" . join("\n",$message_log) . "\n-------\n","medias_nettoyage");
	/**
	 * Et là, on marque bien la fin du script dans les logs.
	 */
	spip_log(date_format(date_create(), 'Y-m-d H:i:s') . ' : Fin de la procédure de déplacement.',"medias_nettoyage");

	return true;
}

/**
 * Réparer les documents.
 * Il arrive parfois que suite à un problème de droits, les documents ne soient plus rangés correctement dans IMG/ext/fichier.ext
 * mais dans un faux sous répertoire IMG/ext_fichier.ext
 * Le présent script va recopier les fichiers mal placés, et changer leur référence dans la table spip_documents ;
 * il donnera ensuite la liste des fichiers recopiés et des erreurs recontrées dans un fichier de log.
 *
 * Script repris de ce fichier : http://zone.spip.org/trac/spip-zone/browser/_outils_/repare_doc.html
 *
 * @uses medias_lister_logos_fichiers()
 * @uses _DIR_IMG
 *
 * @return bool
 */
function medias_reparer_documents_fichiers () {
	/**
	 * On crée un log vraiment au début du script.
	 * Ainsi, on sait déjà en regardant les logs
	 * si le script est lancé ou pas.
	 */
	spip_log(date_format(date_create(), 'Y-m-d H:i:s') . ' : Début de la procédure de réparation des documents.',"medias_nettoyage");

	$repertoire_img 	= _DIR_IMG ;
	$docs_fichiers 		= array();
	$pattern_img 		= "/" . preg_replace("/\//", "\/", $repertoire_img) . "/";
	$message_log 		= array();

	// On va chercher dans IMG/*.*
	$fichiers = glob($repertoire_img . "*.*");
	foreach ($fichiers as $fichier) {
		$docs_fichiers[] = $fichier;
	}
	$docs_fichiers = array_filter(array_diff($docs_fichiers, medias_lister_logos_fichiers())); // a voir si on n'a pas de logos ce que ça donne comme ça…
	$docs_fichiers = preg_replace($pattern_img, '', $docs_fichiers);

	if (count($docs_fichiers) > 0) {
		$docs_bdd = sql_allfetsel('id_document,fichier,extension', 'spip_documents', "fichier IN ('" . join("','", $docs_fichiers) . "') AND mode IN ('document','image')");
		foreach ($docs_bdd as $document) {
			$destination = preg_replace(',^([a-z0-3]+)_([^/]+\.(\1))$,i', '$1/$2', $document['fichier']);
			// On va vérifier si on est bien sous la forme ../IMG/ext/nom_fichier.ext
			// Sinon, on le construit manuellement. 
			// (ne pas oublier d'enlever '../IMG/' à notre variable de test 
			// car cette variable sera enresgitrée en BDD)
			$destination_test = preg_replace($pattern_img, '', $destination);
			if (count(explode("/", $destination_test)) == 1) {
				$destination = $document['extension'] . '/' . $destination_test ;
			}
			if ($document['fichier'] != $destination AND rename($repertoire_img . $document['fichier'],$repertoire_img . $destination)) {
				sql_updateq('spip_documents', array('fichier' => $destination), 'id_document=' . $document['id_document']);
				$message_log[] = date_format(date_create(), 'Y-m-d H:i:s') . ' : le fichier ' . $repertoire_img . $document['fichier'] . ' a été déplacé vers ' . $repertoire_img . $destination .'.';
			} else {
				$message_log[] = date_format(date_create(), 'Y-m-d H:i:s') . ' : le fichier ' . $repertoire_img . $document['fichier'] . ' n\'a pu être déplacé vers ' . $repertoire_img . $destination . '.';
			}
		}
	} else {
		$message_log[] = date_format(date_create(), 'Y-m-d H:i:s') . ' : Il n\'y a pas de documents à réparer.';
	}

	spip_log("\n-------\n" . join("\n",$message_log) . "\n-------\n","medias_nettoyage");
	/**
	 * Et là, on marque bien la fin du script dans les logs.
	 */
	spip_log(date_format(date_create(), 'Y-m-d H:i:s') . ' : Fin de la procédure de réparation des documents.',"medias_nettoyage");

	return true;
}

?>
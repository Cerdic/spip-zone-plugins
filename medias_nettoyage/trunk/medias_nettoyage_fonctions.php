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

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}
include_spip('base/abstract_sql');
include_spip('inc/documents');
include_spip('inc/chercher_logo');

/**
 * Lister les extensions enregistrées dans la table spip_documents.
 *
 * @return array
 *         Tableau des extensions uniques
 */
function medias_lister_extensions_documents ()
{
    $extensions = array();
    $extensions_cibles = sql_allfetsel('DISTINCT extension', 'spip_documents');
    // On vérifie bien qu'on reçoit un tableau.
    if (is_array($extensions_cibles) and count($extensions_cibles) > 0) {
        foreach ($extensions_cibles as $extension) {
            $extensions[] = $extension['extension'];
        }
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
function medias_creer_extensions_repertoires ($repertoire_img = _DIR_IMG)
{
    $extensions = medias_lister_extensions_documents();

    if (is_array($extensions) and count($extensions) > 0) {
        foreach ($extensions as $extension) {
            if (!is_dir($repertoire_img . $extension)) {
                @mkdir($repertoire_img . $extension, _SPIP_CHMOD);
            }
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
function medias_creer_repertoires_orphelins ()
{
    if (!is_dir(_MEDIAS_NETTOYAGE_REP_ORPHELINS)) {
        @mkdir(_MEDIAS_NETTOYAGE_REP_ORPHELINS, _SPIP_CHMOD);
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
function medias_lister_repertoires ($repertoire_img = _DIR_IMG)
{
    $repertoires = array();
    // On vérifie que $repertoire_img passé en paramètre est bien un répertoire existant.
    // cf. ../IMG/orphelins qui ne serait pas encore créé.
    if (is_dir($repertoire_img)) {
        // Avec la fonction scandir, on liste le contenu (existant) du répertoire cible.
        $rep_img = array_diff(scandir($repertoire_img), array('..','.','.svn')); // On ne liste pas le répertoire .svn
        foreach ($rep_img as $repertoire) {
            // On vérifie que c'est un répertoire et non un fichier.
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
function medias_lister_documents_bdd ()
{
    $docs_fichiers = array();

    $docs_bdd = sql_allfetsel('fichier', 'spip_documents', "distant='non' AND fichier!=''");
    // On vérifie que nous avons au moins un élément dans le tableau
    if (count($docs_bdd) > 0) {
        foreach ($docs_bdd as $doc) {
            /**
             * On formate par rapport au répertoire ../IMG/
             * On évite les doubles // qu'il peut y avoir
             */
            $docs_fichiers[] = preg_replace("/\/\//", "/", get_spip_doc($doc['fichier']));
        }
        // on enlève les url vides issues de la base :
        $docs_fichiers = array_filter($docs_fichiers);
    }

    // On trie dans l'ordre alphabétique :
    sort($docs_fichiers);

    return (array) $docs_fichiers;
}

/**
 * Donner la taille en octets des documents non-distants enregistrés en BDD
 *
 * @return integer
 */
function medias_lister_documents_bdd_taille()
{
    $docs_bdd = sql_fetsel('SUM(taille) AS taille_totale', 'spip_documents', "distant='non' AND fichier!=''");
    return $docs_bdd['taille_totale'];
}

/**
 * Afficher le nombre de documents enregistrés en BDD
 *
 * @return integer|string
 */
function medias_lister_documents_bdd_complet_compteur ()
{
    return sql_countsel('spip_documents');
}

/**
 * Donner la taille en octets de tous les documents enregistrés en BDD
 *
 * @return integer|string
 */
function medias_lister_documents_bdd_complet_taille()
{
    $docs_bdd = sql_fetsel('SUM(taille) AS taille_totale', 'spip_documents', "id_document > 0");
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
function medias_lister_documents_bdd_orphelins()
{
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
function medias_lister_documents_bdd_orphelins_taille()
{
    $docs_orphelins    = medias_lister_documents_bdd_orphelins();
    $taille         = 0;
    $pattern_img        = "/" . preg_replace("/\//", "\/", _DIR_IMG) . "/";

    if (count($docs_orphelins) > 0) {
        $docs_bdd = sql_allfetsel(
            'fichier,taille',
            'spip_documents',
            "fichier IN ('"
            . join("','", preg_replace($pattern_img, '', $docs_orphelins)) . "')"
        );
        if (is_array($docs_bdd) and count($docs_bdd) > 0) {
            foreach ($docs_bdd as $document_bdd) {
                if (!file_exists(get_spip_doc($document_bdd['fichier']))) {
                    $taille = $taille + ($document_bdd['taille']/1000);
                    // On divise par 1000 pour éviter la limite de l'integer php.
                }
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
function medias_lister_documents_repertoire ($repertoire_img = _DIR_IMG)
{
    $docs_fichiers = array();

    foreach (medias_lister_extensions_documents() as $extension) {
        // Par sécurité, on vérifie que l'extension a bel
        // et bien un répertoire physique
        if (is_dir($repertoire_img . $extension)) {
            // On va chercher dans IMG/$extension/*.*
            $fichiers = glob($repertoire_img . "$extension/*.*");
            if (is_array($fichiers) and count($fichiers) > 0) {
                foreach ($fichiers as $fichier) {
                    $docs_fichiers[] = preg_replace("/\/\//", "/", $fichier);
                }
            }
        }
    }
    // On va chercher dans IMG/*.*
    $fichiers = glob($repertoire_img . "*.*");
    // On vérifie que c'est bien un tableau, avec au moins un élément.
    if (is_array($fichiers) and count($fichiers) > 0) {
        foreach ($fichiers as $fichier) {
            $docs_fichiers[] = $fichier;
        }
    }
    $docs_fichiers = array_unique(
        array_diff(
            $docs_fichiers,
            medias_lister_logos_fichiers()
        )
    );
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
function medias_lister_documents_repertoire_taille ()
{
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
function medias_lister_documents_repertoire_orphelins ()
{
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
function medias_lister_documents_repertoire_orphelins_taille ()
{
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
function medias_lister_documents_repertoire_complet ($repertoire_img = _DIR_IMG)
{
    $docs_fichiers = array();

    // On va chercher dans IMG/distant/*/*.*
        // Exemple : IMG/distant/jpg/nom_fichier.jpg
    $fichiers = glob($repertoire_img . "*/*/*.*");
    if (is_array($fichiers) and count($fichiers) > 0) {
        foreach ($fichiers as $fichier) {
            $docs_fichiers[] = preg_replace("/\/\//", "/", $fichier);
            // On évite les doubles slashs '//' qui pourrait arriver comme un cheveu sur la soupe.
        }
    }

    // On va chercher dans IMG/*/*.*
        // Exemple : IMG/pdf/nom_fichier.pdf
    $fichiers = glob($repertoire_img . "*/*.*");
    if (is_array($fichiers) and count($fichiers) > 0) {
        foreach ($fichiers as $fichier) {
            $docs_fichiers[] = preg_replace("/\/\//", "/", $fichier);
        }
    }

    // On va chercher dans IMG/*.*
        // Exemple : IMG/arton4.png
    $fichiers = glob($repertoire_img . "*.*");
    if (is_array($fichiers) and count($fichiers) > 0) {
        foreach ($fichiers as $fichier) {
            $docs_fichiers[] = preg_replace("/\/\//", "/", $fichier);
        }
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
function medias_lister_documents_repertoire_complet_taille ($repertoire_img = _DIR_IMG)
{
    return medias_calculer_taille_fichiers(medias_lister_documents_repertoire_complet($repertoire_img));
}

/**
 * Lister les logos des objets éditoriaux
 * Prend en compte les cas particuliers suivants :
 * - articles (art)
 * - rubriques (rub)
 * - sites syndiqués (site)
 * - auteurs (aut)
 *
 * @uses lister_tables_principales()
 *       liste en spip 3 les tables principales reconnues par SPIP
 * @uses id_table_objet()
 *       retourne la clé primiare de l'objet
 * @uses type_du_logo()
 *       retourne le type de logo tel que `art` depuis le nom de la clé primaire de l'objet
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
function medias_lister_logos_fichiers ($mode = null, $repertoire_img = _DIR_IMG)
{

    include_spip('inc/chercher_logo');
    include_spip('base/abstract_sql');

    if (intval(spip_version()) == 2) {
        include_spip('base/connect_sql');
        $tables_objets = (isset($GLOBALS['tables_principales']))
        ? array_keys($GLOBALS['tables_principales'])
        : array('spip_articles',
            'spip_rubriques',
            'spip_auteurs',
            'spip_breves',
            'spip_documents',
            'spip_syndic',
            'spip_mots',
            'spip_forum',
            'spip_groupes_mots');
    } elseif (intval(spip_version()) == 3) {
        include_spip('base/objets');
        $tables_objets = array_keys(lister_tables_principales());
    }

    global $formats_logos;
    $docs_fichiers_on   = array();
    $docs_fichiers_off  = array();

    // On va chercher toutes les tables principales connues de SPIP
    foreach ($tables_objets as $table) {
        // On cherche son type d'objet.
        // Il y a aussi dans ces objets la référence à `article`,
        // `rubrique` et `auteur`
        // Grâce à la fonction `id_table_objet()`, on retrouve le nom de la clé primaire de l'objet.
        // `type_du_logo()` retourne le type de logo tel que `art` depuis le nom de la clé primaire de l'objet
        $type_du_logo = type_du_logo(id_table_objet($table));

        // On va chercher dans IMG/$type_du_logo(on|off)*.*
        // On fait un foreach pour ne pas avoir de
        // "Pattern exceeds the maximum allowed length of 260 characters"
        // sur glob()
        $liste = glob($repertoire_img . "{" . $type_du_logo ."}{on,off}*.*", GLOB_BRACE);

        // Il faut avoir au moins un élément dans le tableau de fichiers.
        if (is_array($liste) and count($liste) > 0) {
            foreach ($liste as $fichier) {
                // ... Donc on fait une regex plus poussée avec un preg_match
                if (
                    preg_match(
                        "/("
                        . $type_du_logo
                        .")on(\d+).("
                        . join("|", $formats_logos)
                        .")$/",
                        $fichier,
                        $r
                    )
                ) {
                    // On fait une requête sql pour savoir si ce logo a toujours un objet référencé en bdd.
                    $requete = sql_fetsel('*', $table, id_table_objet($table) . "=" . $r[2]);
                    if ($requete) {
                        $docs_fichiers_on[] = preg_replace("/\/\//", "/", $fichier);
                    }
                }
                if (
                    preg_match(
                        "/("
                        . $type_du_logo
                        .")off(\d+).("
                        . join("|", $formats_logos)
                        .")$/",
                        $fichier,
                        $r
                    )
                ) {
                    $requete = sql_fetsel('*', $table, id_table_objet($table) . "=" . $r[2]);
                    if ($requete) {
                        $docs_fichiers_off[] = preg_replace("/\/\//", "/", $fichier);
                    }
                }
            }
        }
    }

    // On va chercher le logo du site.
    // On force la recherche sur cet élément même si la recherche "classique"
    // devrait gérer cela initialement…
    $logos_site = glob($repertoire_img . "{site}{on,off}0.*", GLOB_BRACE);
    // On évite d'utiliser la fonction `glob()` directement dans le `if` car ça peut créer un bug pour PHP <5.4
    // S'il n'y a pas de siteon0.ext, `glob()` va retourner un `false`. Donc, on regarde si c'est bien un tableau.
    // cf. http://contrib.spip.net/Nettoyer-la-mediatheque#forum475712
    if (is_array($logos_site) and count($logos_site) > 0) {
        foreach ($logos_site as $logo_site) {
            if (
                preg_match(
                    "/(siteon)(\d).("
                    . join("|", $formats_logos)
                    .")$/",
                    $logo_site
                )
            ) {
                $docs_fichiers_on[] = preg_replace("/\/\//", "/", $logo_site);
            }
            if (
                preg_match(
                    "/(siteoff)(\d).("
                    . join("|", $formats_logos)
                    .")$/",
                    $logo_site
                )
            ) {
                $docs_fichiers_off[] = preg_replace("/\/\//", "/", $logo_site);
            }
        }
    }

    // On va lister le logo standard des rubriques : rubon0.ext et ruboff0.ext
    // cf. http://contrib.spip.net/Nettoyer-la-mediatheque#forum475870
    $logos_rub_racine = glob($repertoire_img . "{rub}{on,off}0.*", GLOB_BRACE);
    if (is_array($logos_rub_racine) and count($logos_rub_racine) > 0) {
        foreach ($logos_rub_racine as $logo_rub_racine) {
            if (
                preg_match(
                    "/(rubon)(\d).("
                    . join("|", $formats_logos)
                    .")$/",
                    $logo_rub_racine
                )
            ) {
                $docs_fichiers_on[] = preg_replace("/\/\//", "/", $logo_rub_racine);
            }
            if (
                preg_match(
                    "/(ruboff)(\d).("
                    . join("|", $formats_logos)
                    .")$/",
                    $logo_rub_racine
                )
            ) {
                $docs_fichiers_off[] = preg_replace("/\/\//", "/", $logo_rub_racine);
            }
        }
    }


    if ($mode == 'on') {
        $docs_fichiers_on = array_unique($docs_fichiers_on);
        sort($docs_fichiers_on); // On trie dans l'ordre alphabétique
        return $docs_fichiers_on;
    } elseif ($mode == 'off') {
        $docs_fichiers_off = array_unique($docs_fichiers_off);
        sort($docs_fichiers_off); // On trie dans l'ordre alphabétique
        return $docs_fichiers_off;
    } else {
        $docs_fichiers = array_unique(array_merge($docs_fichiers_on, $docs_fichiers_off));
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
function medias_lister_logos_fichiers_taille ($mode = null)
{
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
function medias_calculer_taille_fichiers ($fichiers = array())
{
    $taille = 0;
    if (count($fichiers) > 0) {
        foreach ($fichiers as $fichier) {
            if (file_exists($fichier)) {
                $taille += filesize($fichier) /1000;
            }
        }
        if (is_float($taille) or $taille > 0) {
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
function medias_lister_repertoires_orphelins ()
{
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
function medias_lister_repertoires_orphelins_fichiers ()
{
    $repertoire_orphelins   = _MEDIAS_NETTOYAGE_REP_ORPHELINS;
    $docs_fichiers      = array();

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
function medias_lister_repertoires_orphelins_fichiers_taille ()
{
    $repertoire_orphelins   = _MEDIAS_NETTOYAGE_REP_ORPHELINS;
    $taille         = 0;

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
function test_medias ()
{
    $test = array();
    $test = medias_lister_logos_fichiers();
    return $test;
}

/**
 * Déplacer tous les répertoires de types 'cache-*' et 'icones*'
 * SPIP normalement, avec la page "réparer la base", devrait répérer ce type
 * de dossier. Mais il peut arriver parfois qu'on récupère des sites qui
 * pour X raisons n'ont pas été nettoyé de ces coquilles.
 *
 * @uses medias_creer_repertoires_orphelins()
 * @uses medias_lister_documents_bdd()
 * @uses _DIR_IMG
 * @uses _MEDIAS_NETTOYAGE_REP_ORPHELINS
 *
 * @return void
 */
function medias_deplacer_rep_obsoletes ()
{
    spip_log(
        date_format(date_create(), 'Y-m-d H:i:s')
        . ' : Début de la procédure de déplacement des répertoires obsolètes.',
        "medias_nettoyage"
    );

    $pattern_obsoletes  = array("cache-","icones");
    $repertoire_img     = _DIR_IMG;
    $repertoire_orphelins   = _MEDIAS_NETTOYAGE_REP_ORPHELINS;
    $repertoires_obsoletes  = array();
    $message_log        = array();
    $pattern_img        = "/" . preg_replace("/\//", "\/", $repertoire_img) . "/";

    // On crée le répertoire IMG/orphelins
    medias_creer_repertoires_orphelins();

    // on cherche les fichiers de type IMG/cache-20x20-blabla.ext
    $fichiers_obsoletes = find_all_in_path('IMG/', '/cache-');
    // on vérifie tout de même que ces fichiers ne font pas parti des documents en BDD
    $fichiers_obsoletes = array_unique(array_diff($fichiers_obsoletes, medias_lister_documents_bdd()));

    foreach ($pattern_obsoletes as $pattern) {
        $repertoires = glob($repertoire_img . $pattern . "*");
        if (is_array($repertoires) and count($repertoires) > 0) {
            $repertoires_obsoletes = array_merge($repertoires_obsoletes, $repertoires);
        }
    }
    // on fusionne avec les fichiers obsolètes
    $repertoires_obsoletes = array_merge($repertoires_obsoletes, $fichiers_obsoletes);

    // on enlève les valeurs vides du tableau.
    $repertoires_obsoletes = array_filter($repertoires_obsoletes);

    if (count($repertoires_obsoletes) > 0) {
        foreach ($repertoires_obsoletes as $repertoire_source) {
            $repertoire_destination = preg_replace($pattern_img, $repertoire_orphelins, $repertoire_source);
            @rename($repertoire_source, $repertoire_destination);
            $message_log[] = date_format(date_create(), 'Y-m-d H:i:s')
            . ' : Déplacement de '
            . $repertoire_source
            . ' vers '
            . $repertoire_destination;
        }
    } else {
        // S'il n'y a pas de dossiers obsolètes, on met un message histoire de ne pas rester dans le brouillard.
        $message_log[] = date_format(date_create(), 'Y-m-d H:i:s')
        . ' : Il n\'y a pas de dossiers ou de fichiers obsolètes';
    }
    spip_log(
        "\n-------\n"
        . join("\n", $message_log)
        . "\n-------\n",
        "medias_nettoyage"
    );
    spip_log(
        date_format(date_create(), 'Y-m-d H:i:s')
        . ' : Fin de la procédure de déplacement des répertoires obsolètes.',
        "medias_nettoyage"
    );
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
function medias_deplacer_documents_repertoire_orphelins ()
{
    /**
     * On crée un log vraiment au début du script.
     * Ainsi, on sait déjà en regardant les logs
     * si le script est lancé ou pas.
     */
    spip_log(
        date_format(date_create(), 'Y-m-d H:i:s')
        . ' : Début de la procédure de déplacement.',
        "medias_nettoyage"
    );

    $fichiers_orphelins     = medias_lister_documents_repertoire_orphelins();
    $fichiers_deplaces  = array();
    $message_log        = array();
    $repertoire_orphelins   = _MEDIAS_NETTOYAGE_REP_ORPHELINS;
    $pattern_img        = "/" . preg_replace("/\//", "\/", _DIR_IMG) . "/";

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
            // On a déjà créé les répertoires d'extensions,
            // mais on laisse cette sécu au cas où on a d'autres répertoires à créer.
            while ($i < $profondeur) {
                $repertoires = $repertoires . $chemin[$i] . '/';
                $i++;
            }
            if (!is_dir($repertoires)) {
                @mkdir($repertoires, _SPIP_CHMOD);
                $message_log[] = date_format(date_create(), 'Y-m-d H:i:s')
                . ' : le répertoire '
                . $repertoires
                . ' a été créé.';
            }
            // Hop, on déplace notre fichier vers IMG/orphelins
            @rename($fichier, $destination);
            $message_log[] = date_format(date_create(), 'Y-m-d H:i:s')
            . ' : le fichier '
            . $fichier
            . ' a été déplacé vers '
            . $destination
            .'.';
            // On construit un tableau dans le cas où qqn voudrait utiliser cette donnée.
            // Pour le moment inutilisé.
            $fichiers_deplaces[] = $destination;
        }
    } else {
        $message_log[] = date_format(date_create(), 'Y-m-d H:i:s')
        . ' : Il ne semble pas avoir de documents orphelins dans IMG/';
    }

    spip_log(
        "\n-------\n"
        . join("\n", $message_log)
        . "\n-------\n",
        "medias_nettoyage"
    );
    /**
     * Et là, on marque bien la fin du script dans les logs.
     */
    spip_log(
        date_format(date_create(), 'Y-m-d H:i:s')
        . ' : Fin de la procédure de déplacement.',
        "medias_nettoyage"
    );

    return true;
}

/**
 * Réparer les documents.
 * Il arrive parfois que suite à un problème de droits,
 * les documents ne soient plus rangés correctement dans IMG/ext/fichier.ext
 * mais dans un faux sous répertoire IMG/ext_fichier.ext
 * Le présent script va recopier les fichiers mal placés,
 * et changer leur référence dans la table spip_documents ;
 * il donnera ensuite la liste des fichiers recopiés et
 * des erreurs recontrées dans un fichier de log.
 *
 * Script repris de ce fichier :
 * http://zone.spip.org/trac/spip-zone/browser/_outils_/repare_doc.html
 *
 * @uses medias_lister_logos_fichiers()
 * @uses _DIR_IMG
 *
 * @return bool
 */
function medias_reparer_documents_fichiers ()
{
    /**
     * On crée un log vraiment au début du script.
     * Ainsi, on sait déjà en regardant les logs
     * si le script est lancé ou pas.
     */
    spip_log(
        date_format(date_create(), 'Y-m-d H:i:s')
        . ' : Début de la procédure de réparation des documents.',
        "medias_nettoyage"
    );

    $repertoire_img     = _DIR_IMG ;
    $docs_fichiers      = array();
    $pattern_img        = "/" . preg_replace("/\//", "\/", $repertoire_img) . "/";
    $message_log        = array();

    // On va chercher dans IMG/*.*
    $fichiers = glob($repertoire_img . "*.*");
    if (is_array($fichiers) and count($fichiers) > 0) {
        foreach ($fichiers as $fichier) {
            $docs_fichiers[] = $fichier;
        }
        $docs_fichiers = array_filter(
            array_diff(
                $docs_fichiers,
                medias_lister_logos_fichiers()
            )
        ); // a voir si on n'a pas de logos ce que ça donne comme ça…
    }
    $docs_fichiers = preg_replace($pattern_img, '', $docs_fichiers);

    if (count($docs_fichiers) > 0) {
        // On va échapper chaque valeur d'url de fichier car
        // il peut arriver d'avoir des apostrophes dans le nom de fichier...
        // #fail
        foreach ($docs_fichiers as $url_fichier) {
            $url_fichiers[] = sql_quote($url_fichier);
        }
        $docs_bdd = sql_allfetsel(
            'id_document,fichier',
            'spip_documents',
            "fichier IN ("
            . join(",", $url_fichiers)
            . ")"
        );

        if (is_array($docs_bdd) and count($docs_bdd) > 0) {
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
                if ($document['fichier'] != $destination
                    and rename($repertoire_img . $document['fichier'], $repertoire_img . $destination)) {
                    sql_updateq(
                        'spip_documents',
                        array('fichier' => $destination),
                        'id_document=' . $document['id_document']
                    );
                    $message_log[] = date_format(date_create(), 'Y-m-d H:i:s')
                    . ' : le fichier '
                    . $repertoire_img
                    . $document['fichier']
                    . ' a été déplacé vers '
                    . $repertoire_img
                    . $destination
                    .'.';
                } else {
                    $message_log[] = date_format(date_create(), 'Y-m-d H:i:s')
                    . ' : le fichier '
                    . $repertoire_img
                    . $document['fichier']
                    . ' n\'a pu être déplacé vers '
                    . $repertoire_img
                    . $destination
                    . '.';
                }
            }
        }
    } else {
        $message_log[] = date_format(date_create(), 'Y-m-d H:i:s') . ' : Il n\'y a pas de documents à réparer.';
    }

    spip_log(
        "\n-------\n"
        . join("\n", $message_log)
        . "\n-------\n",
        "medias_nettoyage"
    );
    /**
     * Et là, on marque bien la fin du script dans les logs.
     */
    spip_log(
        date_format(date_create(), 'Y-m-d H:i:s')
        . ' : Fin de la procédure de réparation des documents.',
        "medias_nettoyage"
    );

    return true;
}

/**
 * Cette fonction vérifie si le fichier est une image ou pas.
 * On fait un test selon l'existence des fonctions PHP qui peuvent nous aider.
 * On évite ainsi une alerte PHP
 * @param  string $fichier
 *         url relative du fichier.
 * @return bool
 */
function medias_est_image ($fichier)
{
    $image = false;
    if (function_exists('exif_imagetype')) {
        if (is_numeric(exif_imagetype($fichier))) {
            $image = true;
        }
    } elseif (function_exists('getimagesize')) {
        if (is_array(getimagesize($fichier))) {
            $image = true;
        }
    }
    return $image;
}
?>
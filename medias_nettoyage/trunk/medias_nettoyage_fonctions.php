<?php
/**
 * Fonctions utilitaires du plugin "Nettoyer la médiathèque"
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

    switch ($mode) {
        case 'on':
            $docs_fichiers_on = array_unique($docs_fichiers_on);
            sort($docs_fichiers_on); // On trie dans l'ordre alphabétique
            return $docs_fichiers_on;
            break;

        case 'off':
            $docs_fichiers_off = array_unique($docs_fichiers_off);
            sort($docs_fichiers_off); // On trie dans l'ordre alphabétique
            return $docs_fichiers_off;
            break;

        default:
            $docs_fichiers = array_unique(array_merge($docs_fichiers_on, $docs_fichiers_off));
            sort($docs_fichiers); // On trie dans l'ordre alphabétique
            return $docs_fichiers;
            break;
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
 * Fonction qui retourne l'id_objet et l'objet à partir du nom du fichier de logo.
 * C'est un peu le chemin inverse de quete_logo().
 * @example
 *         - logo_vers_objet('arton51.png')
 *         - [(#LOGO_OBJET|basename|logo_vers_objet)]
 * @param  string $fichier
 *         Nom du fichier du logo sans chemin.
 * @return mixed
 *         - void : s'il n'y a pas de fichier, on ne renvoie rien
 *         - string : si $info == objet
 *         - integer : si $info == id_objet
 *         - array : si $info == null
 */
function logo_vers_objet ($fichier = null, $info = null)
{
    $logo_type_raccourcis = array(
        'art' => 'article',
        'rub' => 'rubrique',
        'aut' => 'auteur',
        'groupe' => 'groupe'
    );
    global $formats_logos;

    if (is_null($fichier)) {
        return;
    }

    if (preg_match("/(\w+)(on|off)(\d+).(" . join("|", $formats_logos) . ")$/", $fichier, $res)) {
        $id_objet = $res[3];
        if (array_key_exists($res[1], $logo_type_raccourcis)) {
            $objet = $logo_type_raccourcis[$res[1]];
        } else {
            $objet = $res[1];
        }

        switch ($info) {
            case 'objet':
                return $objet;
                break;

            case 'id_objet':
                return $id_objet;
                break;

            default:
                return array('objet' => $objet, 'id_objet' => $id_objet);
                break;
        }

    } else {
        return;
    }
}
/**
 * Retourne le lien `<a>` vers l'objet, avec le titre.
 * Fonction basée sur `lien_objet` du plugin médiathèque,
 * remodelée pour prendre la particularité des logos.
 *
 * @param  null|string  $fichier
 * @param  integer $longueur
 * @param  null|string  $connect
 * @return string
 */
function logo_generer_url_ecrire_objet_titre ($fichier = null, $longueur = 80, $connect = null)
{
    if (is_null($fichier)) {
        return;
    }
    $version_spip = intval(spip_version());

    include_spip('inc/liens');
    $info_objet = logo_vers_objet($fichier);
    $type = $info_objet['objet'];
    $id = $info_objet['id_objet'];

    // Si l'id_objet == 0, on est dans un cas particulier.
    // Nativement, on regarde si objet est une rubrique ou un site.
    // Si c'est une rubrique, on est sur le logo standard des rubriques
    // Si c'est un site, on est sur le logo du site SPIP.
    if ($id == 0) {
        switch ($type) {
            case 'rubrique':
                $exec = ($version_spip == 2) ? 'naviguer' : 'rubriques';
                $url = generer_url_ecrire($exec);
                $titre = _T('ecrire:logo_standard_rubrique');
                break;
            case 'site':
                $exec = ($version_spip == 2) ? 'configuration' : 'configurer_identite';
                $url = generer_url_ecrire($exec);
                $type = 'site_spip';
                $titre = _T('ecrire:logo_site');
                break;
        }
    } else {
        $titre = traiter_raccourci_titre($id, $type, $connect);
        $titre = typo($titre['titre']);
        if (!strlen($titre)) {
            $titre = _T('info_sans_titre');
        }
        $url = generer_url_entite($id, $type);
    }

    return "<a href='$url' class='$type'>" . couper($titre, $longueur) . "</a>";
}

/**
 * Générer l'url de vue d'un objet à partir de son fichier de logo
 *
 * @param  string $fichier
 *         Nom du fichier du logo sans chemin.
 * @return string
 */
function logo_generer_url_ecrire_objet ($fichier = null)
{
    if (is_null($fichier)) {
        return;
    }
    $version_spip = intval(spip_version());

    if ($version_spip == 2) {
        include_spip('base/connect_sql');
    } elseif ($version_spip == 3) {
        include_spip('base/objets');
    }

    include_spip('inc/urls');

    $info_objet = logo_vers_objet($fichier);

    // Si l'id_objet == 0, on est dans un cas particulier.
    // Nativement, on regarde si objet est une rubrique ou un site.
    // Si c'est une rubrique, on est sur le logo standard des rubriques
    // Si c'est un site, on est sur le logo du site SPIP.
    if ($info_objet['id_objet'] == 0) {
        switch ($info_objet['objet']) {
            case 'rubrique':
                $exec = ($version_spip == 2) ? 'naviguer' : 'rubriques';
                return generer_url_ecrire($exec);
                break;
            case 'site':
                $exec = ($version_spip == 2) ? 'configuration' : 'configurer_identite';
                return generer_url_ecrire($exec);
                break;
        }
    } else {
        return generer_url_ecrire(
            $info_objet['objet'],
            id_table_objet($info_objet['objet'])
            . '='
            . intval($info_objet['id_objet'])
        );
    }

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
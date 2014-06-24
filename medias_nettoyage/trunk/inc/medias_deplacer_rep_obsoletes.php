<?php
/**
 * Fonctions d'actions du plugin "Nettoyer la médiathèque"
 *
 * @plugin     Nettoyer la médiathèque
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
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
function inc_medias_deplacer_rep_obsoletes_dist ()
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

?>
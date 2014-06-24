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
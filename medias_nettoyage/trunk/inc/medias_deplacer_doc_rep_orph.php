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
function medias_deplacer_doc_rep_orph ()
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

?>
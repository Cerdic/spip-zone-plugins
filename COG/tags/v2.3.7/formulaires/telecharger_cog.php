<?php


function formulaires_telecharger_cog_charger()
{
    include_spip('cog_config');
    $tab_objet = cog_config_tab_fichier();

    foreach ($tab_objet as $key => &$objet) {
        //var_dump($fichier);
        if (is_array($objet['fichier'])) {
            $objet['fichier'] = implode(',', $objet['fichier']);
        }
        if (preg_match('`^http`i', $objet['fichier'])) {
            $objet = $objet;
        } else {
            unset($tab_objet[$key]);
        }
    }


    return array('tab_objet' => $tab_objet);
}


function formulaires_telecharger_cog_verifier_dist()
{
    include_spip('cog_config');
    include_spip('inc/config');
    $erreurs = array();
    $tab_objet = cog_config_tab_fichier();
    $objet = _request('objet');
    if (count($objet) < 1) {
        $erreurs['message_erreur'] = _T('cog:erreur_choix');
        $erreurs['objet'] = _T('cog:erreur_choix');

    } else {
        if (!isset($tab_objet[$objet[0]])) {
            $erreurs['objet'] = _T('cog:fichier_incorrect');
            $erreurs['message_erreur'] = $erreurs['message_erreur'] . _T('cog:fichier_incorrect');
        }
    }
    return $erreurs;
}


// https://code.spip.net/@inc_editer_mot_dist
function formulaires_telecharger_cog_traiter_dist()
{
    include_spip('cog_config');
    include_spip('inc/cog_import');
    $tab_objet = cog_config_tab_fichier();
    $tab_fichier_telecharger = array();
    $objet_nom = "";
    $objet = _request('objet');
    for ($i = 0; $i < count($objet); $i++) {
        $objet_nom = $objet_nom . $objet[$i] . ' ';
        $tab_fichier = cog_tab_fichier_telecharger($tab_objet[$objet[$i]]['fichier']);
        foreach ($tab_fichier as $fichier) {
            $nom_fichier = cog_telecharger_fichier_distant($fichier);
            if ($nom_fichier) {
                $tab_fichier_telecharger[] = $nom_fichier;
            }
        }
    }
    $retour['editable'] = true;
    if (count($tab_fichier_telecharger) == count($objet)) {
        if (count($objet) == 1) {
            $retour['message_ok'] = 'Le fchier ' . $objet_nom . ' a bien été télécharger, vous pouvez procéder à son importation.';
        } else {
            $retour['message_ok'] = 'Les fichiers ' . $objet_nom . ' ont bien été télécharger, vous pouvez procéder à leur importation.';
        }
    } else {
        $retour['message_erreur'] = 'Problème dans l\'importation du fichier';
    }
    return $retour;

}



function cog_telecharger_fichier_distant($source)
{
    include_spip('inc/distant');
    include_spip('inc/config');

    $fichier = copie_locale($source,'force');

    if (file_exists(_DIR_RACINE .$fichier)){

    $infos_fichier = pathinfo($source);
    $emplacement = sous_repertoire(_DIR_TMP, lire_config('cog/chemin_donnee'));
    @chmod($emplacement, 0777);
    $nom_fichier = $emplacement . $infos_fichier['filename'] . '.' . $infos_fichier['extension'];
    rename(_DIR_RACINE . $fichier, $nom_fichier);
    $infos_fichier = pathinfo($nom_fichier);

// Si c'est un zip on l'extrait
    if ($infos_fichier['extension'] == 'zip') {
        include_spip('inc/pclzip');
        include_spip('inc/joindre_document');
        $archive = new PclZip($nom_fichier);
        $archive->extract(_DIR_TMP);
        $contenu = joindre_decrire_contenu_zip($archive);

        if (isset($contenu[0])) {
            foreach ($contenu[0] as $fichier) {
                if ($fichier['filename'] != "readme.txt")
                    rename(_DIR_TMP . $fichier['filename'], $emplacement . $fichier['filename']);
            }
        }
    }

    unlink($nom_fichier);
   return $nom_fichier;
}
    return false;
}








?>

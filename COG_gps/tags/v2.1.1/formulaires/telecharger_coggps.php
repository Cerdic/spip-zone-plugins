<?php
include_spip("formulaires/telecharger_cog");

function formulaires_telecharger_coggps_charger()
{

    include_spip('coggps_config');
    $tab_objet = coggps_config_tab_fichier();
    foreach ($tab_objet as $key => &$objet) {
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


function formulaires_telecharger_coggps_verifier_dist()
{
    include_spip('coggps_config');
    include_spip('inc/config');
    $erreurs = array();
    $tab_objet = coggps_config_tab_fichier();

    // login trop court ou existant
    if ($objet = _request('objet')) {
        if (!isset($tab_objet[$objet])) {
            $erreurs['objet'] = _T('coggps:fichier_incorrect');
            $erreurs['message_erreur'] .= _T('coggps:fichier_incorrect');
        }
    } else {
        $erreurs['objet'] = _T('coggps:choix_erronne');

    }

    return $erreurs;
}

// https://code.spip.net/@inc_editer_mot_dist
function formulaires_telecharger_coggps_traiter_dist()
{
    include_spip('coggps_config');
    include_spip('inc/cog_import');
    $tab_objet = coggps_config_tab_fichier();
    $tab_fichier_telecharger = array();
    $objet_nom = "";
    $objet = _request('objet');
    for ($i = 0; $i < count($objet); $i++) {

       $tab_fichier = cog_tab_fichier_telecharger($tab_objet[$objet]['fichier']);
        foreach ($tab_fichier as $fichier) {

            $nom_fichier = cog_telecharger_fichier_distant($fichier);
            if ($nom_fichier) {
                $tab_fichier_telecharger[] = $nom_fichier;
            }
        }
    }


    $retour['editable'] = true;
    if (count($tab_fichier_telecharger) == count($tab_fichier)) {
        $retour['message_ok'] = 'Le ou les fichier(s) ' . $objet . ' a bien été télécharger, vous pouvez procéder à son importation.';
    } else {
        $retour['message_erreur'] = 'Problème dans le téléchargement du fichier';
    }
    return $retour;

}


function coggps_telecharger_fichier_distant($source, $nom_fichier_txt)
{
    include_spip('inc/distant');
    include_spip('inc/config');
    $fichier = copie_locale($source, 'force');
    $infos_fichier = pathinfo($source);
    $emplacement = _DIR_TMP . lire_config('cog/chemin_donnee');
    $nom_fichier = $emplacement . $infos_fichier['filename'] . '.' . $infos_fichier['extension'];
    if (empty($nom_fichier_txt)) {
        $nom_fichier_txt = $emplacement . $infos_fichier['filename'] . '.txt';
    } else {
        $nom_fichier_txt = $emplacement . $nom_fichier_txt;
    }
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
                if ($fichier['filename'] != "readme.txt") {
                    rename(_DIR_RACINE . $fichier['filename'], $nom_fichier_txt);
                }
            }
        }
        unlink($nom_fichier);
    }

    return $nom_fichier;

}


?>

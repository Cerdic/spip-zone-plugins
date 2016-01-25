<?php

/*
 * Plugin Titre de logo
 *
 * Distribue sous licence GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

include_spip('base/abstract_sql');
include_spip('inc/config');

function formulaires_editer_titre_logo_charger_dist($objet, $id_objet, $retour = '', $options = array()) {
    
    $exec = (_request('exec')) ? _request('exec') : '';
    $objet = objet_type($objet);
    $table_objet = table_objet_sql($objet);
    $_id_objet = id_table_objet($objet);
    $titre_logo = '';
    $descriptif_logo = '';
    $objets_autorises = lire_config('titre_logo/objets_autorises');
    $objets_autorises = (isset($objets_autorises))
        ? array_filter($objets_autorises)
        : array();

    // Si on est sur ?exec=configurer_identite
    // on n'affiche pas le formulaire.
    if ($exec == 'configurer_identite') {
        return false;
    } elseif (!in_array($table_objet, $objets_autorises)) {
        return false;
    }
    // Si on a des options, on les stocke
    if (!is_array($options)) {
        $options = unserialize($options);
        if (is_array($options) and array_key_exists('_pipelines', $options)) {
            // Comme on est un formulaire dans un formulaire,
            // on enlève le _pipeline du premier formulaire
            unset($options['_pipelines']);
        }
        if (is_array($options) and array_key_exists('_options', $options)) {
            $options = array_merge($options, $options['_options']);
            // Comme on est un formulaire dans un formulaire,
            // on enlève le _options du premier formulaire
            unset($options['_options']);
        }
    }

    $requete_sql = sql_fetsel('titre_logo,descriptif_logo', $table_objet, "$_id_objet=$id_objet");
    if ($requete_sql) {
        $titre_logo = $requete_sql['titre_logo'];
        $descriptif_logo = $requete_sql['descriptif_logo'];
    }

    $valeurs = array(
        'objet'     => $objet,
        'id_objet'  => $id_objet,
        'titre_logo' => $titre_logo,
        'descriptif_logo' => $descriptif_logo,
        'exec' => $exec,
        //'logo_on' => $options['logo_on'],
        '_options' => $options,
        );

    return $valeurs;
}

function formulaires_editer_titre_logo_verifier_dist($objet, $id_objet, $retour = '', $options = array()) {
    $erreurs = array();

    return $erreurs;
}

function formulaires_editer_titre_logo_traiter_dist($objet, $id_objet, $retour = '', $options = array()) {
    $res = array();
    $table_objet = table_objet_sql($objet);
    $titre_logo = _request('titre_logo');
    $descriptif_logo = _request('descriptif_logo');
    $_id_objet = id_table_objet($objet);

    sql_updateq(
        $table_objet,
        array('titre_logo' => $titre_logo, 'descriptif_logo' => $descriptif_logo),
        $_id_objet.'='.$id_objet
    );
    $update_sql = sql_fetsel(
        'titre_logo,descriptif_logo',
        $table_objet,
        'titre_logo='.sql_quote($titre_logo).' AND descriptif_logo='.sql_quote($descriptif_logo)
    );

    if ($update_sql) {
        $res['message_ok'] = _T('info_modification_enregistree');
        $res['redirect'] = "index.php?exec=$objet&$_id_objet=$id_objet";
    } else {
        $res['message_erreur'] = _T('avis_erreur');
    }

    return $res;
}

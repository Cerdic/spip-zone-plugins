<?php

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

include_spip('inc/autoriser');
include_spip('inc/actions');
include_spip('inc/editer');

// chargement des valeurs par defaut des champs du formulaire
function formulaires_editer_abomailman_charger_dist($id_abomailman = 'new', $retour = '', $config_fonc = '', $row = array(), $hidden = '')
{
    $valeurs = array();

    //initialise les variables d'environnement pas défaut
    if (!autoriser('creer', 'abomailman', 'oui')) {
        $editable = false;
    } else {
        $valeurs = formulaires_editer_objet_charger('abomailman', $id_abomailman, 0, 0, $retour, $config_fonc, $row, $hidden);
        $editable = true;
    }

    if (!$valeurs['langue']) {
        $valeurs['langue'] = lang_select();
    }
    unset($valeurs['lang']);

    $recuptemplate = explode('&', _request('modele_defaut'));
    $valeurs['template'] = $recuptemplate[0];
    $valeurs['envoi_liste_parametres'] = recup_param(_request('modele_defaut'));
    $valeurs['editable'] = $editable;

    return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui
 * ne representent pas l'objet edite.
 */
function formulaires_editer_abomailman_identifier_dist($id_abomailman = 'new', $retour = '', $associer_objet = '', $config_fonc = 'auteurs_edit_config', $row = array(), $hidden = '')
{
    return serialize(array(intval($id_abomailman), $associer_objet));
}

function formulaires_editer_abomailman_verifier_dist($id_abomailman = 'new', $retour = '', $config_fonc = '', $row = array(), $hidden = '')
{

    //initialise le tableau des erreurs
    $erreurs = formulaires_editer_objet_verifier('abomailman', $id_abomailman, array('titre', 'email'));
    spip_log($erreurs, 'test');
    // Faire une fonction de verif sur le mail et le titre pour validite
    $desactive = _request('desactive');

    // Si on fait une suppression, on ne vérifie pas le reste
    if ($desactive != '2') {
        if (count($erreurs) < 1) {
            include_spip('inc/filtres'); # pour email_valide()
            if (!email_valide(_request('email'))) {
                $erreurs['email'] = _T('abomailmans:email_valide');
            }
        }
    }

    //message d'erreur genéralisé
    if (count($erreurs) > 0) {
        $erreurs['message_erreur'] .= _T('abomailmans:verifier_formulaire');
    }

    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

function formulaires_editer_abomailman_traiter_dist($id_abomailman = 'new', $retour = '', $config_fonc = '', $row = array(), $hidden = '')
{
    $res = formulaires_editer_objet_traiter('abomailman', $id_abomailman, 0, 0, $retour, $config_fonc, $row, $hidden);

    $message = array();
    $valeurs['envoi_liste_parametres'] = _request('envoi_liste_parametres');

    $datas = array();

    // Récupération des données
    $datas['titre'] = _request('titre');
    $datas['descriptif'] = _request('descriptif');
    if (_request('abo_type') && in_array(_request('abo_type'), array('news', 'ml'))) {
        $datas['abo_type'] = _request('abo_type');
    }
    $datas['email'] = _request('email');
    $datas['email_subscribe'] = _request('email_subscribe');
    $datas['email_unsubscribe'] = _request('email_unsubscribe');
    $datas['email_sympa'] = _request('email_sympa');
    $datas['desactive'] = _request('desactive');
    $datas['modele_defaut'] = str_replace('\'', '', _request('template')).''.$valeurs['envoi_liste_parametres'];
    $datas['periodicite'] = _request('periodicite');
    $datas['lang'] = _request('langue');

    // on récupère les données de la liste
    if (intval($id_abomailman)) {
        if ($datas['desactive'] == '2') {
            sql_delete('spip_abomailmans', "id_abomailman = $id_abomailman");
            $message['message_ok'] = _T('abomailmans:liste_supprimee', array('id' => $id_abomailman, 'titre' => $datas['titre']));
            $message['editable'] = false;
        } else {
            sql_updateq('spip_abomailmans', $datas, "id_abomailman = $id_abomailman");
            $message['message_ok'] = _T('abomailmans:liste_updatee', array('id' => $id_abomailman, 'titre' => $datas['titre']));
        }
    } else {
        $message['message_ok'] = _T('abomailmans:liste_creee', array('id' => $id_abomailman, 'titre' => $datas['titre']));
        $message['editable'] = false;
    }

    if (!$retour) {
        $message['redirect'] = parametre_url(parametre_url(self(), 'id_abomailman', $res['id_abomailman']), 'abomailman', '');
    } else {
        // sinon on utilise la redirection donnee.
        $message['redirect'] = parametre_url($retour, 'id_abomailman', $res['id_abomailman']);
    }

    return $message;
}

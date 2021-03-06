<?php

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

include_spip('inc/abomailmans');
include_spip('inc/distant');

// chargement des valeurs par defaut des champs du formulaire
function formulaires_abomailman_envoi_liste_charger_dist()
{
    //initialise les variables d'environnement pas défaut
    $valeurs = array();
    if (autoriser('modifier', 'abomailman')) {
        $valeurs['editable'] = true;
    } else {
        return $valeurs['editable'] = false;
    }

    //$valeurs['id_abomailman'] = _request('id_abomailman');
    $valeurs['sujet'] = _request('sujet');
    $valeurs['template'] = _request('template');
    $valeurs['message'] = _request('message');
    $valeurs['date'] = _request('date');
    $valeurs['id_rubrique'] = _request('id_rubrique');
    $valeurs['id_mot'] = _request('id_mot');

    return $valeurs;
}

function formulaires_abomailman_envoi_liste_verifier_dist()
{

    //initialise le tableau des erreurs
    $erreurs = array();

    //$valeurs['id_abomailman'] = _request('id_abomailman');
    $valeurs['sujet'] = _request('sujet');
    $valeurs['template'] = _request('template');
    $valeurs['message'] = _request('message');
    $valeurs['id_rubrique'] = _request('id_rubrique');
    $valeurs['id_mot'] = _request('id_mot');

    if ($date = recup_date(_request('date'))) {    //On met la date saisie au format MySql AAAA-MM-JJ
        $valeurs['date'] = date('Y-m-d', mktime($date[3], $date[4], $date[5], $date[1], $date[2], $date[0]));
    } else { // Devrait pas arriver mais bon
        $valeurs['date'] = date('Y-m-d');
    }
    // On reinjecte dans le bon format
    set_request('date', $valeurs['date']);

    if (!$valeurs['sujet']) {
        $erreurs['sujet'] = _T('abomailmans:sujet_obligatoire');
    }

    if (count($erreurs)) {
        refuser_traiter_formulaire_ajax();
        $erreurs['message_erreur'] .= _T('abomailmans:verifier_formulaire');
    }

    if (!count($erreurs) and !_request('confirmer_previsu_abomailman')) {
        $previsu = abomailmain_inclure_previsu($valeurs);
        $erreurs['previsu'] = $previsu;
    }

    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

function abomailmain_inclure_previsu($datas)
{
    $datas['bouton'] = _T('abomailmans:envoi_confirmer');
    $datas['template_html'] = recuperer_fond('abomailman_template', $datas);
    $datas['envoi_txt'] = 'oui';
    $datas['template_txt'] = recuperer_fond('abomailman_template', $datas);

    return recuperer_fond('formulaires/inc-previsu_mail', $datas);
}

function formulaires_abomailman_envoi_liste_traiter_dist()
{
    refuser_traiter_formulaire_ajax();

    $query = array();
    $nom_site = lire_meta('nom_site');
    $email_webmaster = lire_meta('email_webmaster');
    $charset = lire_meta('charset');
    $email_receipt = _request('email_liste');
    $sujet = _request('sujet');

   // Recuperation des donnees
    //$query['id_abomailman'] = _request('id_abomailman');
    $query['template'] = _request('template');
    $query['sujet'] = _request('sujet');
    $query['message'] = _request('message');
    $query['id_rubrique'] = _request('id_rubrique');
    $query['id_mot'] = _request('id_mot');

    if ($date = recup_date(_request('date'))) {    //On met la date saisie au format MySql AAAA-MM-JJ
        $query['date'] = date('Y-m-d', mktime($date[3], $date[4], $date[5], $date[1], $date[2], $date[0]));
    } else { // Devrait pas arriver mais bon
        $query['date'] = date('Y-m-d');
    }

    $fond = recuperer_fond('abomailman_template', $query);
    $body = array(
        'html' => $fond,
    );
    /* Format Texte */
    $query['envoi_txt'] = 'oui';
    $body['texte'] = recuperer_fond('abomailman_template', $query);

    if (strlen($fond) > 10) {
        // email denvoi depuis config facteur
        if ($GLOBALS['meta']['facteur_adresse_envoi'] == 'oui'
              and $GLOBALS['meta']['facteur_adresse_envoi_email']) {
            $from_email = $GLOBALS['meta']['facteur_adresse_envoi_email'];
        } else {
            $from_email = $email_webmaster;
        }
        // nom denvoi depuis config facteur
        if ($GLOBALS['meta']['facteur_adresse_envoi'] == 'oui'
              and $GLOBALS['meta']['facteur_adresse_envoi_nom']) {
            $from_nom = $GLOBALS['meta']['facteur_adresse_envoi_nom'];
        } else {
            $from_nom = $nom_site;
        }

        if (abomailman_mail($from_nom, $from_email, '', $email_receipt, $sujet, $body, true, $charset)) {
            $message = _T('abomailmans:email_envoye', array('liste' => $email_receipt));
        } else {
            $message = _T('pass_erreur_probleme_technique');
        }
    } else {
        $message = _T('abomailmans:contenu_insuffisant');
    }

    return array('message_ok' => $message);
}

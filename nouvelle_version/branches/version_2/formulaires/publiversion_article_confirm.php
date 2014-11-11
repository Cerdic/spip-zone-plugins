<?php
/***************************************************************************\
 * Plugin Nouvelle Version pour Spip 2.0
 * Licence GPL (c) 2011-2014
 * Modération de la nouvelle version d'un article
 *
\***************************************************************************/

function formulaires_publiversion_article_confirm_charger_dist()
{
    $valeurs = array();

    return $valeurs;
}

function formulaires_publiversion_article_confirm_verifier_dist($article, $article_orig)
{
    $erreurs = array();

    if (!$article || !$article_orig) {
        $erreurs['message_erreur'] = 'Une erreur est survenue.';
    }

    return $erreurs;
}

function formulaires_publiversion_article_confirm_traiter_dist($article, $article_orig, $newstatut = 'poubelle')
{

    if (_request('confirmer')) {
        include_spip('action/remplacer');

        spip_log("ID ARTICLE CIBLE : $article");
        spip_log("ID ARTICLE VERSION : $article_orig");

        $remplacer_article = remplacer_article(intval($article), intval($article_orig), $newstatut);

        $message = array('message_ok'=>array(
        'message'=>_T('versioning:operation_executee'),
        'cible'=>$article_orig,
        'type_retour'=>_T('versioning:operation_retour_ok_article_publi')
        ));
    }
    if (_request('annuler')) {
        $message = array('message_ok'=>array(
        'message'=>_T('versioning:operation_annulee'),
        'cible'=>$article,
        'type_retour'=>_T('versioning:operation_retour_ko_article')
        ));
    }

    return $message;
}

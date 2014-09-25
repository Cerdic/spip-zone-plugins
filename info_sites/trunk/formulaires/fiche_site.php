<?php

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

include_spip('base/abstract_sql');
include_spip('inc/session');

function formulaires_fiche_site_charger_dist()
{
    // Valeurs du formulaire.
    $valeurs = array(
        'organisation'     => _request('organisation'),
        'projet'           => _request('projet'),
        'projet_parent'    => _request('projet_parent'),
        'webservice'       => _request('webservice'),
        'titre'            => _request('titre'),
        'versioning_trac'  => _request('versioning_trac'),
        'versioning_type'  => _request('versioning_type'),
        'versioning_path'  => _request('versioning_path'),
        'versioning_rss'   => _request('versioning_rss'),
        'logiciel_nom'     => _request('logiciel_nom'),
        'logiciel_version' => _request('logiciel_version'),
        'fo_url'           => _request('fo_url'),
        'fo_login'         => _request('fo_login'),
        'fo_password'      => _request('fo_password'),
        'bo_url'           => _request('bo_url'),
        'bo_login'         => _request('bo_login'),
        'bo_password'      => _request('bo_password'),
    );
    $valeurs['type_site'] = (_request('type_site')) ? _request('type_site') : 'prod';

    return $valeurs;
}

/*
*   Fonction de vérification, cela fonction avec un tableau d'erreur.
*   Le tableau est formater de la sorte:
*   if (!_request('NomErreur')) {
*       $erreurs['message_erreur'] = '';
*       $erreurs['NomErreur'] = '';
*   }
*   Pensez à utiliser _T('info_obligatoire'); pour les éléments obligatoire.
*/
function formulaires_fiche_site_verifier_dist()
{
    $erreurs = array();

    $obligatoires = array('logiciel_nom', 'logiciel_version', 'titre', 'type_site', 'fo_url', 'bo_url');
    foreach ($obligatoires as $obligatoire) {
        if (!_request($obligatoire)) {
            $erreurs[$obligatoire] = _T('info_obligatoire');
        }
    }

    return $erreurs;
}

function formulaires_fiche_site_traiter_dist()
{
    $res                 = array();
    $liste_plugins       = isset($GLOBALS['meta']['plugin'])? unserialize($GLOBALS['meta']['plugin']) : array();
    $id_ateur            = session_get('id_auteur');

    // --------------------
    // On récupère les valeurs transmises par le formulaire
    $organisation        = trim(_request('organisation')); // On enlève les espaces inutiles avant et après
    $projet              = trim(_request('projet')); // On enlève les espaces inutiles avant et après
    $projet_parent       = trim(_request('projet_parent')); // On enlève les espaces inutiles avant et après
    $webservice          = trim(_request('webservice'));

    // On retrouve la clé (cf. uniqid) à partir du webservice
    if ($webservice) {
        $parse_url       = parse_url($webservice);
        parse_str($parse_url['query'], $query);
        if (isset($query['cle'])) {
            $uniqid      = $query['cle'];
        }
    }

    $titre               = trim(_request('titre'));
    $type_site           = trim(_request('type_site'));
    if (in_array('rss_commits', $liste_plugins)) {
        $rss_commits     = true;
        $versioning_trac = trim(_request('versioning_trac'));
        $versioning_type = trim(_request('versioning_type'));
        $versioning_path = trim(_request('versioning_path'));
        $versioning_rss  = trim(_request('versioning_rss'));
    }
    $logiciel_nom        = trim(_request('logiciel_nom'));
    $logiciel_version    = trim(_request('logiciel_version'));
    $fo_url              = trim(_request('fo_url'));
    $fo_login            = trim(_request('fo_login'));
    $fo_password         = trim(_request('fo_password'));
    $bo_url              = trim(_request('bo_url'));
    $bo_login            = trim(_request('bo_login'));
    $bo_password         = trim(_request('bo_password'));
    $date                = date_format(date_create(), 'Y-m-d H:i:s');

    // --------------------
    // On s'occupe du projet parent
    if ($projet_parent) {
        $_id_projet_parent = intval($projet_parent);
        // ne pas oublier que intval() retournera '0' si on lui passe une chaine.
        // Exemple : intval('Tartanpion') == 0
        // intval('1Live') == 1
        // Pour ce dernier exemple, on met une sécu...
        if (strlen($_id_projet_parent) != strlen($projet_parent)) {
            $_id_projet_parent = $projet_parent;
        }

        if (!is_int($_id_projet_parent) or $_id_projet_parent == 0) {
            $id_projet_parent = sql_insertq('spip_projets', array('nom' => $projet_parent));
        } elseif (is_int($_id_projet_parent)) {
            $id_projet_parent = $_id_projet_parent;
        }
    }

    // --------------------
    // On s'occupe du projet
    if ($projet) {
        $champs = array();
        $_id_projet = intval($projet);

        if (strlen($_id_projet) != strlen($projet)) {
            $_id_projet = $projet;
        }

        if ($projet and (!is_int($_id_projet) or $_id_projet == 0)) {
            $champs['nom']                    = $projet;
            $champs['url_site']               = $fo_url;
            $champs['date_publication']       = $date;
            if ($rss_commits) {
                $champs['versioning_trac']    = $versioning_trac;
                $champs['versioning_type']    = $versioning_type;
                $champs['versioning_path']    = $versioning_path;
                $champs['versioning_rss']     = $versioning_rss;
            }
            if ($id_projet_parent and $id_projet_parent != $projet) {
                $champs['id_projet_parent']   = $id_projet_parent;
            }
            if ($type_site == 'prod') {
                $champs['statut']  = 'production';
            }
            if ($type_site == 'rec') {
                $champs['statut']  = 'test';
            }
            if ($type_site == 'prep') {
                $champs['statut']  = 'recette';
            }
            if ($type_site == 'dev') {
                $champs['statut']  = 'fabrication';
            }
            $id_projet = sql_insertq('spip_projets', $champs);
            if (is_int($id_projet) and is_int($id_auteur)) {
                // On reprend le comportement du plugin PROJETS quand on crée un nouveau projet,
                // on lie l'auteur au projet qu'il a créé
                sql_insertq('spip_auteurs_liens', array('id_auteur' => $id_auteur, 'id_objet' => $id_projet, 'objet' => 'projet'));
            }
        } elseif (is_int($_id_projet)) {
            $id_projet = $_id_projet;
        }
    }

    // --------------------
    // On s'occupe de l'organisation
    if ($organisation) {
        $_id_organisation = intval($organisation);

        if (strlen($_id_organisation) != strlen($organisation)) {
            $_id_organisation = $organisation;
        }

        if (!is_int($_id_organisation) or $_id_organisation == 0) {
            $id_organisation = sql_insertq('spip_organisations', array('nom' => $organisation));
            if (is_int($id_projet_parent) and is_int($id_organisation)) {
                sql_insertq('spip_projets_liens', array('id_projet' => $id_projet_parent, 'id_objet' => $id_organisation, 'objet' => 'organisation'));
            }
            if (is_int($id_projet) and is_int($id_organisation)) {
                sql_insertq('spip_projets_liens', array('id_projet' => $id_projet, 'id_objet' => $id_organisation, 'objet' => 'organisation'));
            }
        } elseif (is_int($_id_organisation)) {
            sql_insertq('spip_projets_liens', array('id_projet' => $id_projet, 'id_objet' => $_id_organisation, 'objet' => 'organisation'));
        }
    }

    // --------------------
    // Et enfin, on traite le site du projet
    if ($titre) {
        $champs                     = array();
        $champs['titre']            = $titre;
        $champs['type_site']        = ($type_site) ? $type_site : 'prod';
        $champs['webservice']       = $webservice;
        $champs['uniqid']           = ($uniqid) ? $uniqid : '';
        $champs['logiciel_nom']     = $logiciel_nom;
        $champs['logiciel_version'] = $logiciel_version;
        $champs['fo_url']           = $fo_url;
        $champs['fo_login']         = $fo_login;
        $champs['fo_password']      = $fo_password;
        $champs['bo_url']           = $bo_url;
        $champs['bo_login']         = $bo_login;
        $champs['bo_password']      = $bo_password;
        $champs['date_creation']    = $date;
        $id_projets_site = sql_insertq('spip_projets_sites', $champs);
        if (is_int($id_projets_site) and is_int($id_projet) and $id_projet != 0) {
            sql_insertq('spip_projets_sites_liens', array('id_projets_site' => $id_projets_site, 'id_objet' => $id_projet, 'objet' => 'projet'));
        }
    }

    if (!$id_projets_site) {
        $res['message_erreur']      = _T('enregistrement_ko');
    } else {
        $res['message_ok']          = _T('enregistrement_ok');
        $res['redirect']            = generer_url_entite($id_projets_site, 'projets_site');
    }

    return $res;
}
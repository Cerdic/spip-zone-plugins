<?php

// Les filtres pour les squeletes
function lienscontenus_generer_url($type_objet, $id_objet)
{
    $liste_urls = array(
        'rubrique' => array('naviguer', 'id_rubrique'),
        'article' => array('articles', 'id_article'),
        'breve' => array('breves_voir', 'id_breve'),
        'syndic' => array('sites', 'id_syndic'),
        'mot' => array('mots_edit', 'id_mot'),
        'auteur' => array('auteur_infos', 'id_auteur'),
        'form' => array('forms_edit', 'id_form')
        // TODO : Ajouter les autres
    );
    if (isset($liste_urls[$type_objet])) {
    	return $GLOBALS['meta']['adresse_site'].'/ecrire/?exec='.$liste_urls[$type_objet][0].'&amp;'.$liste_urls[$type_objet][1].'='.$id_objet;
    } else {
        $f = 'lienscontenus_generer_url_'.$type_objet;
        if (function_exists($f)) {
            return $f($id_objet);
        } else {
            // On ne devrait pas se retrouver là
            spip_log('Plugin liens_contenus : il manque une fonction de génération d\'url pour le type '.$type_objet, 'liens_contenus');
        	return '#';
        }
    }
}

function lienscontenus_generer_url_document($id_objet)
{
    include_spip('base/abstract_sql');
    $query = 'SELECT id_article FROM spip_documents_articles WHERE id_document='._q($id_objet);
    $res = spip_query($query);
    if (spip_num_rows($res) == 1) {
        $row = spip_fetch_array($res);
        return lienscontenus_generer_url('article', intval($row['id_article']));
    } else {
        $query = 'SELECT id_rubrique FROM spip_documents_rubriques WHERE id_document='._q($id_objet);
        $res = spip_query($query);
        if (spip_num_rows($res) == 1) {
            $row = spip_fetch_array($res);
            return lienscontenus_generer_url('rubrique', intval($row['id_rubrique']));
        }
    }
    // D'autres possibilites ???
    // A quoi servent les tables spip_documents_breves et spip_documents_donnees ?
}

function lienscontenus_generer_url_modele($id_objet)
{
    return find_in_path('modeles/'.$id_objet.'.html');
}

function lienscontenus_generer_url_modele_non_reconnu($id_objet)
{
    return '#';
}

function lienscontenus_verifier_si_existe($type_objet, $id_objet)
{
    switch ($type_objet) {
    	case 'modele':
            if(find_in_path('modeles/'.$id_objet.'.html')) {
                return 'ok';
            } else {
                return 'ko';
            }
            break;
        default:
            include_spip('base/abstract_sql');
            if (in_array($type_objet, array('syndic', 'forum'))) {
                $query = 'SELECT COUNT(*) AS nb FROM spip_'.$type_objet.' WHERE id_'.$type_objet.'='._q($id_objet);
            } else {
                // Marche aussi pour les formulaires (type = "form")
                $query = 'SELECT COUNT(*) AS nb FROM spip_'.$type_objet.'s WHERE id_'.$type_objet.'='._q($id_objet);
            }
            $res = spip_query($query);
            $row = spip_fetch_array($res);
            if ($row['nb'] == 1) {
                return 'ok';
            } else {
                return 'ko';
            }
    }
}

function lienscontenus_icone_statut($type_objet, $id_objet)
{
    $listeStatuts = array('prepa', 'prop', 'publie', 'refuse', 'poubelle');
    include_spip('base/abstract_sql');
    if (in_array($type_objet, array('syndic', 'forum'))) {
        $query = 'SELECT statut FROM spip_'.$type_objet.' WHERE id_'.$type_objet.'='._q($id_objet);
    } else {
        // Marche aussi pour les formulaires (type = "form")
        $query = 'SELECT statut FROM spip_'.$type_objet.'s WHERE id_'.$type_objet.'='._q($id_objet);
    }
    if ($res = spip_query($query)) {
        $row = spip_fetch_array($res);
        if (in_array($row['statut'], $listeStatuts)) {
            return '<img src="'._DIR_PLUGIN_LIENSCONTENUS.'/images/statut-'.$row['statut'].'.gif" align="absmiddle" alt="'._T('lienscontenus:statut_'.$row['statut']).'" />';      
        } else {
            return '';
        }
    } else {
    	return '';
    }
}

function lienscontenus_statut($type_objet, $id_objet)
{
    $listeStatuts = array('prepa', 'prop', 'publie', 'refuse', 'poubelle');
    include_spip('base/abstract_sql');
    if (in_array($type_objet, array('syndic', 'forum'))) {
        $query = 'SELECT statut FROM spip_'.$type_objet.' WHERE id_'.$type_objet.'='._q($id_objet);
    } else {
        // Marche aussi pour les formulaires (type = "form")
        $query = 'SELECT statut FROM spip_'.$type_objet.'s WHERE id_'.$type_objet.'='._q($id_objet);
    }
    if ($res = spip_query($query)) {
        $row = spip_fetch_array($res);
        if (in_array($row['statut'], $listeStatuts)) {
            return $row['statut'];
        } else {
            return '';
        }
    } else {
        return '';
    }
}
?>
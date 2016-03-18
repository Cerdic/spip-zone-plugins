<?php

/**
 * Définit les fonctions du plugin Info Sites.
 *
 * @plugin     Info Sites
 *
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

include_spip('inc/filtres_ecrire');
include_spip('base/abstract_sql');
include_spip('base/objets');

function lister_tables_liens()
{
    $tables_auxilaires = lister_tables_auxiliaires();
    $tables_auxilaires_objets = array();

    foreach ($tables_auxilaires as $key => $table_auxilaire) {
        if (isset($table_auxilaire['field']['objet'])) {
            $tables_auxilaires_objets[] = $key;
        }
    }

    return $tables_auxilaires_objets;
}

function nb_elements($table, $where = '')
{
    return sql_countsel($table, $where);
}

function nb_organisations($where = '')
{
    return nb_elements('spip_organisations', $where);
}

function nb_projets($where = '')
{
    return nb_elements('spip_projets', $where);
}

function nb_projets_sites($where = '')
{
    return nb_elements('spip_projets_sites', $where);
}

function nb_projets_sites_types($type_site = 'prod')
{
    return nb_projets_sites("type_site='".$type_site."'");
}

function nb_projets_cadres($where = '')
{
    return nb_elements('spip_projets_cadres', $where);
}

function nb_contacts($where = '')
{
    return nb_elements('spip_contacts', $where);
}

/**
 * Lister les sites de projets ayant un webservice renseigné.
 *
 * @return array Liste des sites de projets
 */
function sites_webservices()
{
    include_spip('base/abstract_sql');
    $sites_id = array();
    $sites_projets = sql_allfetsel('id_projets_site', 'spip_projets_sites', "webservice!=''");
    if (is_array($sites_projets) and count($sites_projets) > 0) {
        foreach ($sites_projets as $site_projet) {
            $sites_id[] = $site_projet['id_projets_site'];
        }
    } else {
        // S'il n'y a aucun site de projet avec un webservice renseigné,
        // on renvoie false.
        $sites_id = false;
    }

    return $sites_id;
}

/**
 * Lister les sites projets ayant des plugins à mettre à jour.
 * La première version de cette fonction est pour le CMS SPIP. Il faudra l'adapter pour les autres CMS.
 *
 * @uses formater_tableau()
 *
 * @return array
 */
function sites_projets_maj_plugins()
{
    include_spip('base/abstract_sql');
    $sites_projets = sites_webservices();
    $liste_plugins = array();

    if (is_array($sites_projets)) {
        $liste_sites_projets_plugins = sql_allfetsel('id_projets_site, logiciel_nom, logiciel_plugins', 'spip_projets_sites', 'id_projets_site IN ('.implode(',', $sites_projets).") AND logiciel_plugins!=''");
        if (is_array($liste_sites_projets_plugins) and count($liste_sites_projets_plugins) > 0) {
            foreach ($liste_sites_projets_plugins as $key => $site_projet) {
                $liste_plugins_tmp = formater_tableau($site_projet['logiciel_plugins']);
                foreach ($liste_plugins_tmp as $key => $plugin) {
                    switch (strtolower($site_projet['logiciel_nom'])) {
                        case 'spip':
                            # code...
                            break;

                        default:
                            # code...
                            break;
                    }
                }
                $liste_plugins = array_merge($liste_plugins, $liste_plugins_tmp);
            }
        }
    }

    return $liste_plugins;
}

function plugins_spip($plugins = array(), $branche_spip = null)
{
    if (is_array($plugins) and count($plugins) > 0 and !is_null($branche_spip)) {
    }
}

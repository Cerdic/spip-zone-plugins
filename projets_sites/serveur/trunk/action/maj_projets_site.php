<?php

/**
 * Mettre à jour un site de projet
 *
 * @plugin     Sites pour projets
 * @copyright  2013-2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Projets_sites\Action
 */

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

/**
 * Action pour mettre à jour un site à partir du webservice
 *
 * @param null|int $id
 *     `id` : son identifiant. En absence de `id` utilise l'argument de l'action sécurisée.
**/
function action_maj_projets_site_dist($id = null)
{
    if (is_null($id)) {
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $id = $securiser_action();
    }
    $id_projets_site = intval($id);

    if ($id_projets_site) {

        include_spip('base/abstract_sql');
        $analyser_webservice = charger_fonction('analyser_webservice', 'inc');

        $webservice = sql_fetsel('webservice,id_projets_site,fo_login,fo_password', 'spip_projets_sites', "webservice!='' AND id_projets_site=$id_projets_site");

        if (is_array($webservice) and count($webservice) > 0) {
            $champs = $analyser_webservice($webservice['webservice'], $webservice['fo_login'], $webservice['fo_password']);
            if ($champs and count($champs) > 0) {
                sql_updateq('spip_projets_sites', $champs, 'id_projets_site=' . $webservice['id_projets_site']);
                spip_log(_T('projets_site:maj_webservice_log_ok', array('id' => $webservice['id_projets_site'], 'webservice' => $webservice['webservice'])), 'projets_sites');
            } else {
                spip_log(_T('projets_site:maj_webservice_log_ko', array('id' => $webservice['id_projets_site'], 'webservice' => $webservice['webservice'])), 'projets_sites');
            }
        } else {
            spip_log(_T('projets_site:maj_webservice_log_ko', array('id' => $id_projets_site, 'webservice' => $webservice['webservice'])), 'projets_sites');
        }

    } else {
        spip_log(__FUNCTION__ . " $id pas compris");
    }
}

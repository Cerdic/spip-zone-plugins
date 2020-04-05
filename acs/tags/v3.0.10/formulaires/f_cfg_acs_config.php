<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt
#

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Traitement post du formulaire formulaires/f_cfg_acs_config.html
 * @return array
 */
function formulaires_f_cfg_acs_config_traiter_dist() {
	refuser_traiter_formulaire_ajax();
	if (
			($GLOBALS['meta']['ACS_PREVIEW_BACKGROUND'] != _request('ACS_PREVIEW_BACKGROUND')) ||
			($GLOBALS['meta']['ACS_VOIR_ONGLET_VARS'] != _request('ACS_VOIR_ONGLET_VARS')) ||
			($GLOBALS['meta']['ACS_VOIR_PAGES_COMPOSANTS'] != _request('ACS_VOIR_PAGES_COMPOSANTS')) ||
			($GLOBALS['meta']['ACS_VOIR_PAGES_PREVIEW'] != _request('ACS_VOIR_PAGES_PREVIEW')) ||
			($GLOBALS['meta']['ACS_SPIP_ADMIN_FORM_STYLE'] != _request('ACS_SPIP_ADMIN_FORM_STYLE'))
	) {
		ecrire_meta('ACS_PREVIEW_BACKGROUND', _request('ACS_PREVIEW_BACKGROUND'));
		ecrire_meta('ACS_VOIR_ONGLET_VARS', _request('ACS_VOIR_ONGLET_VARS'));
		ecrire_meta('ACS_VOIR_PAGES_COMPOSANTS', _request('ACS_VOIR_PAGES_COMPOSANTS'));
		ecrire_meta('ACS_VOIR_PAGES_PREVIEW', _request('ACS_VOIR_PAGES_PREVIEW'));
		ecrire_meta('ACS_SPIP_ADMIN_FORM_STYLE', _request('ACS_SPIP_ADMIN_FORM_STYLE'));
		ecrire_meta("acsDerniereModif", time());
		ecrire_metas();
		return array('message_ok' => _T('plugin_info_upgrade_ok'));
	}
}
?>
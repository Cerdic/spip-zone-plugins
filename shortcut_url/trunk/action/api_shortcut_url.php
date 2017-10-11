<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Acces aux shortcut_url
 *
 * URLs de la forme :
 * shortcut_url.api/id_shortcut_url
 *
 * @param null $arg
 */
function action_api_shortcut_url($arg = null) {
	if (is_null($arg)) {
		$arg = _request('arg');
		$shortcut_url = sql_getfetsel('url', 'spip_shortcut_urls', 'id_shortcut_url=' . intval($arg));
		return $shortcut_url;
	}
}

<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// globale filtrer_javascript :
// https://core.spip.net/projects/spip/repository/entry/spip/ecrire/inc/texte.php#L142
// -1 : protection dans l'espace privé et public
// 0  : protection dans l'espace public
// 1  : aucune protection
$GLOBALS['filtrer_javascript'] = 0;

if (defined('_LAZYSIZES_AUTH_DEMO')?
		_LAZYSIZES_AUTH_DEMO
		:
		(isset($GLOBALS['visiteur_session']['statut'])
    AND $GLOBALS['visiteur_session']['statut']=='0minirezo'
    AND $GLOBALS['visiteur_session']['webmestre']=='oui')
	)
	_chemin(_DIR_PLUGIN_LAZYSIZES."demo/");
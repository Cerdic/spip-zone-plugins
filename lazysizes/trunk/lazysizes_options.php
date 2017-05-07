<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


if (defined('_LAZYSIZES_AUTH_DEMO')?
		_LAZYSIZES_AUTH_DEMO
		:
		(isset($GLOBALS['visiteur_session']['statut'])
    AND $GLOBALS['visiteur_session']['statut']=='0minirezo'
    AND $GLOBALS['visiteur_session']['webmestre']=='oui')
	)
	_chemin(_DIR_PLUGIN_LAZYSIZES."demo/");
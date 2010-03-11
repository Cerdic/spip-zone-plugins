<?php

// exec/foni_install.php
	
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/**********************************************
 * Copyright (c) 2010 Christian Paulus - http://www.quesaco.org
 * Dual licensed under the MIT and GPL licenses.
 **********************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/foni_api');

function foni_install ($action) {

	switch($action) {
		case 'test':
		// si renvoie true, c'est que la base est a jour, inutile de re-installer
		// la valise plugin "effacer tout" apparait.
			$result = isset($GLOBALS['meta'][_FONI_META_PREFERENCES]);
			foni_log('TEST foni: ' . ($result ? 'TRUE' : 'FALSE'));
			return($result);
			break;
		case 'install':
			$prefs = foni_lire_preferences(true);
			$result = isset($GLOBALS['meta'][_FONI_META_PREFERENCES]);
			foni_log('INSTALL foni: ' . ($result ? 'OK' : 'ERROR'));
			return($result);
			break;
		case 'uninstall':
			// est appelle lorsque "Effacer tout" dans exec=admin_plugin
			include_spip('base/abstract_sql');
			// $sql_query = "DELETE FROM spip_meta WHERE nom='"._ECCO_META_PREFERENCES."' LIMIT 1"
			$result = sql_delete('spip_meta', 'nom=' . sql_quote(_FONI_META_PREFERENCES) . ' LIMIT 1');
			unset($GLOBALS['meta'][_FONI_META_PREFERENCES]);
			foni_log('UNINSTALL ' . _FONI_META_PREFERENCES . ' : ' . ($result ? 'OK' : 'ERROR'));
			return($result);
	}
}


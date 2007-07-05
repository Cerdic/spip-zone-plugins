<?php

define('_URL_LOADER_PROXY', $GLOBALS['meta']['http_proxy']);
$maj_methode = array(
	'chargeur' => 'https?',
	'svn' => 'svn',
	'spip_loader' => 'https?'
);
$maj_source = array(
	'chargeur',
	'fichier',
	'rss'
);

#anciennes constantes devenues obsoletes
/*define('_SPIP_LOADER_UPDATE_AUTEURS', '1');
define('_SVN_UPDATE_AUTEURS', '1');

#merge des anciennes constantes
$webmestres = array_merge(
	explode(':', _SPIP_LOADER_UPDATE_AUTEURS),
	explode(':', _SVN_UPDATE_AUTEURS)
);
# redefinissables dans config/mes_options ; si on veut en mettre
# plusieurs separer par des deux-points
define('_ID_WEBMESTRES', join(':', $webmestres));
*/
?>
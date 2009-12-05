<?php

/**
 * Copyright (c) 2009 Christian Paulus
 * Dual licensed under the MIT and GPL licenses.
 * */


// exau_options.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

// pour SPIP 1.9.1
if(!defined('_DIR_PLUGIN_EXAU')) {
	$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_EXAU',(_DIR_PLUGINS.end($p)).'/');
}

/**
 * Obsolète. La configuration passe maintenant par une page cfg. (CPA-20091205)
 */
/// Vous pouvez modifier EXAU_EXPORTER_TOUT
//
// si EXAU_EXPORTER_TOUT==FALSE ou absent, le raccourci n'apparait que dans la page "Visiteurs"
// si EXAU_EXPORTER_TOUT==TRUE, le raccourci d'export apparait egalement dans la page "Auteurs"
//define("EXAU_EXPORTER_TOUT", true);
//define("EXAU_EXPORTER_TOUT", false);




// normalement, vous n'avez rien a modifier ci-dessous
define("EXAU_STATUTS_AUTEURS", '0minirezo,1comite,5poubelle');
if(version_compare($GLOBALS['spip_version_code'],'1.9300','<')) 
{
	define("EXAU_STATUTS_INVITES", '6forum');
	define("EXAU_STATUTS_INVITES2", '6forum'); // bug SPIP 2 ? 
}
else 
{
	define("EXAU_STATUTS_INVITES", '!0minirezo,1comite,5poubelle');
	define("EXAU_STATUTS_INVITES2", '!1comite,0minirezo,nouveau'); // bug SPIP 2 ? liens différents dans exec=auteurs pour la meme action ?
}



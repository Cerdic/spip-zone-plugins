<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
# Copyleft: licence GPL - Cf. LICENCES.txt

// Pages de l'espace ecrire à accès contrôlé comme ACS - ACS access rules controlled pages
// Les administrateurs sont ceux d'ACS
$GLOBALS['ACS_ACCES'] = array('mots_type');
// Contrôle d'accès ACS aux pages d'administration de certains plugins, s'ils sont installés
// Deux tests, en raison de changements dans SPIP : à suivre (ok avec spip 2.0.8)
$plugs = isset($GLOBALS['meta']['plugin']) ? unserialize($GLOBALS['meta']['plugin']) : $GLOBALS['plugins'];
if (is_array($plugs)) {
	if(isset($plugs['CFG']))
		$GLOBALS['ACS_ACCES'][] = 'cfg';
	if(isset($plugs['NOTATION']))
		$GLOBALS['ACS_ACCES'][] = 'notation_param';
	if(isset($plugs['W3C_GO_HOME']))
		$GLOBALS['ACS_ACCES'][] = 'w3c_go_home';
	if(isset($plugs['OPENPUBLISHING'])) {
		$GLOBALS['ACS_ACCES'][] = 'op';
		$GLOBALS['ACS_ACCES'][] = 'op_effacer';
	}
}

// Uncomment for debug :
//define('_DEBUG_CRAYONS', true);
define('_ACS_LOG', true);



/*__________________________________________________________________

  Ne PAS modifier ce qui suit - Do NOT modify anything after this
  __________________________________________________________________
*/
// Chargement - Loading
require_once _DIR_PLUGIN_ACS.'inc/acs_onload.php';
?>
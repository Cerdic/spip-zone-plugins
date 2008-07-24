<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

// Override du crayons_html du plugin crayons
require_once(_DIR_PLUGIN_CRAYONS.'action/crayons_html.php');

// On redéfinit cette fonction pour les composants
function action_crayons_html() {
  
	header("Content-Type: text/html; charset=".$GLOBALS['meta']['charset']);

	// CONTROLEUR
	// on affiche le formulaire demande
	include_spip('inc/crayons');
	lang_select($GLOBALS['auteur_session']['lang']);
	$return = affiche_controleur(_request('class'));
	if (strpos(_request('class'), 'composant-') === false)
		$return['$html'] = crayons_formulaire($return['$html']);
	echo var2js($return);
	exit;
}
?>
<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt


/* à remettre si ACS se révèlé aisément portable sous 1.9.1 ?
if (!defined('_DIR_PLUGIN_ACS')){ // defini automatiquement par SPIP 1.9.2
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_ACS',(_DIR_PLUGINS.end($p)."/"));
}
*/

// Permet d'afficher l'interface d'admin d'ACS dans toutes les langues disponibles pour spip.
$GLOBALS['meta']['langues_proposees'] = $GLOBALS['meta']['langues_multilingue'];

function acs_ajouterBouton($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
      && (acs_autorise($GLOBALS['auteur_session']['id_auteur']) || (!isset($GLOBALS['meta']['ACS_ADMINS'])))
     ) {

		// on voit le bouton ACS dans la barre "configuration"
		$boutons_admin['configuration']->sousmenu["acs&detail=2"]= new Bouton(
		_DIR_ACS."img_pack/acs_config-24.gif",  // affichage de l'icone
		_T('acs:configurer_site') // affichage du texte
		);
	}
	return $boutons_admin;
}

function acs_header_prive($flux) {
  $url_css = '../spip.php?page=acs_style_prive.css&couleur_foncee='.substr($GLOBALS['couleur_foncee'],1).'&couleur_claire='.substr($GLOBALS['couleur_claire'],1);
  return $flux.'<link rel="stylesheet" href="'.$url_css.'" type="text/css" media="projection, screen, tv" />'.
'<script type="text/javascript" src="'._DIR_ACS.'javascript/jquery.debug.js"></script>'.
'<script type="text/javascript">var DEBUG = '.
'false'. // true active le debug des éléments jQuery dans ACS
';</script>'.
'<script type="text/javascript" src="'._DIR_ACS.'javascript/dragdrop_interface.js"></script>'.
'<script type="text/javascript" src="'._DIR_ACS.'javascript/acs_ecrire.js"></script>'.
'<script type="text/javascript" src="'._DIR_ACS.'lib/picker/picker.js"></script>';
}

?>

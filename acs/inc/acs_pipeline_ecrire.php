<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
# Copyleft: licence GPL - Cf. LICENCES.txt

// Permet d'afficher l'interface d'admin d'ACS dans toutes les langues disponibles pour spip.
//$GLOBALS['meta']['langues_proposees'] = $GLOBALS['meta']['langues_multilingue'];

function acs_ajouterBouton($boutons_admin) {
	// si on est admin SPIP ET admin ACS
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
      && (acs_autorise() || (!isset($GLOBALS['meta']['ACS_ADMINS'])))
     ) {

		// on voit le bouton ACS dans la barre "configuration"
		$boutons_admin['configuration']->sousmenu["acs"]= new Bouton(
		_DIR_ACS."images/acs_32x32.gif",  // affichage de l'icone
		_T('acs:configurer_site') // affichage du texte
		);
	}
	return $boutons_admin;
}

function acs_header_prive($flux) {
/*
  $url_css = '../spip.php?page=acs_style_prive.css&couleur_foncee='.substr($GLOBALS['couleur_foncee'],1).'&couleur_claire='.substr($GLOBALS['couleur_claire'],1);
  $r = '<link rel="stylesheet" href="'.$url_css.'" type="text/css" media="projection, screen, tv" />';
*/
  $url_css = _ACS_DIR_SITE_ROOT.'spip.php?page=prive/acs_style_prive.css&couleur_foncee='.substr($GLOBALS['couleur_foncee'],1).'&couleur_claire='.substr($GLOBALS['couleur_claire'],1);
	$r = '<link rel="stylesheet" href="'.$url_css.'" />';
	

  $js_dragdrop = find_in_path('javascript/dragdrop_interface.js');
  
	// A partir de spip 2.1, l'interface dragdrop de JQuery a changé de nom:
	if (!$js_dragdrop) {
		$js_dragdrop = find_in_path('javascript/jquery-ui-1.8-drag-drop.min.js');
		$jquery_version = 1;
	}
	else
		$jquery_version = 0;

	$r .= 
'<script type="text/javascript" src="'.$js_dragdrop.'"></script>'.
'<script type="text/javascript" src="../spip.php?page=javascript/acs_ecrire.js&jquery_version='.$jquery_version.'"></script>'.
'<script type="text/javascript" src="'._DIR_ACS.'lib/picker/picker.js"></script>'."\r";	
	return $flux.$r;
}

?>

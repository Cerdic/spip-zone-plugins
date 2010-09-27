<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Implémentation du pipeline insert_head pour le plugin ACS.
 * 
 * insert_head pipeline for ACS plugin.
 */
function acs_insert_head($flux) {
  $js_acs = find_in_path('acs.js.html');
  if ($js_acs)
    $r .= '<script type="text/javascript" src="spip.php?page=acs.js"></script>';
  $js_model = find_in_path($GLOBALS[acsModel].'.js.html');
  if ($js_model)
    $r .= '<script type="text/javascript" src="spip.php?page='.$GLOBALS[acsModel].'.js"></script>';

  // On ajoute des javascripts rien que pour les adminstrateurs ACS
  if (acs_autorise()) {
  	$js_dragdrop = find_in_path('javascript/dragdrop_interface.js');
  	// A partir de spip 2.1, l'interface dragdrop de JQuery a changé de nom:
  	if (!$js_dragdrop)
  		$js_dragdrop = find_in_path('javascript/jquery-ui-1.8-drag-drop.min.js');
  	$r .= '<script type="text/javascript" src="'.$js_dragdrop.'"></script>';
  }
  return $flux.$r;
}
?>
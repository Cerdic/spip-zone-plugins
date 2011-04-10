<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2011
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Implémentation du pipeline insert_head pour le plugin ACS.
 * 
 * insert_head pipeline for ACS plugin.
 */
function acs_insert_head($flux) {
  $model =$GLOBALS['meta']['acsModel'];
  $css_model = find_in_path($model.'.css.html');
  if ($css_model)
    $r .= '<link rel="stylesheet" href="spip.php?page='.$model.'.css" type="text/css" media="projection, screen, tv" />';
  $js_model = find_in_path($model.'.js.html');
  if ($js_model)
    $r .= '<script type="text/javascript" src="spip.php?page='.$model.'.js"></script>';

  // On ajoute une css et des javascripts rien que pour les administrateurs ACS
  if (acs_autorise()) {
  	$r .= '<link rel="stylesheet" href="'.direction_css(generer_url_public('acs_style_prive.css')).'" type="text/css" media="projection, screen, tv" />';
  	$js_dragdrop = find_in_path('javascript/dragdrop_interface.js');
  	$jquery_version = 0;
  	// A partir de spip 2.1, l'interface dragdrop de JQuery a changé de nom:
  	if (!$js_dragdrop) {
  		$js_dragdrop = find_in_path('javascript/jquery-ui-1.8-drag-drop.min.js');
  		$jquery_version = 1;
  	}
  	$js_params = array('jquery_version' => $jquery_version);
  	$r .= '<script type="text/javascript" src="'.$js_dragdrop.'"></script>';
  	$r .= '<script type="text/javascript" src="'.urldecode(generer_url_public('javascript/acs_controleur_composant.js', $js_params)).'"></script>';
  }
  return $flux.$r;
}
?>
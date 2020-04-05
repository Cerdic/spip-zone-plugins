<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Implémentation du pipeline insert_head pour le plugin ACS.
 * Recherche  a la racine du set de composants les fichiers de la forme
 * "set_de_composants.css.html" et "javascript/set_de_compsoants.js.html", et 
 * insere leur lien dans le header de lapage web s'il existe.
 * 
 * insert_head pipeline for ACS plugin.
 */
function acs_insert_head($flux) {
	$r = '';
  // On ajoute au début une css rien que pour les administrateurs ACS
  if (autoriser('acs', 'pinceaux'))
  	$r .= '<link rel="stylesheet" href="'.direction_css(generer_url_public('acs_style_prive.css')).'" type="text/css" media="projection, screen, tv" />';
  // On ajoute la CSS du jeu de composants, si elle existe :
  $set = $GLOBALS['meta']['acsSet'];
  $css_set = find_in_path($set.'.css.html');
  if ($css_set)
    $r .= '<link rel="stylesheet" href="spip.php?page='.$set.'.css&amp;v='.$GLOBALS["meta"]["acsDerniereModif"].'" type="text/css" media="projection, screen, tv" />';
  $js_set = find_in_path($set.'.js.html');
  if ($js_set)
    $r .= '<script type="text/javascript" src="spip.php?page='.$set.'.js&amp;v='.$GLOBALS["meta"]["acsDerniereModif"].'"></script>';

  // On ajoute à la fin les javascripts spécifiques des administrateurs ACS
  if (autoriser('acs', 'pinceaux')) {
  	$r .= '<script type="text/javascript" src="'.urldecode(generer_url_public('javascript/acs_controleur_composant.js', $js_params)).'"></script>';
  }
  acs_log('acs_insert_head()');
  return $flux.$r;
}

?>
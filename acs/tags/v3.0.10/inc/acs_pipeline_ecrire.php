<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

// Permet d'afficher l'interface d'admin d'ACS dans toutes les langues disponibles pour spip.
//$GLOBALS['meta']['langues_proposees'] = $GLOBALS['meta']['langues_multilingue'];

function acs_header_prive($flux) {
	$set =$GLOBALS['meta']['acsSet'];
  $css_set = find_in_path($set.'.css.html');
  $r = '';
  if ($css_set)
    $r .= '<link rel="stylesheet" href="../spip.php?page='.$set.'.css&v='.$GLOBALS["meta"]["acsDerniereModif"].'" type="text/css" media="projection, screen, tv" />';

  // On ajoute le style privé APRES la feuille de style du modèle pour pouvoir 
  // overrider ce qui pourrait perturber l'interface privee, comme par exemple la couleur du <body> 
  $url_css = '../spip.php?page=acs_style_prive.css&couleur_foncee='.substr($GLOBALS['couleur_foncee'],1).'&couleur_claire='.substr($GLOBALS['couleur_claire'],1);
  $r .= '<link rel="stylesheet" href="'.$url_css.'" type="text/css" media="projection, screen, tv" />';

  $js_set = find_in_path($set.'.js.html');
  if ($js_set)
    $r .= '<script type="text/javascript" src="../spip.php?page='.$set.'.js&v='.$GLOBALS["meta"]["acsDerniereModif"].'"></script>';

	$r .= 
'<script type="text/javascript" src="../spip.php?page=javascript/acs_ecrire.js"></script>'.
'<script type="text/javascript" src="'._DIR_ACS.'inc/picker/picker.js"></script>'."\r";
	acs_log('acs_header_prive()');	
	return $flux.$r;
}

?>

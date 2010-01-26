<?php
// ajouter js et css necessaires pour l'affichage public des modeles netvibes
function pubnetvibes_insert_head($flux){
	// Initialisation des valeurs de config
	$config = @unserialize($GLOBALS['meta']['pubnetvibes']);
  
// Insertion des librairies js en fonction de la version de jQuery donc de celle de SPIP...
  // en SPIP 2.1.* jQuery est en version 1.3.+ donc ui en version 1.7.2
  if (strpos('2.1', $GLOBALS['spip_version_affichee'])) {
      $flux .='<link type="text/css" href="'.url_absolue(find_in_path('lib/jquery-ui-themes-1.7.2/themes/base/jquery-ui.css')).'" rel="Stylesheet"/>'."\r\n";
      $flux .='<script src="'.url_absolue(find_in_path('lib/jquery-ui-1.7.2/ui/jquery-ui.js')).'"></script>'."\r\n";
  }
  // en SPIP 2.0.* jQuery est en version 1.2.6 donc ui en version 1.6
  else {
      $flux .='<link type="text/css" href="'.url_absolue(find_in_path('lib/jquery.ui-1.6/themes/default/ui.all.css')).'" rel="Stylesheet"/>'."\r\n";
      $flux .='<script src="'.url_absolue(find_in_path('lib/jquery.ui-1.6/ui/ui.core.js')).'"></script>'."\r\n";
      $flux .='<script src="'.url_absolue(find_in_path('lib/jquery.ui-1.6/ui/ui.tabs.js')).'"></script>'."\r\n";
  }
  
// la feuille de style specifique du plugin pubnetvibes
  $flux .='<link type="text/css" href="'.url_absolue(find_in_path('pubnetvibes.css')).'" rel="Stylesheet"/>'."\r\n";
  
// lancement des tabs jQuery
  $flux .= '<script language="JavaScript" type="text/javascript">/* <![CDATA[ */'."\r\n";
  $flux .= '  jQuery(document).ready(function() {'."\r\n";
  $flux .= '      jQuery(".tabs_netvibes").tabs();'."\r\n";
  $flux .= '  });'."\r\n";
  $flux .= '/* ]]> */</script>'."\r\n";
  
  return $flux;
}
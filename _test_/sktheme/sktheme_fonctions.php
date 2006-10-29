<?php
  // ---------------------------------------------------------------------
  //
  // Sktheme : manage themes under SPIP (squelettes + habillages)
  //
  // Copyright (c) 2006 - Jerome RICHARD
  //
  // This program is free software; you can redistribute it and/or modify
  // it under the terms of the GNU General Public License as published by
  // the Free Software Foundation; either version 2 of the License, or
  // (at your option) any later version.
  //
  // You should have received a copy of the GNU General Public License
  // along with this program; 
  //
  // ---------------------------------------------------------------------

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_SKTHEME',(_DIR_PLUGINS.end($p)));

include_spip('inc/sktheme_list');

// Add private area button
function sktheme_ajouter_boutons($boutons_admin) {
  // administrator only
  if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
    
    // See button in the 'configuration' 
    $boutons_admin['configuration']->sousmenu["sktheme_public_choice"]= 
       new Bouton("../"._DIR_PLUGIN_SKTHEME."/img_pack/sktheme_icon.png",  // icon
		  _L("sktheme:manage_theme"));                                     // title
  }
  return $boutons_admin;
}


//
// Include Philippe Drouot switcher functionnality
// Based on Fil contrib
function sktheme_affichage_final($texte){
	
  global $html;
  
  $sktheme_list = sktheme_list();
   
  // Insertion du Javascript de rechargement de page
  // Always include this script used for #SKTHEME_HABILLAGES_SWITCHER and #SKTHEME_THEMES_SWITCHER
  $code = '<script type="text/javascript">
		//<![CDATA[
		function sktheme_gotof(url) {
		window.location=url;
		}//]]>
		</script>';	  
  
  if (isset($GLOBALS['meta']['sktheme_switcher_activated']) 
      AND ($GLOBALS['meta']['sktheme_switcher_activated']=="yes")) {
    if ($html) {
		
      // Doit-on afficher le selecteur de squelette ? (Fonctionnalite restreinte aux seuls administrateurs ?)
      $afficherSelecteur=TRUE;
      if (isset($GLOBALS['meta']['sktheme_switcher_admin_only']) 
	  AND ($GLOBALS['meta']['sktheme_switcher_admin_only']=="yes") 
	  AND (!isset($_COOKIE['spip_admin']))) $afficherSelecteur=FALSE;
		
      if ($afficherSelecteur) {
			
			
	// Insertion du selecteur de squelettes			
	$code.='<div id="sktheme_switcher" style="top: 0;left: 20px; position: absolute; background-color: transparent;z-index: 100;">';
	$code.='<form action="" method="post">';
	$code.='<select name="selecteurTheme" style="'.$GLOBALS['meta']['sktheme_theme_switcher_style'].'" onchange="sktheme_gotof(this.options[this.selectedIndex].value)">';
	$code.='<option selected="selected" value="">Themes</option>';
	foreach( $sktheme_list as $value )	$code.='<option value="'.parametre_url(self(),'sktheme',$value).'">&nbsp;-> '.$value.'</option>';
	$code.='</select>';
	$code.='</form>';
	$code.='</div>';
      }

			
    }
  } 
	// On rajoute le code du selecteur de squelettes avant la balise </body>  
	$texte=eregi_replace("</body>","$code</body>",$texte);
	return($texte);
}

?>

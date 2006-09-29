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


include_spip('inc/sktheme_list');

// @param p est un objet SPIP
function balise_SKTHEME_THEMES_SWITCHER ($p) {
  $sktheme_list = sktheme_list();
  $code.='<form action="" method="post">';
  $code.='<select name="selecteurTheme" style="'.$GLOBALS['meta']['sktheme_theme_switcher_style'].'" onchange="sktheme_gotof(this.options[this.selectedIndex].value)">';
  $code.='<option selected="selected" value="">'._T('themes').'</option>';
  foreach( $sktheme_list as $value )	$code.='<option value="'.parametre_url(self(),'sktheme',$value).'">&nbsp;-> '.$value.'</option>';
  $code.='</select>';
  $code.='</form>';  
  
  $p->code = "'$code'";
  return ($p);
}

// @param p est un objet SPIP
function balise_SKTHEME_HABILLAGES_SWITCHER ($p) {
  $sktheme_habillage_list = sktheme_habillage_list();
  $code.='<form action="" method="post">';
  $code.='<select name="selecteurHabillage" style="'.$GLOBALS['meta']['sktheme_habillage_switcher_style'].'" onchange="sktheme_gotof(this.options[this.selectedIndex].value)">';
  $code.='<option selected="selected" value="">'._T('habillages').'</option>';
  foreach( $sktheme_habillage_list as $value )	{
    $code.='<option value="'.parametre_url(self(),'sktheme',"__current::".$value).'">&nbsp;-> '.$value.'</option>';
  }
  $code.='</select>';
  $code.='</form>';  
  $p->code = "'$code'";
  return ($p);
}

?>
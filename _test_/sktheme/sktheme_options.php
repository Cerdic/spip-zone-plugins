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

include_spip("inc/meta");

// Set a default configuration - each values can be modify 
// in the private area
if (!isset($GLOBALS['meta']['sktheme_squelettes_public_dir'])){
  ecrire_meta('sktheme_squelettes_public_dir',"themes/squelettes");
  ecrire_meta('sktheme_habillages_public_dir',"themes/habillages");
  ecrire_meta('sktheme_squelette_public_name',"dist");
  ecrire_meta('sktheme_habillage_public_name',"");
  ecrire_metas();
} 

// So set only the 'dossier_squelettes' to the corresponding user choice
$dossier_squelettes =  $GLOBALS['meta']['sktheme_habillages_public_dir'].'/'.$GLOBALS['meta']['sktheme_habillage_public_name'].
':'.$GLOBALS['meta']['sktheme_squelettes_public_dir'].'/'.$GLOBALS['meta']['sktheme_squelette_public_name'];

?>

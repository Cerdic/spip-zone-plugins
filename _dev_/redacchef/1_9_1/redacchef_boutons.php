<?php

include("exec/redacchef_inc.php");

function redacchef_ajouterBoutons($boutons_admin) {
  global $toto;
  // si on est admin
  if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {

    $boutons_admin['auteurs']->sousmenu['redacchef']= 
      new Bouton( _DIR_PLUGIN_REDACCHEF."/IMG/auteur-24-orange.gif", _T('redacchef:gestionredacchef') );
  }

  return $boutons_admin;
}


?>

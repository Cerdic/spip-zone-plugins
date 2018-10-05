<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function skeleditor_autoriser(){}

// autorisation des boutons
function autoriser_skeleditor_ajout_bouton_dist($faire, $type, $id, $qui, $opt) {
  global $connect_toutes_rubriques;

  if ($GLOBALS['connect_statut'] == "0minirezo" && $connect_toutes_rubriques) return true;
  return false; 
}




?>
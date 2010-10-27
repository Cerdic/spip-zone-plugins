<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_objet_dist() {
  $editer_objets=charger_fonction('editer_objets','action');
  return $editer_objets();
}
?>
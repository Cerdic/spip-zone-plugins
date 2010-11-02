<?php
  if (!defined("_ECRIRE_INC_VERSION")) return;

  function action_explication_dist(){
    include_spip('inc/autoriser');
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $arg = $securiser_action();
    if (!preg_match(",^(\w+)-(\d+)$,", $arg, $r)) {
        spip_log("action_supprimer_explication_dist $arg pas compris");
        return;
    }
    $op = $r[1];
    if($op!="eff")
      return;
    $id_explication = intval($r[2]);

    if (!autoriser('webmestre'))
        return;

    sql_delete("spip_explications", "id_explication=". $id_explication);
  }

<?php

if (!defined('_ECRIRE_INC_VERSION')) {
  return;
}

function action_supprimer_stock_dist($id_stock=null){

  if (is_null($id_stock)){
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $id_stock = $securiser_action();
  }
  $table_stocks = table_objet_sql('stocks');

  $action = sql_delete($table_stocks,array('id_stock ='.intval($id_stock)));
  spip_log('Supression du stock : '.$id_stock,'stocks');
}

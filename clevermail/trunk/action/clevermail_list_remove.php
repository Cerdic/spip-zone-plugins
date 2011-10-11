<?php
function action_clevermail_list_remove_dist($lst_id = 0) {
	$securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();
  $lst_id = intval($arg);
  $lst_name = sql_getfetsel("lst_name", "spip_cm_lists", "lst_id=".intval($lst_id));

  include_spip('inc/autoriser');
  if (autoriser('supprimer','cm_list',intval($lst_id))) {
		if ((sql_countsel("spip_cm_lists_subscribers", "lst_id=".intval($lst_id)) == 0)
		    && (sql_countsel("spip_cm_posts", "lst_id=".intval($lst_id)) == 0)) {
  		sql_delete('spip_cm_lists', 'lst_id='.intval($lst_id));
      spip_log('Suppression de la liste « '.$lst_name.' » (id = '.intval($lst_id).')', 'clevermail');
    }
  }
}
?>

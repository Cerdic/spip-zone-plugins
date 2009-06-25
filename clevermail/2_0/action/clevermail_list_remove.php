<?php
function action_clevermail_list_remove_dist($lst_id = 0) {
	$securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();
  $lst_id = intval($arg);

  include_spip('inc/autoriser');
  if (autoriser('supprimer','cm_list',$lst_id)) {
		if ((sql_countsel("spip_cm_lists_subscribers", "lst_id=".$lst_id) == 0)
		    && (sql_countsel("spip_cm_posts", "lst_id=".$lst_id) == 0)) {
  		sql_delete('spip_cm_lists', 'lst_id='.$lst_id);
      spip_log('Suppression de la liste « '.sql_getfetsel("lst_name", "spip_cm_lists", "lst_id=".$lst_id).' » (id = '.$lst_id.')', 'clevermail');
    }
  }
}
?>

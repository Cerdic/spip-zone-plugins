<?php
function action_clevermail_list_subscriber_toggle_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();
  $lsr_id = $arg;
  $mode = (sql_getfetsel("lsr_mode", "spip_cm_lists_subscribers", "lsr_id=".sql_quote($lsr_id)) + 1) % 2;
  
  if (sql_countsel("spip_cm_lists_subscribers", "lsr_id=".sql_quote($lsr_id)) == 1) {
	  include_spip('inc/autoriser');
	  if (autoriser('toggle', 'cm_list_subscriber', sql_quote($lsr_id))) {
	    sql_updateq("spip_cm_lists_subscribers", array('lsr_mode' => $mode), "lsr_id=".sql_quote($lsr_id));
	    $abonnement = sql_fetsel("l.lst_id, l.lst_name, s.sub_email", "spip_cm_lists_subscribers ls, spip_cm_lists l, spip_cm_subscribers s", "ls.sub_id=s.sub_id AND ls.lst_id=l.lst_id AND ls.lsr_id=".sql_quote($lsr_id));
	    spip_log('Changement de mode de l\'abonnement de « '.$abonnement['sub_email'].' » à la liste « '.$abonnement['lst_name'].' » (id='.$abonnement['lst_id'].')', 'clevermail');
	  }
  }
}
?>

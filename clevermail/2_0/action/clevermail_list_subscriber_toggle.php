<?php
function action_clevermail_list_subscriber_toggle_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();
  $lsr_id = $arg;
  $mode = 1 - sql_getfetsel("lsr_mode", "spip_cm_lists_subscribers", "lsr_id=".$lsr_id);
  
  if (sql_countsel("spip_cm_lists_subscribers", "lsr_id=".$lsr_id) == 1) {
	  include_spip('inc/autoriser');
	  if (autoriser('toggle','cm_list_subscriber',$pst_id)) {
	    sql_updateq("spip_cm_lists_subscribers", array('lsr_mode' => $mode), "lsr_id=".$lsr_id);
	    spip_log('Changement de mode de l\'abonnement de « '.$abonne.' » à la liste « '.$liste.' » (id='.$abonnement['lst_id'].')', 'clevermail');
	  }
  }
}
?>

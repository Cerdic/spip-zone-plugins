<?php
function action_clevermail_list_subscriber_remove_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();
  $lsr_id = intval($arg);
  
  if (sql_countsel("*", "spip_cm_lists_subscribers", "lsr_id=".$lsr_id)) == 1 {
	  include_spip('inc/autoriser');
	  if (autoriser('supprimer','cm_list_subscriber',$pst_id)) {
	    $abonnement = sql_fetsel("sub_id, lst_id", "spip_cm_lists_subscribers", "lsr_id=".$lsr_id);
	  	$abonne = sql_getfetsel("sub_email", "spip_cm_subscribers", "sub_id=".$abonnement['sub_id']);
	  	$liste = sql_getfetsel("lst_name", "spip_cm_lists", "lst_id=".$abonnement['lst_id']);
	    sql_delete("spip_cm_lists_subscribers", "lsr_id = ".$lsr_id);
	    spip_log('Suppression du l\'abonnement de « '.$abonne.' » à la liste « '.$liste.' » (id='.$abonnement['lst_id'].')', 'clevermail');
	  }
  }
}
?>

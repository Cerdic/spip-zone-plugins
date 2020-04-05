<?php
function action_clevermail_list_subscriber_clear_dist() {
  $securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();

  if (sql_countsel("spip_cm_lists_subscribers AS list, spip_cm_subscribers AS sub", "list.sub_id = sub.sub_id AND sub.sub_email LIKE '%@example.com'")) {
  	$subscribers = sql_select("sub.sub_id", "spip_cm_lists_subscribers AS list, spip_cm_subscribers AS sub", "list.sub_id = sub.sub_id AND sub.sub_email LIKE '%@example.com'", "list.sub_id", "", "");
		$sub_deleted = "";
		while ($subscriber = sql_fetch($subscribers)) {
			sql_delete("spip_cm_lists_subscribers", "sub_id = ".intval($subscriber['sub_id']));
			sql_delete("spip_cm_pending", "sub_id = ".intval($subscriber['sub_id']));
			$sub_deleted = $sub_deleted.' '.intval($subscriber['sub_id']);
		}
		spip_log('Suppression des abonnements '.$sub_deleted, 'clevermail');
  } else {
  	spip_log('Il n\'y a pas d\'abonnement à supprimer','clevermail');
  }
  if (sql_countsel("spip_cm_lists_subscribers","","lsr_id","count(lsr_id) > 1") > 1) {
  	$doublons = sql_select("lsr_id","spip_cm_lists_subscribers","","lsr_id","","","count(lsr_id) > 1");
  	while ($doublon = sql_fetch($doublons)) {
  		$lists = sql_select("lst_id, sub_id","spip_cm_lists_subscribers","lsr_id ='".$doublon['lsr_id']."'");
			while ($list = sql_fetch($lists)) {
				sql_updateq("spip_cm_lists_subscribers", array('lsr_id' => md5('subscribe#'.intval($list['lst_id']).'#'.intval($list['sub_id']).'#'.time())), "lst_id=".sql_quote($list['lst_id'])." AND sub_id=".sql_quote($list['sub_id']));
			}
		}
  }
	//Récupération du timestamp du mois dernier
	$today = time();
	$date_today = date("d:m:Y" , $today);
	$today_exploded = explode(":" , $date_today);
	$mois = (int) $today_exploded[1];
	$mois = --$mois;
	$valid_date = mktime(0, 0, 0, $mois,$today_exploded[0], $today_exploded[2]);
	sql_delete("spip_cm_pending", "pnd_action_date <".$valid_date);
	spip_log('Suppression des abonnements en attente depuis plus d\'un mois', 'clevermail');
	
}
?>

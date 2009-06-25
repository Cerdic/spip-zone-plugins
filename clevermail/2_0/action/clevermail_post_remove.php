<?php
function action_clevermail_post_remove_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();
  $pst_id = intval($arg);

  include_spip('inc/autoriser');
  if (autoriser('supprimer','cm_post',$pst_id)) {
    $post = sql_fetsel("lst_id, pst_subject", "spip_cm_posts", "pst_id = ".$pst_id);
  	$list = sql_fetsel("*", "spip_cm_lists", "lst_id = ".$post['lst_id']);
    sql_delete("spip_cm_posts", "pst_id = ".$pst_id);
    spip_log('Suppression du message « '.$post['pst_subject'].' » de la liste « '.$list['lst_name'].' » (id = '.$pst_id.')', 'clevermail');
  }
}
?>

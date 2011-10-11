<?php
function action_clevermail_post_remove_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();
  $pst_id = intval($arg);

  include_spip('inc/autoriser');
  if (autoriser('supprimer','cm_post',$pst_id)) {
  	$nbQueued = sql_countsel("spip_cm_posts_queued", "pst_id = ".intval($pst_id));
  	if ($nbQueued == 0) {
	    $post = sql_fetsel("lst_id, pst_subject", "spip_cm_posts", "pst_id = ".intval($pst_id));
	    $lst_id = $post['lst_id'];
	  	$list = sql_fetsel("*", "spip_cm_lists", "lst_id = ".intval($lst_id));
	    sql_delete("spip_cm_posts", "pst_id = ".intval($pst_id));
	    spip_log('Suppression du message « '.$post['pst_subject'].' » (id='.$pst_id.') de la liste « '.$list['lst_name'].' » (id='.$lst_id.')', 'clevermail');
      $nb = sql_countsel("spip_cm_posts", "lst_id = ".intval($lst_id));
      if ($nb == 0) {
        include_spip('inc/headers');
        redirige_par_entete(generer_url_ecrire('clevermail_lists').'#lst'.$lst_id);
      }
  	}
  }
}
?>

<?php
function clevermail_post_queue($pst_id) {
  if ($post = sql_fetsel("lst_id, pst_subject", "spip_cm_posts", "pst_id = ".intval($pst_id))) {
	  $lst_id = $post['lst_id'];
	  $lst_name = sql_getfetsel("lst_name", "spip_cm_lists", "lst_id = ".intval($lst_id));
	  
	  if (sql_countsel("spip_cm_posts", "pst_id = ".intval($pst_id)." AND pst_date_sent = 0")) {
	    // Si le script plante en cours, on ne renvoie pas deux fois aux premiers, tant pis pour ceux qui ne sont pas passes
	    sql_update("spip_cm_posts", array('pst_date_sent' => time()), "pst_id = ".intval($pst_id));
	    $subscribers = sql_select("sub_id", "spip_cm_lists_subscribers", "lst_id = ".intval($lst_id));
	    while ($sub = sql_fetch($subscribers)) {
	      sql_insertq("spip_cm_posts_queued", array('pst_id' => intval($pst_id), 'sub_id' => intval($sub['sub_id']), 'psq_date' => time()));
	    }
	    spip_log('Déclenchement de l\'envoi du message « '.$post['pst_subject'].' » (id='.$pst_id.') de la liste « '.$lst_name.' » (id='.$lst_id.')', 'clevermail');
	  }
  }
}
?>

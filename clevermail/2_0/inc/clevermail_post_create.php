<?php
function clevermail_post_create($lst_id) {
  if ($list = sql_fetsel("*", "spip_cm_lists", "lst_id = ".intval($lst_id))) {
	  $post = array('lst_id' => intval($lst_id), 'pst_date_create' => time());
	  include_spip('inc/distant');
	  $post['pst_html'] = recuperer_page($list['lst_url_html']);
	  $post['pst_text'] = recuperer_page($list['lst_url_text']);
	  if (eregi("<title>(.*)</title>", $post['pst_html'], $regs)) {
	    $post['pst_subject'] = trim($regs[1]);
	  } else {
	    $post['pst_subject'] = 'Aucun sujet';
	  }
	  $pst_id = sql_insertq("spip_cm_posts", $post);
	  spip_log('Création du message « '.$post['pst_subject'].' » (id='.$pst_id.') dans la liste « '.$list['lst_name'].' » (id='.$lst_id.')', 'clevermail');
	  return $pst_id;
  }
}
?>

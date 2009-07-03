<?php
function action_clevermail_post_create_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();
  $lst_id = intval($arg);

  include_spip('inc/autoriser');
  if (autoriser('creer','cm_post',$lst_id)) {
  	$list = sql_fetsel("*", "spip_cm_lists", "lst_id = ".intval($lst_id));
  	$post = array('lst_id' => intval($lst_id), 'pst_date_create' => time());
  	include_spip('inc/distant');
  	$post['pst_html'] = recuperer_page($list['lst_url_html']);
    $post['pst_text'] = recuperer_page($list['lst_url_text']);
    if (eregi("<title>(.*)</title>", $post['pst_html'], $regs)) {
      $post['pst_subject'] = trim($regs[1]);
    } else {
      $post['pst_subject'] = 'Aucun sujet';
    }
    sql_insertq("spip_cm_posts", $post);
    spip_log('Création du message « '.$post['pst_subject'].' » dans la liste « '.$list['lst_name'].' » (id = '.$lst_id.')', 'clevermail');
  }
}
?>

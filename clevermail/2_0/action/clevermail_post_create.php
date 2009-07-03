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
  	$html = recuperer_page($list['lst_url_html']);
  	if (eregi("<title>(.*)</title>", $html, $regs)) {
      $post['pst_subject'] = trim($regs[1]);
  	} else {
  		$post['pst_subject'] = 'Aucun sujet';
  	}
  	$post['pst_html'] = $html;
    $post['pst_text'] = recuperer_page($list['lst_url_text']);
    sql_insertq("spip_cm_posts", $post);
    spip_log('Création du message « '.$post['pst_subject'].' » dans la liste « '.sql_getfetsel("lst_name", "spip_cm_lists", "lst_id=".$lst_id).' » (id = '.$lst_id.')', 'clevermail');
  }
}
?>

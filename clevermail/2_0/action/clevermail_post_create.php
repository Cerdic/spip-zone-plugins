<?php
function action_clevermail_post_create_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();
  $lst_id = intval($arg);

  include_spip('inc/autoriser');
  if (autoriser('creer','cm_post',$lst_id)) {
  	$list = sql_fetsel("*", "spip_cm_lists", "lst_id = ".$lst_id);
  	$post = array('lst_id' => $lst_id, 'pst_date_create' => time());
  	include_spip('inc/distant');
  	$html = recuperer_page($list['lst_url_html']);
    $post['pst_subject'] = trim(eregi_replace("^.*<title>(.*)</title>.*$", "\\1", $html));
  	$post['pst_html'] = addslashes($html);
    $post['pst_text'] = addslashes(wordwrap(recuperer_page($list['lst_url_text']), 70));
    sql_insertq("spip_cm_posts", $post);
    spip_log('Création du message « '.$post['pst_subject'].' » dans la liste « '.sql_getfetsel("lst_name", "spip_cm_lists", "lst_id=".$lst_id).' » (id = '.$lst_id.')', 'clevermail');
  }
}
?>

<?php
function clevermail_post_create($lst_id) {
  if ($list = sql_fetsel("*", "spip_cm_lists", "lst_id = ".intval($lst_id))) {
    if (!$last_create = sql_getfetsel("pst_date_create", "spip_cm_posts", "lst_id=".intval($list['lst_id']), "", "pst_date_create DESC", "0,1")) {
      // Il n'y a pas encore eu de message dans cette liste
      $last_create = 60*60*24; // On se place le 2 janvier 1970, SPIP n'aime pas epoc avec le critere "age"
    }
  	$post = array('lst_id' => intval($lst_id), 'pst_date_create' => time());
	  include_spip('inc/distant');
	  $url_html =  $list['lst_url_html'].(strpos($list['lst_url_html'], '?') !== false ? '&' : '?').'date='.date("Y-m-d",$last_create).'&lst_id='.intval($lst_id);
	  $post['pst_html'] = recuperer_page($url_html);
	  $url_text = $list['lst_url_text'].(strpos($list['lst_url_html'], '?') !== false ? '&' : '?').'date='.date("Y-m-d",$last_create).'&lst_id='.intval($lst_id);
	  $post['pst_text'] = recuperer_page($url_text);
	  if (trim($post['pst_html']) != '' && trim($post['pst_text']) != '') {
		  //if (eregi("<title>(.*)</title>", $post['pst_html'], $regs)) {
		  if (preg_match(",<title>(.*)</title>,", $post['pst_html'], $regs)) {
		    $post['pst_subject'] = trim($regs[1]);
		  } else {
		    $post['pst_subject'] = 'Aucun sujet';
		  }
		  $pst_id = sql_insertq("spip_cm_posts", $post);
		  spip_log('Création du message « '.$post['pst_subject'].' » (id='.$pst_id.') dans la liste « '.$list['lst_name'].' » (id='.$lst_id.')', 'clevermail');
		  return $pst_id;
	  } else {
      spip_log('Création d\'un message dans la liste « '.$list['lst_name'].' » (id='.$lst_id.') impossible, contenu vide à '.$url_html.' et '.$url_text, 'clevermail');
	  	return false;
	  }
  }
}
?>
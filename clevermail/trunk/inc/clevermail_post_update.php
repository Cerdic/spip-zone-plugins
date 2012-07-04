<?php
function clevermail_post_update($pst_id) {
  	if ($pst_id == intval($pst_id)) {
   $lst_id = sql_getfetsel('lst_id', 'spip_cm_posts', 'pst_id='.intval($pst_id)); 
   
   $post = array('lst_id' => intval($lst_id), 'pst_date_create' => time());
   
   $list = sql_fetsel('*', 'spip_cm_lists', 'lst_id = '.intval($lst_id));
  	
  	// Traitement de la source HTML
	  if ( strpos($list['lst_url_html'], 'http://') !== false ) {
		  include_spip('inc/distant');
		  $url_html =  $list['lst_url_html'].(strpos($list['lst_url_html'], '?') !== false ? '&' : '?').'date='.date("Y-m-d",$last_create).'&lst_id='.intval($lst_id);
		  $post['pst_html'] = recuperer_page($url_html);
	  } else {
		  $contexte = array(
				'date' => date("Y-m-d",$last_create),
				'lst_id' => intval($lst_id),
			);
		  // on passe la globale lien_implicite_cible_public en true
		  // pour avoir les liens internes en public (en non prive d'apres le contexte)
		  // credit de l'astuce: denisb & rastapopoulos & erational
		  $GLOBALS['lien_implicite_cible_public'] = true;
		  $post['pst_html'] = recuperer_fond($list['lst_url_html'], $contexte);
		  // on revient a la config initiale
		  unset($GLOBALS['lien_implicite_cible_public']);
	  }
	  
	  // Traitement de la source texte
	  if ($list['lst_url_text'] != '') {
  	  if ( strpos($list['lst_url_text'], 'http://') !== false ) {
  		  include_spip('inc/distant');
  		  $url_text = $list['lst_url_text'].(strpos($list['lst_url_text'], '?') !== false ? '&' : '?').'date='.date("Y-m-d",$last_create).'&lst_id='.intval($lst_id);
  		  $post['pst_text'] = recuperer_page($url_text);
  	  } else {
  		  $contexte = array(
  				'date' => date("Y-m-d",$last_create),
  				'lst_id' => intval($lst_id),
  			);
  		  // on passe la globale lien_implicite_cible_public en true
		  // pour avoir les liens internes en public (en non prive d'apres le contexte)
		  // credit de l'astuce: denisb & rastapopoulos & erational
		  $GLOBALS['lien_implicite_cible_public'] = true;
  		  $post['pst_text'] = recuperer_fond($list['lst_url_text'], $contexte);
		  // on revient a la config initiale
		  unset($GLOBALS['lien_implicite_cible_public']);
  	  }
      $post['pst_text'] = strip_tags($post['pst_text']);
  	} else {
  	  // TODO : essayer d'utiliser TEN : http://www.headstar.com/ten/
  	  include_spip('classes/facteur');
  	  $post['pst_text'] = Facteur::html2text($post['pst_html']);
  	}
	  if (trim($post['pst_html']) != '' && trim($post['pst_text']) != '') {
		  /*
if (preg_match(",<title>(.*)</title>,", $post['pst_html'], $regs)) {
		    $post['pst_subject'] = trim($regs[1]);
		  } else {
		    $post['pst_subject'] = 'Aucun sujet';
		  }
*/
		  $pst_id = sql_updateq('spip_cm_posts', $post,'pst_id='.$pst_id);
		  spip_log('Mise a jour du message « '.$post['pst_subject'].' » (id='.$pst_id.') dans la liste « '.$list['lst_name'].' » (id='.$lst_id.')', 'clevermail');
		  return $pst_id;
	  } else {
      spip_log('Mise a jour d\'un message dans la liste « '.$list['lst_name'].' » (id='.$lst_id.') impossible, contenu vide à '.$url_html.' et '.$url_text, 'clevermail');
	  	return false;
	  }
  }
}
?>
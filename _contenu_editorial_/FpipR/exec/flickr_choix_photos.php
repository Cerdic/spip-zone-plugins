<?php

function exec_flickr_choix_photos() {
  global $connect_id_auteur;

  include_spip('inc/flickr_api');
  include_spip('base/abstract_sql');
  
  $from = array('spip_auteurs');
  $select = array('flickr_token','flickr_nsid');
  $where = array('id_auteur='.$connect_id_auteur);
  $rez = spip_abstract_select($select,$from,$where);
  $row = spip_abstract_fetch($rez);
  if($row['flickr_nsid'] != '' && $row['flickr_token'] != '') {
	$page = _request('page')?_request('page'):1;
	$photos = flickr_photos_search(40,$page,$row['flickr_nsid'],'','','','','','','','','','','',$row['flickr_token']);

	$html = "<ul>\n";
	foreach($photos->photos as $photo) {
	  $html .= '<li style="display:inline;"><a href="'.$photo->url().'">
<img src="'.$photo->source('s').'"></a>
<input type="checkbox" name="photos[]" value="'.$photo->source('o').'@#@'.$photo->title.'"/>
</li>'."\n";
	}
	$html .= "</ul>\n";
	$html .= '<button type="submit">'._T('spip:valider')."</button>\n";
	$html .= '<input type="hidden" name="type" value="'.((_request('type') == 'articles')?'article':'rubrique').'"/>'."\n";
	$html .= '<input type="hidden" name="id" value="'.((_request('type') == 'articles')?_request('id_article'):_request('id_rubrique')).'"/>'."\n";

	include_spip('inc/actions');
	if(_request('type') == 'articles') {
	  echo generer_action_auteur('flickr_ajouter_documents',_request('id_article'), generer_url_ecrire('articles','id_article='._request('id_article')),$html);
	} else {
	  echo generer_action_auteur('flickr_ajouter_documents',_request('id_rubrique'), generer_url_ecrire('naviguer','id_rubrique='._request('id_rubrique')),$html);
	}
	echo '<hr/>';
	  

	for($i=1;$i <= $photos->pages;$i++) {
	  if($i != $page) {
	  	echo '<a href="'.generer_url_ecrire('flickr_choix_photos',"page=$i&type="._request('type').'&'.((_request('type') == 'articles')?'id_article='._request('id_article'):'id_rubrique='._request('id_rubrique'))).'">';
	  }
	  echo $i.'|';
	  if($i != $page) {
	  	echo '</a>';
	  }
	  
	}
  } else {
	echo 'vous devez d\'abord vous authetifier <a href="'.generer_url_ecrire('auteurs_edit','id_auteur='.$connect_id_auteur).'">l&agrave;</a>';
  }
}

?>

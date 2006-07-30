<?php

function exec_flickr_choix_photos() {
  global $connect_id_auteur;

  include_spip('inc/flickr_api');
  include_spip('base/abstract_sql');
  
  
  echo '<h1>'._T('fpipr:ajouter_photos').'</h1>';
  echo _T('fpipr:info_photos');

  $from = array('spip_auteurs');
  $select = array('flickr_token','flickr_nsid');
  $where = array('id_auteur='.$connect_id_auteur);
  $rez = spip_abstract_select($select,$from,$where);
  $row = spip_abstract_fetch($rez);
  if($row['flickr_nsid'] != '' && $row['flickr_token'] != '') {
	$page = _request('page')?_request('page'):1;
	//TODO: ajouter des options de recherches
	$photos = flickr_photos_search(40,$page,$row['flickr_nsid'],'','','','','','','','','','','',$row['flickr_token']);

	$html = "<ul>\n";
	foreach($photos->photos as $photo) {
	  $html .= '<li style="display:inline;">
<label for="photo_'.$photo->id.'"><a href="'.$photo->url().'">
<img src="'.$photo->source('s').'" alt="'.$photo->title.'">'.
$photo->title.
'</a></label>
<input type="checkbox" name="photos[]" id="photo_'.$photo->id.'" value="'.$photo->source('o').'@#@'.$photo->title.'"/>
</li>'."\n";
	}
	$html .= "</ul>\n";
	$html .= '<button type="submit">'._T('spip:photo_valider')."</button>\n";
	$html .= '<input type="hidden" name="type" value="'._request('type').'"/>'."\n";
	$html .= '<input type="hidden" name="id" value="'._request('id').'"/>'."\n";

	include_spip('inc/actions');
	if(_request('type') == 'article') {
	  echo generer_action_auteur('flickr_ajouter_documents',_request('id'), generer_url_ecrire('articles','id_article='._request('id')),$html);
	} else if(_request('type') == 'rubrique') {
	  echo generer_action_auteur('flickr_ajouter_documents',_request('id'), generer_url_ecrire('naviguer','id_rubrique='._request('id')),$html);
	} else {
	  echo generer_action_auteur('flickr_ajouter_documents',_request('id'), generer_url_ecrire('breves_edit','id_breve='._request('id')),$html);
	}
	echo '<hr/><h3>'._T('fpipr:pages').':</h3>';
	  

	for($i=1;$i <= $photos->pages;$i++) {
	  if($i != $page) {
	  	echo '<a href="'.generer_url_ecrire('flickr_choix_photos',"page=$i&type="._request('type')."&id="._request('id')).'">';
	  }
	  echo $i.'|';
	  if($i != $page) {
	  	echo '</a>';
	  }
	  
	}
  } else {
	echo _T('fpipr:demande_authentification',array('url'=>generer_url_ecrire('auteurs_edit','id_auteur='.$connect_id_auteur)));
  }
  echo '<hr/>';
  if(_request('type') == 'article') {
	echo '<a href="'.generer_url_ecrire('articles','id_article='._request('id')).'">'._T('fpipr:retour').'</a>';
  } else if(_request('type') == 'rubrique') {
	echo '<a href="'.generer_url_ecrire('naviguer','id_rubrique='._request('id')).'">'._T('fpipr:retour').'</a>';
  } else {
	echo '<a href="'.generer_url_ecrire('breves_edit','id_breve='._request('id')).'">'._T('fpipr:retour').'</a>';
  }

}

?>

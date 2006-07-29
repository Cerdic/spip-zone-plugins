<?php

function exec_flickr_choix_sets() {
  global $connect_id_auteur;

  include_spip('inc/flickr_api');
  include_spip('base/abstract_sql');
  
  echo '<h1>Ajouter des sets de photos</h1>';
  echo 'Veillez choisir les sets que vous voulez ajouter.';

  $from = array('spip_auteurs');
  $select = array('flickr_token','flickr_nsid');
  $where = array('id_auteur='.$connect_id_auteur);
  $row = spip_abstract_fetsel($select,$from,$where);
  if($row['flickr_nsid'] != '' && $row['flickr_token'] != '') {
	$page = _request('page')?_request('page'):1;
	$photosets = flickr_photosets_getList($row['flickr_nsid'],$row['flickr_token']);
	
	$html = "<ul>\n";
	foreach($photosets as $set) {

	  $html .= '<li style="display:inline;">
<label for="set'.$set->id.'">
<a href="'.$set->url().'"><img alt="'.$set->title.'" src="'.$set->logo('s').'">'.
$set->title.
'</a>
</label>
<input type="checkbox" id="set'.$set->id.'" name="sets[]" value="'.$set->id.'"/>
</li>'."\n";
	}
	$html .= "</ul>\n";
	$html .= '<button type="submit">'._T('spip:valider')."</button>\n";
	$html .= '<input type="hidden" name="type" value="'._request('type').'"/>'."\n";
	$html .= '<input type="hidden" name="id" value="'._request('id').'"/>'."\n";
	$html .= '<input type="hidden" name="set" value="oui"/>'."\n";


	include_spip('inc/actions');
	if(_request('type') == 'article') {
	  echo generer_action_auteur('flickr_ajouter_documents',_request('id'), generer_url_ecrire('articles','id_article='._request('id')),$html);
	} else if(_request('type') == 'rubrique') {
	  echo generer_action_auteur('flickr_ajouter_documents',_request('id'), generer_url_ecrire('naviguer','id_rubrique='._request('id')),$html);
	} else {
	  echo generer_action_auteur('flickr_ajouter_documents',_request('id'), generer_url_ecrire('breves_edit','id_breve='._request('id')),$html);
	}
  } else {
	echo 'vous devez d\'abord vous authentifier <a href="'.generer_url_ecrire('auteurs_edit','id_auteur='.$connect_id_auteur).'">l&agrave;</a>';
  }
  if(_request('type') == 'article') {
	echo '<a href="'.generer_url_ecrire('articles','id_article='._request('id')).'">retour</a>';
  } else if(_request('type') == 'rubrique') {
	  echo '<a href="'.generer_url_ecrire('naviguer','id_rubrique='._request('id')).'">retour</a>';
  } else {
	  echo '<a href="'.generer_url_ecrire('breves_edit','id_breve='._request('id')).'">retour</a>';
  }
}

?>

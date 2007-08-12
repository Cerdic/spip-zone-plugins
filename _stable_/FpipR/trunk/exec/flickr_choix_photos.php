<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__).'/../')));
define('_DIR_PLUGIN_FPIPR',(_DIR_PLUGINS.end($p)));

function exec_flickr_choix_photos() {
  global $connect_id_auteur;

  include_spip('inc/flickr_api');
  include_spip('base/abstract_sql');

  echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <title>'._T('fpipr:ajouter_photos').'</title>
	<link href="'._DIR_PLUGIN_FPIPR.'/styles.css" rel="stylesheet" type="text/css" />
  </head>

  <body>';
  
  echo '<h1>'._T('fpipr:ajouter_photos').'</h1>';

  $from = array('spip_auteurs');
  $select = array('flickr_token','flickr_nsid');
  $where = array('id_auteur='.$connect_id_auteur);
  $rez = sql_select($select,$from,$where);
  $row = sql_fetch($rez);
  if($row['flickr_nsid'] != '' && $row['flickr_token'] != '') {
	$check = flickr_auth_checkToken($row['flickr_token']);
	if($check) {
	  echo _T('fpipr:info_photos');
	  $page = intval(_request('page'))?intval(_request('page')):1;
	  $sort = _request('sort')?_request('sort'):'date-posted-desc';
	  
	  $photos = flickr_photos_search(40,$page,$row['flickr_nsid'],'','',_request('text_search'),'','','','','',$sort,'','','','','','','','',$row['flickr_token']);
		  
	  
	  $html = '<input type="hidden" name="flickr_nsid" value="'.$row['flickr_nsid'].'">';
	  $html .= '<input type="hidden" name="flickr_token" value="'.$row['flickr_token'].'">';
	  $html .= "<ul>\n";
	  foreach($photos->photos as $photo) {
		$html .= '<li>';
		if($photo->title) {
		  $html .= '<a  href="'.$photo->url().'">';
		  $html .= '<img src="'.$photo->source('s').'" alt="'.$photo->title.' on Flickr">';
		  $html .= '</a>';
		  $html .= '<input type="checkbox" name="photos[]" id="photo_'.$photo->id.'" value="'.$photo->id.'@#@'.$photo->secret.'"/>';
		  $html .= '<label for="photo_'.$photo->id.'">'.$photo->title.'</label>';
		} else {
		  $html .= '<label for="photo_'.$photo->id.'">';
		  $html .= '<a  href="'.$photo->url().'">';
		  $html .= '<img src="'.$photo->source('s').'" alt="'.$photo->title.' on Flickr">';
		  $html .= '</a>';
		  $html .= '</label>';
		  $html .= '<input type="checkbox" name="photos[]" id="photo_'.$photo->id.'" value="'.$photo->id.'@#@'.$photo->secret.'"/>';
		}
		$html .= '</li>'."\n";
	  }
	  $html .= "</ul>\n";
	  $html .= '<br clear="both"/>';
	  $html .= '<button type="submit">'._T('spip:bouton_valider')."</button>\n";
	  $html .= '<input type="hidden" name="type" value="'.addslashes(_request('type')).'"/>'."\n";
	  $html .= '<input type="hidden" name="id" value="'.intval(_request('id')).'"/>'."\n";
	  
	  include_spip('inc/actions');
	  if(addslashes(_request('type')) == 'article') {
		echo generer_action_auteur('flickr_ajouter_documents',intval(_request('id')), generer_url_ecrire('articles','id_article='.intval(_request('id')),true),$html);
	  } else if(addslashes(_request('type')) == 'rubrique') {
		echo generer_action_auteur('flickr_ajouter_documents',intval(_request('id')), generer_url_ecrire('naviguer','id_rubrique='.intval(_request('id')),true),$html);
	  } else {
		echo generer_action_auteur('flickr_ajouter_documents',intval(_request('id')), generer_url_ecrire('breves_edit','id_breve='.intval(_request('id')),true),$html);
	  }
	  if($photos->pages > 1) {
		echo '<hr/><h3>'._T('fpipr:pages').':</h3>';	  
		for($i=1;$i <= $photos->pages;$i++) {
		  if($i != $page) {
			echo '<a href="'.generer_url_ecrire('flickr_choix_photos',"page=$i&type=".addslashes(_request('type'))."&id=".intval(_request('id'))."&sort=$sort".(_request('text_search')?"&text_search="._request('text_search'):'')).'">';
		  }
		  echo $i;
		  if($i != $page) {
			echo '</a>';
		  }
		  echo (($i == $photos->pages)?'':'&nbsp;|');
		  echo "\n";
		}
	  }
	  
	  echo '<hr/><h3>'._T('fpipr:recherche').':</h3>';
	  echo '<form id="recherche" method="get">';
	  echo '<input type="hidden" name="exec" value="'._request('exec').'"/>';
	  echo '<input type="hidden" name="type" value="'.addslashes(_request('type')).'"/>';
	  echo '<input type="hidden" name="id" value="'.intval(_request('id')).'"/>';
	  echo '<label for="text_search">'._T('fpipr:text_search').':</label>';
	  echo '<input type="text" name="text_search" id="text_search" value="'._request('text_search').'"/>';
	  echo '<label for="sort">'._T('fpipr:ordre').'</label>';
	  echo '<select name="sort" id="sort">';
	  echo '<option value="date-posted-asc"'.(($sort=="date-posted-asc")?' selected="true"':'').'>'._T('fpipr:date-posted-asc').'</option>';
	  echo '<option value="date-posted-desc"'.(($sort=="date-posted-desc")?' selected="true"':'').'>'._T('fpipr:date-posted-desc').'</option>';
	  echo '<option value="date-taken-asc"'.(($sort=="date-taken-asc")?' selected="true"':'').'>'._T('fpipr:date-posted-asc').'</option>';
	  echo '<option value="date-taken-desc"'.(($sort=="date-taken-desc")?' selected="true"':'').'>'._T('fpipr:date-taken-desc').'</option>';
	  echo '<option value="interestingness-desc"'.(($sort=="interestingness-desc")?' selected="true"':'').'>'._T('fpipr:interestingness-desc').'</option>';
	  echo '<option value="interestingness-asc"'.(($sort=="interestingness-asc")?' selected="true"':'').'>'._T('fpipr:interestingness-asc').'</option>';
	  echo '<option value="relevance"'.(($sort=="relevance")?' selected="true"':'').'>'._T('fpipr:relevance').'</option>';
	  echo '</select>';
	  echo '<button type="submit">'._T('fpipr:recherche').'</button>';
	  echo '</form>';
	} else {
	  include_spip('base/abstract_sql');
	  global $table_prefix;
	  spip_query("UPDATE ".$table_prefix."_auteurs SET flickr_nsid = '', flickr_token = '' WHERE id_auteur=$connect_id_auteur");
	  echo _T('fpipr:demande_authentification',array('url'=>generer_url_ecrire('auteurs_edit','id_auteur='.$connect_id_auteur)));
	}
  } else {
	echo _T('fpipr:demande_authentification',array('url'=>generer_url_ecrire('auteurs_edit','id_auteur='.$connect_id_auteur)));
  }
  echo '<hr/>';
  if(addslashes(_request('type')) == 'article') {
	echo '<a href="'.generer_url_ecrire('articles','id_article='.intval(_request('id'))).'">'._T('fpipr:retour').'</a>';
  } else if(addslashes(_request('type')) == 'rubrique') {
	echo '<a href="'.generer_url_ecrire('naviguer','id_rubrique='.intval(_request('id'))).'">'._T('fpipr:retour').'</a>';
  } else {
	echo '<a href="'.generer_url_ecrire('breves_edit','id_breve='.intval(_request('id'))).'">'._T('fpipr:retour').'</a>';
  }
  echo '
  </body>
</html>';
}

?>

<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__).'/../')));
define('_DIR_PLUGIN_FPIPR',(_DIR_PLUGINS.end($p)));


function exec_flickr_choix_sets() {
  global $connect_id_auteur;

  include_spip('inc/flickr_api');
  include_spip('base/abstract_sql');
  
  echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <title>'._T('fpipr:ajouter_sets').'</title>
	<link href="'._DIR_PLUGIN_FPIPR.'/styles.css" rel="stylesheet" type="text/css" />	
  </head>

  <body>';

  echo '<h1>'._T('fpipr:ajouter_sets').'</h1>';

  $from = array('spip_auteurs');
  $select = array('flickr_token','flickr_nsid');
  $where = array('id_auteur='.$connect_id_auteur);
  $row = spip_abstract_fetsel($select,$from,$where);
  if($row['flickr_nsid'] != '' && $row['flickr_token'] != '') {
	$check = flickr_auth_checkToken($row['flickr_token']);
	if($check) {
	  echo _T('fpipr:info_sets');
	  $page = intval(_request('page'))?intval(_request('page')):1;
	  $photosets = flickr_photosets_getList($row['flickr_nsid'],$row['flickr_token']);
	  
	  $html = '<input type="hidden" name="flickr_nsid" value="'.$row['flickr_nsid'].'">';
	  $html .= '<input type="hidden" name="flickr_token" value="'.$row['flickr_token'].'">';
	  $html .= "<ul>\n";
	  foreach($photosets as $set) {
		$html .= '<li>';
		if($set->title) {
		  $html .= '<a  href="'.$set->url().'">';
		  $html .= '<img src="'.$set->logo('s').'" alt="'.$set->title.' on Flickr">';
		  $html .= '</a>';
		  $html .= '<input type="checkbox" name="sets[]" id="set_'.$set->id.'" value="'.$set->id.'"/>';
		  $html .= '<label for="set_'.$set->id.'">'.$set->title.'</label>';
		} else {
		  $html .= '<label for="set_'.$set->id.'">';
		  $html .= '<a  href="'.$set->url().'">';
		  $html .= '<img src="'.$set->logo('s').'" alt="'.$set->title.' on Flickr">';
		  $html .= '</a>';
		  $html .= '</label>';
		  $html .= '<input type="checkbox" name="sets[]" id="set_'.$set->id.'" value="'.$set->id.'"/>';
		}
		$html .= '</li>'."\n";
	  }
	  $html .= "</ul>\n";
	  $html .= '<br clear="both">';
	  $html .= '<div align="right"><button type="submit">'._T('spip:bouton_valider')."</button></div>\n";
	  $html .= '<input type="hidden" name="type" value="'._request('type').'"/>'."\n";
	  $html .= '<input type="hidden" name="id" value="'.intval(_request('id')).'"/>'."\n";
	  $html .= '<input type="hidden" name="set" value="oui"/>'."\n";


	  include_spip('inc/actions');
	  if(_request('type') == 'article') {
		echo generer_action_auteur('flickr_ajouter_documents',intval(_request('id')), generer_url_ecrire('articles','id_article='.intval(_request('id')),true),$html);
	  } else if(_request('type') == 'rubrique') {
		echo generer_action_auteur('flickr_ajouter_documents',intval(_request('id')), generer_url_ecrire('naviguer','id_rubrique='.intval(_request('id')),true),$html);
	  } else {
		echo generer_action_auteur('flickr_ajouter_documents',intval(_request('id')), generer_url_ecrire('breves_edit','id_breve='.intval(_request('id')),true),$html);
	  }
	  
	} else {
	  include_spip('base/abstract_sql');
	  global $table_prefix;
	  spip_query("UPDATE ".$table_prefix."_auteurs SET flickr_nsid = '', flickr_token = '' WHERE id_auteur=$connect_id_auteur");
	  echo _T('fpipr:demande_authentification',array('url'=>generer_url_ecrire('auteur_infos','id_auteur='.$connect_id_auteur)));
	}
  } else {
	echo _T('fpipr:demande_authentification',array('url'=>generer_url_ecrire('auteur_infos','id_auteur='.$connect_id_auteur)));
  }
  echo '<br/>';
  if(_request('type') == 'article') {
	echo '<a href="'.generer_url_ecrire('articles','id_article='.intval(_request('id'))).'">'._T('fpipr:retour').'</a>';
  } else if(_request('type') == 'rubrique') {
	echo '<a href="'.generer_url_ecrire('naviguer','id_rubrique='.intval(_request('id'))).'">'._T('fpipr:retour').'</a>';
  } else {
	echo '<a href="'.generer_url_ecrire('breves_edit','id_breve='.intval(_request('id'))).'">'._T('fpipr:retour').'</a>';
  }
  echo '</body></html>';  
}

?>

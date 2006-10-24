<?php

function FpipR_affiche_milieu($flux) {
  if($flux['args']['exec'] == 'auteur_infos' && _request('initial') < 0) {
	global $table_prefix, $connect_id_auteur;

	include_spip('base/abstract_sql');

	//on crée la colonne pour stoquer les frobs
	$installe = unserialize(lire_meta('FpipR:installe'));
	if(!$installe) {
	  spip_query("ALTER TABLE `".$table_prefix."_auteurs` ADD (`flickr_token` TINYTEXT NULL, `flickr_nsid` TINYTEXT NULL);");
	  ecrire_meta('FpipR:installe',serialize(true)); //histoire de pas faire une recherche dans la base à chaque coup
	  ecrire_metas();
	}
	if($connect_id_auteur == $flux['args']['id_auteur']) {

	  include_spip('inc/presentation');

	  $html = '<div>&nbsp;</div>';
	  $html .= '<div style="margin-top: 14px;" class="cadre-r">
<div style="position: relative;">
<div style="position: absolute; top: -16px; left: 10px;">
<img src="'.find_in_path('fpipr.gif').'"/>
</div>
</div>
<div style="overflow: hidden;" class="cadre-padding">';
	  $html .= '<h3>'._T('fpipr:autorisation_titre').'</h3>';
	  include_spip('inc/flickr_api');


	  $from = array('spip_auteurs');
	  $select = array('flickr_token','flickr_nsid');
	  $where = array('id_auteur='.$flux['args']['id_auteur']);
	  $rez = spip_abstract_select($select,$from,$where);
	  $row = spip_abstract_fetch($rez);
	  $wrong = false;
	  if($row['flickr_nsid'] != '' && $row['flickr_token'] != '') {
		$check = flickr_auth_checkToken($row['flickr_token']);
		if($check) {
		  $html .= _T('fpipr:identifie_ok',array('user_id'=>'<a href="http://www.flickr.com/photos/'.$row['flickr_nsid'].'">'.$row['flickr_nsid'].'</a>'));

		  $html .= flickr_bookmarklet_info();
		} else {
		  include_spip('base/abstract_sql');
		  global $table_prefix;
		  spip_query("UPDATE ".$table_prefix."_auteurs SET flickr_nsid = '', flickr_token = '' WHERE id_auteur=$connect_id_auteur");
		  $wrong = true;
		}
	  } else $wrong = true;
	  if($wrong){
		$infos = flickr_authenticate_get_frob();
		$html .= '<ol><li>'.
		  _T('fpipr:identifie_etape1',array('url'=>$infos['url'])).
		  '</li>
<li>'.
		  _T('fpipr:identifie_etape2',array('form'=>generer_action_auteur('flickr_authenticate_end',$infos['frob'], generer_url_ecrire('auteurs_edit','id_auteur='.$connect_id_auteur,true),'<button type="submit">'._T('fpipr:terminer').'</button>'))).
		  '</li></ol>';
	  }
	  spip_abstract_free($rez);
	  $html .= fin_cadre_relief(true);
	  $flux['data'].=$html;
	}
	return $flux;
  }
}

function FpipR_affiche_gauche($flux) {
  global $connect_id_auteur;
  //Verifier les droits des auteurs
  if((($flux['args']['exec'] == 'articles') && ($GLOBALS['meta']["documents_articles"] != 'non')) || (($flux['args']['exec'] == 'naviguer')&& ($GLOBALS['meta']["documents_rubriques"] != 'non')) || (($flux['args']['exec'] == 'breves_edit')&& ($GLOBALS['meta']["documents_breves"] != 'non'))) {
	include_spip('base/abstract_sql');
	if($flux['args']['exec'] == 'articles') {
	  $type = 'article';
	  $id = intval(_request('id_article'));
	  $row = spip_abstract_fetsel(array('statut','id_rubrique'),array('spip_articles'),array("id_article=$id"));
	  $cnt = spip_abstract_fetsel(array('count(*) as cnt'),array('spip_auteurs_articles'),array("id_article=$id",'id_auteur='.$connect_id_auteur));
	  $acces = acces_rubrique($row['id_rubrique']) || acces_restreint_rubrique($row['id_rubrique']) || (($row['statut'] == 'prepa' || $row['statut'] == 'prop' || $row['statut'] == 'poubelle') && $cnt['cnt'] > 0);
	} else if($flux['args']['exec'] == 'naviguer') {
	  $type = 'rubrique';
	  $id = intval(_request('id_rubrique'));
	  $acces = acces_rubrique($id_rubrique);
	} /*else if($flux['args']['exec'] == 'breves_edit') {
	  $type = 'breve';
	  $id = _request('id_breve');
	  $row = spip_abstract_fetsel(array('statut','id_rubrique'),array('spip_breves'), array('id_breve='.$flux['args']['id_breve']));
	  $acces = true; //si on est arrivé là c'est qu'on a le droit de faire les modifs
	  }*/
	if($acces) {
	  $to_ret = '<div>&nbsp;</div>';
	  $to_ret .='<div style="z-index: 1;" class="bandeau_rubriques">
<div style="position: relative;">
<div style="position: absolute; top: -12px; left: 3px;">
<font size="1" face="Verdana,Arial,Sans,sans-serif">
<img alt="article-24" src="'.find_in_path('fpipr.gif').'"/>
</font>
</div>
<div class="verdana2" style="border-bottom: 1px solid rgb(68, 68, 68); padding: 3px 3px 3px 30px; background-color: white; color: black;">
<font size="1" face="Verdana,Arial,Sans,sans-serif">
   <b>'._T('fpipr:Flickr').'</b>
		</font>
		</div>
		</div>';
	  $to_ret .= '<div class="plan-articles">';

	  $to_ret .= '<a class="thickbox" href="'.generer_url_ecrire('flickr_choix_photos',"type=$type&id=$id",true).'">'._T('fpipr:ajouter_photos').'</a>';
	  $to_ret .= '<a class="thickbox" href="'.generer_url_ecrire('flickr_choix_sets',"type=$type&id=$id",true).'">'._T('fpipr:ajouter_sets').'</a>';
	  $to_ret .= '</div>';
	  $to_ret .= '</div></div>';

  $flux['data'] .= $to_ret;

	}
  }
  return $flux;
}

?>

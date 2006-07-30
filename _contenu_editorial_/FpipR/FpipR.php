<?php

function FpipR_affiche_milieu($flux) {
  if($flux['args']['exec'] == 'auteurs_edit') {
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

	  include('inc/presentation');

	  $html = debut_cadre_relief('',true);
	  $html .= '<h3>'._T('fpipr:autorisation_titre').'</h3>';

	  $from = array('spip_auteurs');
	  $select = array('flickr_token','flickr_nsid');
	  $where = array('id_auteur='.$flux['args']['id_auteur']);
	  $rez = spip_abstract_select($select,$from,$where);
	  $row = spip_abstract_fetch($rez);
	  if($row['flickr_nsid'] != '' && $row['flickr_token'] != '') {
		$html .= 'Vous êtes identifié avec l\'utilisateur: <a href="http://www.flickr.com/photos/'.$row['flickr_nsid'].'">'.$row['flickr_nsid'].'</a>';
	  } else {
		include_spip('inc/flickr_api');
		$infos = flickr_authenticate_get_frob();
		$html .= '<ol><li>Veillez d\'abord autoriser ce plugin sur flickr en cliquant <strong><a target="blank" href="'.$infos['url'].'">ici</a></strong><br/>
 Une nouvelle fen&ecirc;tre sera ouverte, suivez les instructions qui y sont fournis.</li>
<li>Une fois termin&eacute; vous devez revenir sur cette fen&ecirc;tre pour '.generer_action_auteur('flickr_authenticate_end',$infos['frob'], generer_url_ecrire('auteurs_edit','id_auteur='.$connect_id_auteur),'<button type="submit">'._T('fpipr:terminer').'</button> l\'authentification.</li></ol>');
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
	  $id = _request('id_article');
	  $row = spip_abstract_fetsel(array('statut','id_rubrique'),array('spip_articles'),array("id_article=$id"));
	  $cnt = spip_abstract_fetsel(array('count(*) as cnt'),array('spip_auteurs_articles'),array("id_article=$id",'id_auteur='.$connect_id_auteur));
	  $acces = acces_rubrique($row['id_rubrique']) || acces_restreint_rubrique($row['id_rubrique']) || (($row['statut'] == 'prepa' || $row['statut'] == 'prop' || $row['statut'] == 'poubelle') && $cnt['cnt'] > 0);
	} else if($flux['args']['exec'] == 'naviguer') {
	  $type = 'rubrique';
	  $id = _request('id_rubrique');
	  $acces = acces_rubrique($id_rubrique);
	} /*else if($flux['args']['exec'] == 'breves_edit') {
	  $type = 'breve';
	  $id = _request('id_breve');
	  $row = spip_abstract_fetsel(array('statut','id_rubrique'),array('spip_breves'), array('id_breve='.$flux['args']['id_breve']));
	  $acces = true; //si on est arrivé là c'est qu'on a le droit de faire les modifs
	  }*/
	if($acces) {
	  $to_ret = '<div>&nbsp;</div>';
	  $to_ret .= '<div class="bandeau_rubriques" style="z-index: 1;">';
	  $to_ret .= "<div style='position: relative;'>";
	  $to_ret .= "<div style='position: absolute; top: -12px; $spip_lang_left: 3px;'>
	<img src=''/></div>";
	  $to_ret .= "<div style='background-color: white; color: black; padding: 3px; padding-$spip_lang_left: 30px; border-bottom: 1px solid #444444;' class='verdana2'><b>"._T('Flickr')."</b></div>";
	  $to_ret .= "</div>";

	  $to_ret .= '<div class="plan-articles">';

	  $to_ret .= '<a class="thickbox" href="'.generer_url_ecrire('flickr_choix_photos',"type=$type&id=$id").'">ajouter une photo Flickr</a>';
	  $to_ret .= '<a class="thickbox" href="'.generer_url_ecrire('flickr_choix_sets',"type=$type&id=$id").'">ajouter un set de photos Flickr</a>';
	  $to_ret .= '</div>';
	  $to_ret .= '</div></div>';

  $flux['data'] .= $to_ret;

	}
  }
  return $flux;
}

?>

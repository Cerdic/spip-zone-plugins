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
	  $from = array('spip_auteurs');
	  $select = array('flickr_token','flickr_nsid');
	  $where = array('id_auteur='.$flux['args']['id_auteur']);
	  $rez = spip_abstract_select($select,$from,$where);
	  $row = spip_abstract_fetch($rez);
	  if($row['flickr_nsid'] != '' && $row['flickr_token'] != '') {
		$html = "Vous êtes identifié, bravo: ".$row['flickr_nsid'].':'.$row['flickr_token'];
	  } else {
		include_spip('inc/flickr_api');
		$infos = flickr_authenticate_get_frob();
		$html = 'Veillez d\'abord <a target="blank" href="'.$infos['url'].'">autoriser</a> ce plugin sur flickr,
<br/> une fois termin&eacute; vous pouvez revenir sur cette fenetre pour '.generer_action_auteur('flickr_authenticate_end',$infos['frob'], generer_url_ecrire('auteurs_edit','id_auteur='.$connect_id_auteur),'<button type="submit">'._T('terminer').'</button> l\'authentification');
	  }
	  spip_abstract_free($rez);
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
	if($acces)
	  $flux['data'] .= '<a class="thickbox" href="'.generer_url_ecrire('flickr_choix_photos',"type=$type&id=$id").'">ajouter une photo Flickr</a>';
  }
  return $flux;
}

?>

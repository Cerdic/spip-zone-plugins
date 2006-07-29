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
	return $flux;
  }
}

?>

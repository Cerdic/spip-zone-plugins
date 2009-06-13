<?php
/*
 * Plugin amis / gestion des amis
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

/**
 * Lister les amis d'un visiteur
 * La fonction liste les amis connus en base de donnee
 * et permet aux plugins d'ajouter des amis connus par ailleurs
 * via le pipeline social_lister_ami
 * Ce pipeline peut etre utilise par les extensions de gestion des
 * reseaux sociaux qui permettent de connaitre des liens d'amitie
 * si le visiteur est connecte au reseau, mais pas de les stocker en bdd.
 *
 * @param int $id_auteur
 * @return array(id=>true,...)
 */
function amis_lister($id_auteur){
	include_spip('base/abstract_sql');
	$liste = array();
	$res = sql_select("(id_auteur+id_ami-".intval($id_auteur).") as ami",'spip_amis as amis',
	"(amis.id_auteur=".intval($id_auteur)." OR amis.id_ami=".intval($id_auteur).") AND (amis.statut='publie')"
	);
	while ($row = sql_fetch($res)){
		$liste[$row['ami']] = true;
	}
	// checker pour les amis sociaux
	$liste = pipeline('social_lister_amis',array('args'=>array('id_auteur'=>$id_auteur),'data'=>$liste));
	return $liste;
}

?>